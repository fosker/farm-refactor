<?php

namespace common\models\seminar;

use Yii;

/**
 * This is the model class for table "seminar_for_types".
 *
 * @property integer $seminar_id
 * @property integer $type_id
 */
class Type extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'seminar_for_types';
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