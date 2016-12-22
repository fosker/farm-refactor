<?php

namespace common\models;

use Yii;
use yii\imagine\Image;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;

use common\models\profile\Device;
use common\models\substance\Request;
use common\models\shop\Present;
use common\models\news\Comment as News_comment;
use common\models\news\View as News_view;
use common\models\video\Comment as Video_comment;
use common\models\survey\View as Survey_view;
use common\models\presentation\View as Presentation_view;
use common\models\presentation\Comment as Presentation_comment;
use common\models\stock\Reply;
use common\models\seminar\Entry as Seminar_entry;
use common\models\seminar\Comment as Seminar_comment;
use common\models\vacancy\Comment as Vacancy_comment;
use common\models\vacancy\Entry as Vacancy_entry;
use common\models\profile\Type;
use common\models\user\Pharmacist;
use common\models\user\Agent;
use common\models\Mailer;


/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $login
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $reset_token
 * @property string $reset_token_expire
 * @property integer $status
 * @property string $avatar
 * @property integer $points
 * @property string $date_reg
 * @property string $details
 * @property string $phone
 * @property integer $type_id
 * @property integer $inList
 * @property string $comment
 */
class User extends ActiveRecord implements IdentityInterface , RateLimitInterface
{
    public $image;
    public $re_password;
    public $old_password;
    public $device_id;

    const STATUS_VERIFY = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_NOTE_VERIFIED = 2;

    const IN_BLACK = 1;
    const IN_WHITE = 2;
    const IN_GRAY = 0;

    const SEX_MALE = 'male';
    const SEX_FEMALE = 'female';

    public function scenarios()
    {
        return array_merge(
            parent::scenarios(),
            [
                'update' => ['name', 'email', 'phone'],
                'black' => ['inList', 'comment'],
                'white' => ['inList', 'comment'],
                'join' => ['login', 'name', 'email', 'password', 're_password', 'details', 'type_id', 'phone', 'device_id'],
                'without-device' => ['login', 'name', 'email', 'password', 're_password', 'details', 'type_id', 'phone'],
                'update-password' => ['old_password', 'password', 're_password'],
                'reset-password' => ['reset_token', 'password', 're_password'],
                'update-photo' => ['image'],
            ]
        );
    }

    /**
     * @inheritdoc
     */

    public function logout()
    {
        $request = Yii::$app->request;
        $token = $request->get('access-token') ? $request->get('access-token') : explode(" ",$request->getHeaders()->get('Authorization'))[1];
        $device = Device::findOne(['access_token' => $token]);
        $device->user_id = "";
        $device->access_token = "";
        $device->save();
    }

    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'name', 'password', 're_password', 'reset_token', 'old_password', 'device_id', 'type_id', 'email'], 'required'],
            [['device_id'], 'exist', 'targetClass' => Device::className(), 'targetAttribute'=>'id'],
            [['login'], 'string', 'max' => 100],
            [['name','email'], 'string', 'max'=>255],
            [['email'],'email'],
            [['phone'], 'string', 'max' => 30],
            [['login'], 'unique'],
            [['re_password'], 'compare', 'compareAttribute' => 'password'],
            [['password', 'old_password', 're_password'], 'string', 'min' => 8,'max' => 100],
            [['details', 'comment'], 'string'],
            [['inList'], 'integer'],
            [['image'], 'file',
                'extensions' => 'png, jpg, jpeg',
                'checkExtensionByMimeType'=>false,
            ],
            [['old_password'], 'check_old_password'],
            [['reset_token'], 'check_reset_token'],
            ['email', 'unique'],
            [['login', 'password'], 'trim']
        ];
    }

    /**
     * Checks old password
     * @param $attribute
     */
    public function customUnique($attribute)
    {
        if (!$this->hasErrors()) {
            $user = static::find()->where(['email' => $this->email])
                ->andWhere(['in', 'status', [static::STATUS_VERIFY, static::STATUS_ACTIVE]])
                ->orWhere(['email' => $this->email, 'id' => $this->id])
                ->one();
            if ($user) {
                $this->addError($attribute, "Значение $this->email для «Почта» уже занято");
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

    /**
     * Checks old password
     * @param $attribute
     */
    public function check_reset_token($attribute)
    {
        if (!$this->hasErrors()) {
            $user = static::findByPasswordResetToken($this->reset_token);
            if (!$user) {
                $this->addError($attribute, 'Код восстановления введен неправильно.');
            }
        }
    }

    public function fields()
    {
        if($this->scenario == 'default')
            return [
                'name', 'login', 'email', 'points', 'phone', 'avatar'=>'avatarPath', 'inList'
            ];
        else
            return $this->scenarios()[$this->scenario];
    }

    public function extraFields()
    {
        if($this->scenario == 'default')
            return [
                'type_id',
                'pharmacist',
                'agent'
            ];
        else return [''];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'name' => 'Имя Фамилия',
            'email' => 'Почта',
            'password' => 'Пароль',
            're_password' => 'Повторите пароль',
            'old_password' => 'Старый пароль',
            'status' => 'Статус',
            'avatar' => 'Аватар',
            'avatarFile' => 'Аватар',
            'date_reg'=>'Зарегистрирован',
            'points' => 'Баллы',
            'details'=>'Дополнительные сведения',
            'phone' => 'Мобильный телефон',
            'type_id' => 'Тип пользователя',
            'inList' => 'В списке',
            'comment' => 'Комментарий',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => static::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['id' => Device::getUserByAccessToken($token), 'status'=>static::STATUS_ACTIVE]);
    }

    /**
     * Finds user by login
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => static::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
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

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token))
            return false;


        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        if(!$user = static::findOne(['reset_token'=>$token]))
            return false;

        return strtotime($user->reset_token_expire) + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function resetPassword()
    {
        $user = User::findByPasswordResetToken($this->reset_token);
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->save(false);
        return true;
    }

    public function generateAccessToken($device_id = null)
    {
        $device = Device::findOne(['id'=>$device_id == null ? $this->device_id : $device_id]);
        if(!$device)
            return false;
        $device->user_id = $this->id;
        $device->access_token = Yii::$app->security->generateRandomString(64);
        return $device->save(false);
    }

    public function getAccessTokenByDevice($device_id)
    {
        if($device = Device::findOne(['id'=>$device_id,'user_id'=>$this->id]))
            return $device->access_token;
        else return null;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->reset_token = Yii::$app->security->generateRandomString(8);
        $this->reset_token_expire = new Expression('NOW()');
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->reset_token = null;
        $this->reset_token_expire = null;
    }

    /**
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
        if (($request->isPut || $request->isDelete || $request->isPost)) {
            return [Yii::$app->params['maxRateLimit'], Yii::$app->params['perRateLimit']];
        }
        return [Yii::$app->params['maxGetRateLimit'], Yii::$app->params['perGetRateLimit']];
    }

    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {
        return [
            Yii::$app->cache->get($request->getPathInfo() . $request->getMethod() . '_remaining'),
            Yii::$app->cache->get($request->getPathInfo() . $request->getMethod() . '_ts')
        ];
    }

    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        Yii::$app->cache->set($request->getPathInfo() . $request->getMethod() . '_remaining', $allowance);
        Yii::$app->cache->set($request->getPathInfo() . $request->getMethod() . '_ts', $timestamp);
    }


    public function getDevices()
    {
        return $this->hasMany(Device::className(),['user_id'=>'id']);
    }

    public function getType()
    {
        return $this->hasOne(Type::className(), ['id' => 'type_id']);
    }

    public function getPharmacist()
    {
        return $this->hasOne(Pharmacist::className(), ['id' => 'id']);
    }

    public function getAgent()
    {
        return $this->hasOne(Agent::className(), ['id' => 'id']);
    }

    public function register()
    {
        $this->setPassword($this->password);
        $this->save(false);
        $this->generateAccessToken();
        Mailer::sendRegisterMail($this);
    }

    public function answerSurvey($survey)
    {
        $this->points += $survey->points;
        $this->save(false);
    }

    public function viewPresentation($presentation)
    {
        $this->points += $presentation->points;
        $this->save(false);
    }

    public function pay($amount)
    {
        if($amount > $this->points) return false;
        $this->points -= $amount;
        $this->save(false);
        return true;
    }

    public function getDefaultAvatar()
    {
        return $this->type_id == Type::TYPE_PHARMACIST ?
            Yii::getAlias('@uploads_view/avatars/'.Yii::$app->params['default_'.$this->pharmacist->sex.'_avatar']):
            Yii::getAlias('@uploads_view/avatars/'.'default_male_avatar.png');

    }

    public function getAvatarPath()
    {
        return $this->avatar !== NULL ? Yii::getAlias('@uploads_view/avatars/'.$this->avatar) : $this->getDefaultAvatar();
    }

    public function getStatuses()
    {
        $values = array(
            self::STATUS_NOTE_VERIFIED => 'не прошёл верификацию',
            self::STATUS_VERIFY => 'ожидает',
            self::STATUS_ACTIVE => 'активен',
        );
        if(isset($values[$this->status])) {
            return $values[$this->status];
        }
    }

    public function getLists()
    {
        $values = array(
            self::IN_WHITE => 'в белом',
            self::IN_BLACK => 'в черном',
            self::IN_GRAY => 'в сером',
        );
        if(isset($values[$this->inList])) {
            return $values[$this->inList];
        }
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

    public function verified()
    {
        $this->status = static::STATUS_ACTIVE;
        $this->save(false);
        Mailer::sendVerificationMailToUser($this, static::STATUS_ACTIVE);
    }

    public function ban()
    {
        $this->status = static::STATUS_VERIFY;
        $this->save(false);
    }

    public function toGray()
    {
        $this->inList = static::IN_GRAY;
        $this->comment = "";
        $this->save(false);
    }


    public function toWhite()
    {
        $this->inList = static::IN_WHITE;
        $this->save(false);
    }


    public function toBlack()
    {
        $this->inList = static::IN_BLACK;
        $this->save(false);
    }

    public function notVerify()
    {
        $this->status = static::STATUS_NOTE_VERIFIED;
        $this->save(false);
        Mailer::sendVerificationMailToUser($this, static::STATUS_VERIFY);
    }

    public function getPresentationViews()
    {
        return $this->hasMany(Presentation_view::className(), ['user_id' => 'id']);
    }

    public function getSurveyViews()
    {
        return $this->hasMany(Survey_view::className(), ['user_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach($this->devices as $device)
            $device->delete();
        Request::deleteAll(['user_id'=>$this->id]);
        Present::deleteAll(['user_id'=>$this->id]);
        News_comment::deleteAll(['user_id'=>$this->id]);
        News_view::deleteAll(['user_id'=>$this->id]);
        Video_comment::deleteAll(['user_id'=>$this->id]);
        Survey_view::deleteAll(['user_id'=>$this->id]);
        Presentation_view::deleteAll(['user_id'=>$this->id]);
        Presentation_comment::deleteAll(['user_id'=>$this->id]);
        Reply::deleteAll(['user_id'=>$this->id]);
        Seminar_entry::deleteAll(['user_id'=>$this->id]);
        Seminar_comment::deleteAll(['user_id'=>$this->id]);
        Vacancy_comment::deleteAll(['user_id'=>$this->id]);
        Vacancy_entry::deleteAll(['user_id'=>$this->id]);
    }

}
