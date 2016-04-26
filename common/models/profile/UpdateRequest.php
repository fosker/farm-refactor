<?php

namespace common\models\profile;

use common\models\agency\Firm;
use common\models\agency\Pharmacy;
use common\models\location\City;
use common\models\location\Region;
use common\models\User;
use Yii;

/**
 * This is the model class for table "user_update_requests".
 *
 * @property integer $user_id
 * @property string $name
 * @property string $sex
 * @property string $email
 * @property integer $education_id
 * @property integer $pharmacy_id
 * @property integer $position_id
 * @property string $phone
 * @property string $mail_address
 * @property string $details
 * @property string date_add
 */
class UpdateRequest extends \yii\db\ActiveRecord
{

    public $region_id;
    public $firm_id;
    public $city_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_update_requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'region_id', 'education_id', 'sex'], 'required'],
            [['name','email', 'mail_address'], 'string', 'max'=>255],
            [['sex'], 'string', 'max' => 6],
            [['phone'], 'string', 'max' => 30],
            [['email'],'email'],
            [['email'],'unique'],
            [['education_id'], 'exist', 'targetClass'=>Education::className(), 'targetAttribute'=>'id'],
            [['position_id'], 'exist', 'targetClass'=>Position::className(), 'targetAttribute'=>'id'],
            [['pharmacy_id'], 'exist', 'targetClass'=>Pharmacy::className(), 'targetAttribute'=>'id'],
            [['region_id'], 'exist', 'targetClass'=>Region::className(), 'targetAttribute'=>'id'],
            [['city_id'], 'exist', 'targetClass'=>City::className(), 'targetAttribute'=>'id'],
            [['firm_id'], 'exist', 'targetClass'=>Firm::className(), 'targetAttribute'=>'id'],
            [['details'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ID',
            'name' => 'Имя Фамилия',
            'sex' => 'Пол',
            'email' => 'Email',
            'education_id' => 'Образование',
            'pharmacy_id' => 'Аптека',
            'position_id' => 'Должность',
            'firm_id' => 'Фирма',
            'city_id' => 'Город',
            'region_id' => 'Область',
            'details'=>'Дополнительные сведения',
            'date_add'=>'Дата запроса',
            'phone' => 'Мобильный телефон',
            'mail_address' => 'Почтовый адрес'
        ];
    }

    public function loadCurrentAttributes($user)
    {
        $this->attributes = $user->attributes;
        $this->user_id = $user->id;
        $this->city_id = $user->pharmacy->city_id;
        $this->firm_id = $user->pharmacy->firm_id;
        $this->region_id = $user->pharmacy->city->region_id;
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

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function fields() {
        return [
            'user_id', 'name', 'sex', 'email', 'education_id', 'pharmacy_id', 'position_id',
            'firm_id', 'city_id', 'region_id', 'details', 'phone', 'mail_address'
        ];
    }
}
