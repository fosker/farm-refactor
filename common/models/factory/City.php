<?php

namespace common\models\factory;

use Yii;

/**
 * This is the model class for table "factory_stock_for_cities".
 *
 * @property integer $city_id
 * @property integer $stock_id
 */
class City extends \yii\db\ActiveRecord
{
    public $cities = [];

    public static function tableName()
    {
        return 'factory_stock_for_cities';
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
            'cities' => 'Города'
        ];
    }

    public function getCity() {
        return $this->hasOne(\common\models\location\City::className(),['id'=>'city_id']);
    }
}
