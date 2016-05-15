<?php

namespace common\models\user;

use Yii;
use common\models\company\Pharmacy;
use common\models\profile\Position;
use common\models\profile\Education;
use common\models\User;

/**
 * This is the model class for table "pharmacists".
 *
 * @property integer $id
 * @property string $sex
 * @property integer $education_id
 * @property integer $pharmacy_id
 * @property integer $position_id
 * @property string $mail_address
 */
class Pharmacist extends \yii\db\ActiveRecord
{

    const STATUS_VERIFY = 0;
    const STATUS_ACTIVE = 1;

    public $region_id;
    public $company_id;
    public $city_id;

    public static function tableName()
    {
        return 'pharmacists';
    }

    /**
     * @inheritdoc
     */


    public function rules()
    {
        return [
            [['education_id', 'region_id', 'city_id', 'company_id', 'pharmacy_id', 'sex'], 'required'],
            [['education_id', 'pharmacy_id', 'position_id', 'region_id', 'company_id', 'city_id'], 'integer'],
            [['sex'], 'string', 'max' => 6],
            [['mail_address'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sex' => 'Пол',
            'education_id' => 'Образование',
            'pharmacy_id' => 'Аптека',
            'position_id' => 'Должность',
            'region_id' => 'Регион',
            'city_id' => 'Город',
            'company_id' => 'Компания',
            'mail_address' => 'Почтовый адрес',
        ];
    }

    public function fields() {

        return ['id', 'sex','education','pharmacy','position','region', 'city', 'company', 'mail_address'];
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

    public function getCity()
    {
        return $this->pharmacy->city;
    }

    public function getRegion()
    {
        return $this->pharmacy->city->region;
    }

    public function getCompany()
    {
        return $this->pharmacy->company;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }

    public function verified()
    {
        $this->user->status = static::STATUS_ACTIVE;
        $this->user->save(false);
    }

    public function ban()
    {
        $this->user->status = static::STATUS_VERIFY;
        $this->user->save(false);
    }
}
