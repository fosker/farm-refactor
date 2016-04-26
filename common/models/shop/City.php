<?php

namespace common\models\shop;

use Yii;

/**
 * This is the model class for table "shop_item_for_cities".
 *
 * @property integer $item_id
 * @property integer $city_id
 */
class City extends \yii\db\ActiveRecord
{
    public $cities = [];

    public static function tableName()
    {
        return 'shop_item_for_cities';
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
