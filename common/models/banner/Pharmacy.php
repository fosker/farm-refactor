<?php

namespace common\models\banner;

use Yii;

/**
 * This is the model class for table "banner_for_pharmacies".
 *
 * @property integer $pharmacy_id
 * @property integer $banner_id
 */
class Pharmacy extends \yii\db\ActiveRecord
{
    public $pharmacies = [];

    public static function tableName()
    {
        return 'banner_for_pharmacies';
    }

    public function rules()
    {
        return [

        ];
    }

    public function attributeLabels()
    {
        return [
            'pharmacies' => 'Аптеки'
        ];
    }

    public function getPharmacy() {
        return $this->hasOne(\common\models\company\Pharmacy::className(),['id'=>'pharmacy_id']);
    }
}
