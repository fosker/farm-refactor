<?php

namespace common\models\news;


class Pharmacy extends \yii\db\ActiveRecord
{
    public $pharmacies = [];

    public static function tableName()
    {
        return 'news_for_pharmacies';
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
            'pharmacies' => 'Аптеки'

        ];
    }

    public function getPharmacy() {
        return $this->hasOne(\common\models\agency\Pharmacy::className(),['id'=>'pharmacy_id']);
    }
}