<?php

namespace common\models\presentation;

use Yii;

/**
 * This is the model class for table "presentation_for_education".
 *
 * @property integer $id
 * @property integer $presentation_id
 * @property integer $education_id
 */
class Education extends \yii\db\ActiveRecord
{
    public $education = [];

    public static function tableName()
    {
        return 'presentation_for_education';
    }

    public function rules()
    {
        return [
        ];
    }

    public function attributeLabels()
    {
        return [
            'education' => 'Образования'
        ];
    }

    public function getEducation() {
        return $this->hasOne(\common\models\profile\Education::className(),['id'=>'education_id']);
    }
}
