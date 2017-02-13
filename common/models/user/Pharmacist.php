<?php

namespace common\models\user;

use Yii;
use common\models\company\Pharmacy;
use common\models\profile\Position;
use common\models\profile\Education;
use common\models\User;
use common\models\profile\Device;
use yii\helpers\Html;

/**
 * This is the model class for table "pharmacists".
 *
 * @property integer $id
 * @property string $sex
 * @property integer $education_id
 * @property integer $pharmacy_id
 * @property integer $position_id
 * @property string $mail_address
 * @property string $date_birth
 * @property string $pharmacy_phone_number
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

    public function scenarios()
    {
        return array_merge(
            parent::scenarios(),
            [
                'update' => ['pharmacy_id', 'education_id', 'sex', 'position_id', 'mail_address', 'date_birth'],
            ]
        );
    }

    public function rules()
    {
        return [
            [['education_id', 'region_id', 'sex', 'pharmacy_id', 'company_id', 'city_id'], 'required'],
            [['education_id', 'pharmacy_id', 'position_id', 'region_id', 'company_id', 'city_id'], 'integer'],
            [['sex', 'pharmacy_phone_number'], 'string', 'max' => 6],
            [['mail_address'], 'string', 'max' => 100],
            [['date_birth'], 'required'],
            [['date_birth'], 'string']
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
            'company_id' => 'Организация',
            'mail_address' => 'Почтовый адрес',
            'date_birth' => 'Дата рождения',
            'pharmacy_phone_number' => 'Телефонный номер аптеки'
        ];
    }

    public function fields() {

        return ['id', 'sex','education','pharmacy','position','region', 'city', 'company', 'mail_address',
        'date_birth', 'pharmacy_phone_number'];
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

    public function getDeviceLabels()
    {
        $string = '';
        foreach ($this->user->devices as $device) {
            if ($device->version) {
                $string .= Html::tag('p', ($device->type == Device::TYPE_ANDROID ? 'Android' : 'Ios') . ': ' . $device->version);
            }
        }

        return $string;
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

    public function afterDelete()
    {
        parent::afterDelete();
        $this->user->delete();
    }
}
