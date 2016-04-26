<?php

namespace common\models;

use common\models\profile\SetNotification;
use Yii;
use yii\imagine\Image;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;

use common\models\profile\Device;
use common\models\agency\Firm;
use common\models\location\City;
use common\models\agency\Pharmacy;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\profile\Position;
use backend\models\Param;
use common\models\factory\Reply;
use common\models\block\Comment as Block_comment;
use common\models\block\Mark as Block_mark;
use common\models\presentation\Comment as Presentation_comment;
use common\models\presentation\View as Presentation_view;
use common\models\seminar\Comment as Seminar_comment;
use common\models\seminar\Entry as Seminar_entry;
use common\models\substance\Request;
use common\models\survey\View as Survey_view;
use common\models\shop\Desire;
use common\models\shop\Present;
use common\models\profile\UpdateRequest;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $login
 * @property string $name
 * @property integer $sex
 * @property string $email
 * @property string $password
 * @property integer $education_id
 * @property integer $pharmacy_id
 * @property integer $position_id
 * @property string $reset_token
 * @property string $reset_token_expire
 * @property integer $status
 * @property string $avatar
 * @property integer $points
 * @property string $date_reg
 * @property string $details
 * @property string $phone
 * @property string @mail_address
 */
class User extends ActiveRecord implements IdentityInterface , RateLimitInterface
{

    public $image;
    public $re_password;
    public $old_password;
    public $region_id;
    public $firm_id;
    public $city_id;
    public $device_id;

    const STATUS_VERIFY = 0;
    const STATUS_ACTIVE = 1;

    const SEX_MALE = 'male';
    const SEX_FEMALE = 'female';

    public function scenarios() {
        return array_merge(
            parent::scenarios(),
            [
                'join'=>['login', 'name', 'email', 'password', 're_password','sex','education_id', 'region_id', 'city_id', 'firm_id', 'pharmacy_id', 'details','device_id'],
                'update-password'=>['old_password','password','re_password'],
                'reset-password'=>['reset_token','password','re_password'],
                'update-photo'=>['image'],
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
            [['login', 'name', 'password', 're_password', 'region_id', 'education_id', 'sex', 'reset_token', 'old_password', 'device_id'], 'required'],
            [['device_id'],'exist','targetClass'=>Device::className(),'targetAttribute'=>'id'],
            [['login'], 'string', 'max' => 100],
            [['name','email', 'mail_address'], 'string', 'max'=>255],
            [['sex'], 'string', 'max' => 6],
            [['email'],'email'],
            [['phone'], 'string', 'max' => 30],
            [['login','email'],'unique'],
            [['re_password'], 'compare', 'compareAttribute'=>'password'],
            [['password','old_password', 're_password'], 'string', 'min' => 8,'max' => 100],
            [['education_id'], 'exist', 'targetClass'=>Education::className(), 'targetAttribute'=>'id'],
            [['position_id'], 'exist', 'targetClass'=>Position::className(), 'targetAttribute'=>'id'],
            [['pharmacy_id'], 'exist', 'targetClass'=>Pharmacy::className(), 'targetAttribute'=>'id'],
            [['region_id'], 'exist', 'targetClass'=>Region::className(), 'targetAttribute'=>'id'],
            [['city_id'], 'exist', 'targetClass'=>City::className(), 'targetAttribute'=>'id'],
            [['firm_id'], 'exist', 'targetClass'=>Firm::className(), 'targetAttribute'=>'id'],
            [['details'],'string'],
            [['image'],'image',
                'extensions' => 'png, jpg, jpeg',
                'minWidth' => 150, 'maxWidth' => 4000,
                'minHeight' => 150, 'maxHeight' => 4000,
            ],
            [['old_password'], 'check_old_password'],
            [['reset_token'], 'check_reset_token'],
        ];
    }

    /**
     * Checks old password
     * @param $attribute
     */
    public function check_old_password($attribute) {
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
    public function check_reset_token($attribute) {
        if (!$this->hasErrors()) {
            $user = static::findByPasswordResetToken($this->reset_token);
            if (!$user) {
                $this->addError($attribute, 'Код восстановления введен неправильно.');
            }
        }
    }

    public function fields() {
        if($this->scenario == 'default')
            return [
                'name','login','email','sex','points','phone','mail_address','avatar'=>'avatarPath'
            ];
        else
            return $this->scenarios()[$this->scenario];
    }

    public function extraFields() {
        if($this->scenario == 'default')
        return [
            'pharmacy','education','city','region','firm','position','notifications'
        ];
        else return [''];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'name' => 'Имя Фамилия',
            'sex' => 'Пол',
            'email' => 'Почта',
            'password' => 'Пароль',
            're_password' => 'Повторите пароль',
            'old_password' => 'Старый пароль',
            'education_id' => 'Образование',
            'pharmacy_id' => 'Аптека',
            'position_id' => 'Должность',
            'firm_id' => 'Фирма',
            'city_id' => 'Город',
            'region_id' => 'Область',
            'status' => 'Статус',
            'avatar' => 'Аватар',
            'avatarFile' => 'Аватар',
            'date_reg'=>'Зарегистрирован',
            'points' => 'Баллы',
            'details'=>'Дополнительные сведения',
            'phone' => 'Мобильный телефон',
            'mail_address' => 'Почтовый адрес'
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
        return static::findOne(['login' => $login, 'status' => static::STATUS_ACTIVE]);
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

    public function resetPassword() {
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

    public function getAccessTokenByDevice($device_id) {
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

    public function getPharmacy() {
        return $this->hasOne(Pharmacy::className(), ['id' => 'pharmacy_id']);
    }

    public function getPosition() {
        return $this->hasOne(Position::className(), ['id' => 'position_id']);
    }

    public function getEducation() {
        return $this->hasOne(Education::className(), ['id' => 'education_id']);
    }

    public function getDevices() {
        return $this->hasMany(Device::className(),['user_id'=>'id']);
    }

    public function getCity() {
        return $this->pharmacy->city;
    }

    public function getRegion() {
        return $this->pharmacy->city->region;
    }

    public function getFirm() {
        return $this->pharmacy->firm;
    }

    public function register()
    {
        $this->setPassword($this->password);
        $this->sendInfoMail();
        $this->save(false);
        SetNotification::registerNewUser($this->id);
        $this->generateAccessToken();

    }

    public function answerSurvey($survey) {
        $this->points += $survey->points;
        $this->save(false);
    }

    public function viewPresentation($presentation) {
        $this->points += $presentation->points;
        $this->save(false);
    }

    public function pay($amount) {
        if($amount > $this->points) return false;
        $this->points -= $amount;
        $this->save(false);
        return true;
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

    public function verified()
    {
        $this->status = static::STATUS_ACTIVE;
        $this->save(false);
    }

    public function ban()
    {
        $this->status = static::STATUS_VERIFY;
        $this->save(false);
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
        Block_comment::deleteAll(['user_id'=>$this->id]);
        Reply::deleteAll(['user_id'=>$this->id]);
        Block_mark::deleteAll(['user_id'=>$this->id]);
        Presentation_comment::deleteAll(['user_id'=>$this->id]);
        foreach($this->presentationViews as $view)
            $view->delete();
        Seminar_comment::deleteAll(['user_id'=>$this->id]);
        Seminar_entry::deleteAll(['user_id'=>$this->id]);
        Request::deleteAll(['user_id'=>$this->id]);
        foreach($this->surveyViews as $view)
            $view->delete();
        Desire::deleteAll(['user_id'=>$this->id]);
        Device::deleteAll(['user_id'=>$this->id]);
        Present::deleteAll(['user_id'=>$this->id]);
        UpdateRequest::deleteAll(['user_id'=>$this->id]);
        parent::afterDelete();
    }

    private function sendInfoMail()
    {
        Yii::$app->mailer->compose('@common/mail/user-register', [
            'name'=>$this->name,
            'login'=>$this->login,
            'email'=>$this->email,
            'pharmacy'=>$this->pharmacy->name,
            'education'=>$this->education->name,
        ])
            ->setFrom(Param::getParam('email'))
            ->setTo("pharmbonus@gmail.com")
            ->setSubject('Новый пользователь!')
            ->send();
    }

}
