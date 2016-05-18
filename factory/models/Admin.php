<?php

namespace factory\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\Expression;
use yii\imagine\Image;
use common\models\Factory;

/**
 * This is the model class for table "factory_agents".
 *
 * @property integer $id
 * @property string $login
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $reset_token
 * @property integer $status
 * @property string $avatar
 * @property string $reset_token_expire
 * @property string $date_reg
 * @property integer $factory_id
 * @property string $sex
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{

    const STATUS_VERIFY = 0;
    const STATUS_ACTIVE = 1;

    const SEX_MALE = 'male';
    const SEX_FEMALE = 'female';

    public $rememberMe = null;
    private $_user = null;

    public $re_password;
    public $old_password;

    public $image;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'factory_agents';
    }

    public function scenarios() {
        return array_merge(
            parent::scenarios(),
            [
                'login' => ['login', 'password', 'rememberMe'],
                'update-password' => ['old_password', 'password', 're_password'],
                'reset-password' => ['reset_token','password','re_password'],
                'update-photo' => ['image'],
                'update' => ['name', 'email', 'sex']
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password', 're_password', 'old_password'], 'required'],
            [['login', 'email'], 'unique', 'on'=>'update'],
            ['email', 'email'],
            [['name', 'email'], 'string', 'max' => 255],
            [['rememberMe'], 'boolean'],
            [['password'],'check_password','on' => 'login'],
            [['re_password'], 'compare', 'compareAttribute'=>'password'],
            [['sex'], 'string', 'max' => 6],
            [['image'],'image',
                'extensions' => 'png, jpg, jpeg',
                'minWidth' => 150, 'maxWidth' => 4000,
                'minHeight' => 150, 'maxHeight' => 4000,
                'on' => 'update-photo'],
            [['old_password'], 'check_old_password'],
            [['reset_token'], 'check_reset_token'],
        ];
    }

    public function check_password($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильные данные.');
            }
        }
    }

    public function check_old_password($attribute)
    {
        if (!$this->hasErrors()) {
            $user = static::findOne(Yii::$app->user->id);
            if (!$user || !$user->validatePassword($this->old_password)) {
                $this->addError($attribute, 'Неправильный старый пароль.');
            }
        }
    }

    public function check_reset_token($attribute)
    {
        if (!$this->hasErrors()) {
            $user = static::findByPasswordResetToken($this->reset_token);
            if (!$user) {
                $this->addError($attribute, 'Код восстановления введен неправильно.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'name' => 'Имя Фамилия',
            'email' => 'Email',
            'password' => 'Пароль',
            'factory_id' => 'Производитель',
            'status' => 'Статус',
            'avatar' => 'Аватар',
            'sex' => 'Пол',
            'date_reg' => 'Дата регистрация',
            'rememberMe' => 'Запомнить меня',
            're_password' => 'Повтор пароля',
            'image' => 'Аватар',
            'old_password' => 'Старый пароль',
        ];
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => static::STATUS_ACTIVE]);
    }

    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login, 'status' => static::STATUS_ACTIVE]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => static::STATUS_ACTIVE]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'reset_token' => $token,
            'status' => static::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token))
            return false;

        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        if(!$user = static::findOne(['reset_token'=>$token]))
            return false;

        return strtotime($user->reset_token_expire) + $expire >= time();
    }

    public function login()
    {

        if ($this->validate()) {
            $user = $this->getUser();
            $this->status = $user ? $user->status : static::STATUS_VERIFY;
            if($this->status === static::STATUS_ACTIVE)
                return Yii::$app->user->login($user, $this->rememberMe? 3600*24*30 : 0);
        } else {
            return false;
        }
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = static::findByLogin($this->login);
        }

        return $this->_user;
    }

    public function generatePasswordResetToken()
    {
        $this->reset_token = Yii::$app->security->generateRandomString(8);
        $this->reset_token_expire = new Expression('NOW()');

        $this->save(false);
        Yii::$app->mailer->compose('repair-password', [
            'route'=>['/auth/reset-password', 'key'=>$this->reset_token],
        ])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Восстановление пароля')
            ->send();
    }

    public function removePasswordResetToken()
    {
        $this->reset_token = null;
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function getDefaultAvatar()
    {
        return Yii::getAlias('@uploads_view/avatars/'.Yii::$app->params['default_'.$this->sex.'_avatar']);
    }

    public function getAvatarPath()
    {
        return $this->avatar !== NULL ? Yii::getAlias('@uploads_view/avatars/'.$this->avatar) : $this->getDefaultAvatar();
    }

    public function saveImage()
    {
        if($this->image) {
            $this->deleteCurrentAvatar();
            $filename = Yii::$app->getSecurity()->generateRandomString() . time() . '.' . $this->image->extension;
            $newAvatarPath = Yii::getAlias('@uploads/avatars/'.$filename);
            $this->image->saveAs($newAvatarPath);
            $this->avatar = $filename;
            Image::thumbnail($newAvatarPath, 200, 200)
                ->save($newAvatarPath, ['quality' => 80]);
        }
    }

    public function deleteCurrentAvatar()
    {
        $avatar = Yii::getAlias('@uploads/avatars/'.$this->avatar);
        if(file_exists($avatar) && is_file($avatar)) {
            @unlink($avatar);
        }
    }

    public function getAuthKey()
    {

    }

    public function validateAuthKey($authKey)
    {

    }

    public static function findIdentityByAccessToken($token, $type = null)
    {

    }
}
