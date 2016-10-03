<?php

namespace common\models\location;
use Yii;
use common\models\user\Pharmacist;
use common\models\location\City;
use common\models\company\Pharmacy;
/**
 * This is the model class for table "regions".
 *
 * @property integer $id
 * @property string $name
 */

class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название региона'
        ];
    }

    public function getCities()
    {
        return $this->hasMany(City::className(), ['region_id' => 'id'])->orderBy('name');
    }

    public function getCitiesCount()
    {
        $cityCount = Pharmacist::find()
            ->from([Pharmacist::tableName(), Pharmacy::tableName(), City::tableName(), Region::tableName()])
            ->select('count('.Pharmacist::tableName().'.id'.') as count, city_id')
            ->where(Pharmacist::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->andWhere(Pharmacy::tableName().'.city_id ='.City::tableName().'.id')
            ->andWhere(City::tableName().'.region_id ='.Region::tableName().'.id')
            ->andWhere([Region::tableName().'.id'=>$this->id])
            ->groupBy('city_id');
        return $this->hasMany(City::className(), ['region_id' => 'id'])
            ->leftJoin(['cityCount' => $cityCount], 'cityCount.city_id = id')
            ->orderBy(['cityCount.count' => SORT_DESC]);
    }

    public function getUserCount()
    {
        return Pharmacist::find()
            ->from([Pharmacist::tableName(),Pharmacy::tableName(),City::tableName(),static::tableName()])
            ->where(Pharmacist::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->andWhere(Pharmacy::tableName().'.city_id ='.City::tableName().'.id')
            ->andWhere(City::tableName().'.region_id ='.static::tableName().'.id')
            ->andWhere([static::tableName().'.id' => $this->id])
            ->count();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        City::deleteAll(['id'=>$this->id]);
    }

}
