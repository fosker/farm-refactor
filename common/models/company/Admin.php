<?php

namespace common\models\company;

use Yii;
use common\models\Company;

/**
 * This is the model class for table "company_admins".
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
 * @property string $company_id
 * @property string $sex
 */
class Admin extends \yii\db\ActiveRecord
{

    public $re_password;

    const STATUS_VERIFY = 0;
    const STATUS_ACTIVE = 1;

    const SEX_MALE = 'male';
    const SEX_FEMALE = 'female';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_admins';
    }

    public function scenarios() {
        return array_merge(
            parent::scenarios(),
            [
                'join'=>['login', 'name', 'email', 'password', 're_password', 'company_id', 'sex'],
            ]
        );
    }

    public function fields() {
        return [
            'name','login','avatar'=>'avatarPath'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'name', 'password', 'company_id', 're_password'], 'required'],
            ['email', 'email'],
            [['login', 'email'],'unique'],
            [['re_password'], 'compare', 'compareAttribute'=>'password'],
            [['login'], 'string', 'max' => 100],
            [['name', 'email'], 'string', 'max' => 255],
            [['password'], 'string', 'min' => 8, 'max' => 100],
            [['reset_token'], 'string', 'max' => 8],
            ['sex', 'string']
        ];
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function register()
    {
        $this->setPassword($this->password);
        $this->save(false);
    }

    public function getDefaultAvatar()
    {
        return Yii::getAlias('@uploads_view/avatars/'.Yii::$app->params['default_'.$this->sex.'_avatar']);
    }

    public function getAvatarPath()
    {
        return $this->avatar !== NULL ? Yii::getAlias('@uploads_view/avatars/'.$this->avatar) : $this->getDefaultAvatar();
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
            'company_id' => 'Компания',
            're_password' => 'Повтор пароля',
            'status' => 'Статус',
            'avatar' => 'Аватар',
            'sex' => 'Пол',
            'date_reg' => 'Дата регистрация'
        ];
    }

    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

}