<?php

namespace common\models\presentation;

use Yii;

/**
 * This is the model class for table "presentation_for_types".
 *
 * @property integer $presentation_id
 * @property integer $type_id
 */
class Type extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'presentation_for_types';
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

    public function getType() {
        return $this->hasOne(\common\models\profile\Type::className(),['id'=>'type_id']);
    }
}