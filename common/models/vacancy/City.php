<?php

namespace common\models\vacancy;

use Yii;

/**
 * This is the model class for table "vacancy_for_cities".
 *
 * @property integer $city_id
 * @property integer $vacancy_id
 */
class City extends \yii\db\ActiveRecord
{
    public $cities = [];

    public static function tableName()
    {
        return 'vacancy_for_cities';
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
