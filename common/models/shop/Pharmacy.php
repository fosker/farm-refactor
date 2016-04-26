<?php

namespace common\models\shop;

use Yii;

/**
 * This is the model class for table "shop_item_for_pharmacies".
 *
 * @property integer $pharmacy_id
 * @property integer $item_id
 */
class Pharmacy extends \yii\db\ActiveRecord
{
    public $pharmacies = [];
    public static function tableName()
    {
        return 'shop_item_for_pharmacies';
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
