<?php

namespace common\models\profile;

use Yii;

use common\models\User;
use common\models\location\City;
use common\models\profile\Education;
use common\models\profile\Position;
use common\models\company\Pharmacy;
use common\models\Company;
use common\models\location\Region;


/**
 * This is the model class for table "pharmacist_update_requests".
 *
 * @property integer $pharmacist_id
 * @property string $name
 * @property string $email
 * @property integer $sex
 * @property integer $education_id
 * @property string $pharmacy_id
 * @property integer $position_id
 * @property string $phone
 * @property string $mail_address
 * @property string $details
 * @property string date_add
 */

class PharmacistUpdateRequest extends \yii\db\ActiveRecord
{

    public $region_id;
    public $company_id;
    public $city_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pharmacist_update_requests';
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
            [['email'],'unique', 'targetClass'=>User::className(), 'targetAttribute'=>'email'],
            [['education_id'], 'exist', 'targetClass'=>Education::className(), 'targetAttribute'=>'id'],
            [['position_id'], 'exist', 'targetClass'=>Position::className(), 'targetAttribute'=>'id'],
            [['pharmacy_id'], 'exist', 'targetClass'=>Pharmacy::className(), 'targetAttribute'=>'id'],
            [['region_id'], 'exist', 'targetClass'=>Region::className(), 'targetAttribute'=>'id'],
            [['city_id'], 'exist', 'targetClass'=>City::className(), 'targetAttribute'=>'id'],
            [['company_id'], 'exist', 'targetClass'=>Company::className(), 'targetAttribute'=>'id'],
            [['details'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pharmacist_id' => 'ID',
            'name' => 'Имя Фамилия',
            'sex' => 'Пол',
            'email' => 'Email',
            'education_id' => 'Образование',
            'pharmacy_id' => 'Аптека',
            'position_id' => 'Должность',
            'company_id' => 'Компания',
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
        $this->pharmacist_id = $user->id;
        $this->attributes = $user->pharmacist->attributes;
        $this->city_id = $user->pharmacist->pharmacy->city_id;
        $this->company_id = $user->pharmacist->company->id;
        $this->region_id = $user->pharmacist->region->id;
    }

    public function getPharmacy()
    {
        return $this->hasOne(Pharmacy::className(), ['id' => 'pharmacy_id']);
    }

    public function getPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'position_id']);
    }

    public function getEducation()
    {
        return $this->hasOne(Education::className(), ['id' => 'education_id']);
    }

    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    public function getRegion()
    {
        return $this->pharmacy->city->region;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function fields()
    {
        return [
            'pharmacist_id', 'name', 'sex', 'email', 'education_id', 'pharmacy_id', 'position_id',
            'company_id', 'city_id', 'region_id', 'details', 'phone', 'mail_address'
        ];
    }
}
