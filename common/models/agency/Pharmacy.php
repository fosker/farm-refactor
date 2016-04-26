<?php

namespace common\models\agency;

use Yii;
use common\models\location\City;
use common\models\banner\Pharmacy as Banner_pharmacy;
use common\models\factory\Pharmacy as Factory_pharmacy;
use common\models\presentation\Pharmacy as Presentation_pharmacy;
use common\models\seminar\Pharmacy as Seminar_pharmacy;
use common\models\shop\Pharmacy as Item_pharmacy;
use common\models\survey\Pharmacy as Survey_pharmacy;
use common\models\User;

/**
 * This is the model class for table "firm_pharmacies".
 *
 * @property integer $id
 * @property string $address
 * @property string $name
 * @property integer $firm_id
 * @property integer $city_id
 */
class Pharmacy extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'firm_pharmacies';
    }

    public function rules()
    {
        return [
            [['firm_id', 'city_id', 'name'], 'required'],
            [['address'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название аптеки',
            'address' => 'Адрес',
            'firm_id' => 'Фирма',
            'city_id' => 'Город',
        ];
    }

    public function getFirm()
    {
        return $this->hasOne(Firm::className(), ['id' => 'firm_id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getUsers() {
        return $this->hasMany(User::className(), ['pharmacy_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Banner_pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Factory_pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Presentation_pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Seminar_pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Item_pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        Survey_pharmacy::deleteAll(['pharmacy_id' => $this->id]);
        foreach($this->users as $user)
            $user->delete();
    }

    public static function getPharmacyList($firm_id, $city_id)
    {
        return Pharmacy::find()
            ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
            ->where(['firm_id'=>$firm_id])
            ->andWhere(['city_id' => $city_id])
            ->asArray()
            ->all();
    }
}
