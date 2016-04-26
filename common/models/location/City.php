<?php

namespace common\models\location;
use yii\helpers\ArrayHelper;
use common\models\banner\City as Banner_city;
use common\models\factory\City as Factory_city;
use common\models\presentation\City as Presentation_city;
use common\models\seminar\City as Seminar_city;
use common\models\shop\City as Item_city;
use common\models\survey\City as Survey_city;
use common\models\agency\Pharmacy;

use Yii;

/**
 * This is the model class for table "region_cities".
 *
 * @property integer $id
 * @property string $name
 * @property integer $region_id
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region_cities';
    }


    public function fields()
    {
        return [
            'id', 'name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'region_id'], 'integer'],
            [['region_id', 'name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название города',
            'region_id' => 'Регион'
        ];
    }

    public function getRegion() {
        return $this->hasOne(Region::className(),['id'=>'region_id']);
    }

    public function getPharmacies() {
        return $this->hasMany(Pharmacy::className(), ['city_id' => 'id']);
    }

    public static function getCityList($region_id)
    {
        return City::find()->select('id, name')
            ->where(['region_id'=>$region_id])
            ->asArray()
            ->all();
    }


    public function afterDelete()
    {
        parent::afterDelete();
        Banner_city::deleteAll(['city_id' => $this->id]);
        Factory_city::deleteAll(['city_id' => $this->id]);
        Presentation_city::deleteAll(['city_id' => $this->id]);
        Seminar_city::deleteAll(['city_id' => $this->id]);
        Item_city::deleteAll(['city_id' => $this->id]);
        Survey_city::deleteAll(['city_id' => $this->id]);
        foreach($this->pharmacies as $pharmacy)
            $pharmacy->delete();
    }
}
