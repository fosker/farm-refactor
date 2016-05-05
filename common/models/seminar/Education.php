<?php

namespace common\models\seminar;

use Yii;

/**
 * This is the model class for table "seminar_for_education".
 *
 * @property integer $id
 * @property integer $seminar_id
 * @property integer $education_id
 */
class Education extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'seminar_for_education';
    }

    /**
     * @inheritdoc
     */
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
