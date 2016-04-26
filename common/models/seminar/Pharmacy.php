<?php

namespace common\models\seminar;

use Yii;

/**
 * This is the model class for table "seminar_for_pharmacies".
 *
 * @property integer $pharmacy_id
 * @property integer $seminar_id
 */
class Pharmacy extends \yii\db\ActiveRecord
{
    public $pharmacies = [];

    public static function tableName()
    {
        return 'seminar_for_pharmacies';
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
