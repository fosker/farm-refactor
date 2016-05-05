<?php

namespace common\models\stock;

use Yii;

/**
 * This is the model class for table "stock_for_types".
 *
 * @property integer $stock_id
 * @property integer $type_id
 */
class Type extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'stock_for_types';
    }

    public function rules()
    {
        return [
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getType()
    {
        return $this->hasOne(\common\models\profile\Type::className(),['id'=>'type_id']);
    }
}