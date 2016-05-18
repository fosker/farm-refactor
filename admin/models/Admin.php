<?php
namespace backend\models;

use Yii;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

use backend\models\admin\Right;
use backend\models\admin\HasRight;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $name
 * @property string $password
 * @property string $reset_token
 * @property string $auth_key
 * @property string $email
 *
 */

class Admin extends ActiveRecord  implements IdentityInterface
{

    public $rememberMe = null;
    public $re_password;

    private $_user = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'administrators';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'login' => ['email', 'password', 'rememberMe'],
            'reset_password'=>['password','re_password'],
            'create' => ['email','name'],
            'update' => ['email','name'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password', 'name', 'old_password'], 'required'],
            [['email'], 'email'],
            [['email'],'unique', 'on'=>'create'],
            [['name'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 60, 'min'=>8],
            [['password'],'checkPassword','on' => 'login'],
            [['rememberMe'], 'boolean'],
            [['re_password'], 'compare', 'compareAttribute'=>'password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name'=>'Имя',
            'password' => 'Пароль',
            'rememberMe'=>'Запомнить меня',
			're_password'=>'Повторите пароль',
        ];
    }

    /** INCLUDE USER LOGIN VALIDATION FUNCTIONS**/
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($insert) {
                $this->setPassword(Yii::$app->getSecurity()->generateRandomString());
                $this->generateAuthKey();
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $attr) {
        parent::afterSave($insert, $attr);
        if($insert) {
            foreach(Right::find()->asArray()->all() as $right) {
                $hasRight = new HasRight();
                $hasRight->admin_id = $this->id;
                $hasRight->right_id = $right['id'];
                $hasRight->value = 0;
                $hasRight->save(false);
            }
        }     
    }

    public function afterDelete() {
        parent::afterDelete();
        HasRight::deleteAll(['admin_id'=>$this->id]);
    }

    /**
     * Log In user if validation is ok
     *
     * @return boolean
    */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->admin->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }


    /**
     * @return Admin|null
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = static::findByEmail($this->email);
        }

        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
          return static::findOne(['auth_key' => $token]);
    }

    /**
     * @param $attribute
     */
    public function checkPassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильные данные.');
            }
        }
    }

    public static function findByEmail($username)
    {
        return static::findOne(['email' => $username]);
    }


    public static function findByPasswordResetToken($token)
    {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'reset_token' => $token
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
    *  Returns an object Right associated with an admin
    *
    *   @return Right
    */
    public function getRights()
    {
        return $this->hasMany(HasRight::className(), ['admin_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
        $this->save(false);
        Yii::$app->mailer->compose('@common/mail/repair-password', [
            'route'=>['/auth/reset-password', 'key'=>$this->reset_token],
        ])
            ->setFrom(Param::getParam('email'))
            ->setTo($this->email)
            ->setSubject('Восстановление пароля')
            ->send();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->reset_token = null;
    }

    public function can($action) {
        if(Right::HasAdmin(Yii::$app->admin->id,$action->controller->id)) return true;
        else return false;
    }

    public static function showLists($id)
    {
        $list = ['city', 'company', 'factory', 'factories/products',
            'pharmacy', 'education', 'type', 'position',
            'substance', 'substances/request', 'presents/vendor'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item) == true) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showVideo($id)
    {
        $list = ['video', 'videos/comment'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showUser($id)
    {
        $list = ['user/agents', 'user/pharmacists', 'users/push-users',
            'users/present', 'users/push-groups',
            'users/agent/update-request', 'users/pharmacist/update-request', 'users/factory-admin'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showSurvey($id)
    {
        $list = ['survey', 'surveys/answer'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showPresentation($id)
    {
        $list = ['presentation', 'presentations/comment', 'presentations/answer'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showStock($id)
    {
        $list = ['stock', 'stocks/answer'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showSeminar($id)
    {
        $list = ['seminar', 'seminars/sign', 'seminars/comment'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showMain($id)
    {
        $list = ['main', 'admin', 'contact-form'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showNews($id)
    {
        $list = ['news', 'newss/comment'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showVacancy($id)
    {
        $list = ['vacancy', 'vacancies/comment', 'vacancies/sign'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

    public static function showContent($id)
    {
        $list = ['survey', 'presentation', 'seminar', 'present', 'news', 'video', 'banner'];
        foreach($list as $item) {
            if (Right::HasAdmin($id, $item)) {
                return true;
                break;
            }
        }

        return false;
    }

}
