<?php

namespace common\models\location;

use common\models\company\Pharmacy;

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

    public function getRegion()
    {
        return $this->hasOne(Region::className(),['id'=>'region_id']);
    }

    public function getPharmacies()
    {
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
        foreach($this->pharmacies as $pharmacy)
            $pharmacy->delete();
    }
}
