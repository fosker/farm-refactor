<?php

namespace common\models\survey;


use Yii;

/**
 * This is the model class for table "survey_for_types".
 *
 * @property integer $survey_id
 * @property integer $type_id
 */
class Type extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'survey_for_types';
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