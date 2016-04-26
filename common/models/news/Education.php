<?php

namespace common\models\news;

use Yii;

/**
 * This is the model class for table "news_for_education".
 *
 * @property integer $id
 * @property integer $news_id
 * @property integer $education_id
 */
class Education extends \yii\db\ActiveRecord
{
    public $education = [];

    public static function tableName()
    {
        return 'news_for_education';
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
