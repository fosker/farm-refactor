<?php
namespace common\models\news;


class City extends \yii\db\ActiveRecord
{
    public $cities = [];

    public static function tableName()
    {
        return 'news_for_cities';
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