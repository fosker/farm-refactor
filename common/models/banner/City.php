<?php

namespace common\models\banner;

use Yii;

/**
 * This is the model class for table "banner_for_cities".
 *
 * @property integer $banner_id
 * @property integer $city_id
 */
class City extends \yii\db\ActiveRecord
{
    public $cities = [];

    public static function tableName()
    {
        return 'banner_for_cities';
    }

    public function rules()
    {
        return [

        ];
    }

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
