<?php

namespace common\models\stock;

use Yii;

/**
 * This is the model class for table "stock_for_pharmacies".
 *
 * @property integer $pharmacy_id
 * @property integer $stock_id
 */
class Pharmacy extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'stock_for_pharmacies';
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
