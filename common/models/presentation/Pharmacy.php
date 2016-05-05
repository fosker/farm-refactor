<?php

namespace common\models\presentation;

use Yii;

/**
 * This is the model class for table "presentation_for_pharmacies".
 *
 * @property integer $pharmacy_id
 * @property integer $presentation_id
 */
class Pharmacy extends \yii\db\ActiveRecord
{
    public $pharmacies = [];

    public static function tableName()
    {
        return 'presentation_for_pharmacies';
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
        return $this->hasOne(\common\models\company\Pharmacy::className(),['id'=>'pharmacy_id']);
    }
}
