<?php

namespace common\models\survey;

use Yii;
use yii\db\ActiveRecord;

use common\models\Survey;

/**
 * This is the model class for table "survey_questions".
 *
 * @property integer $id
 * @property string $question
 * @property integer $survey_id
 * @property integer $right_answers
 */
class Question extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question', 'right_answers'], 'required'],
            ['question', 'string'],
            ['right_answers', 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question' => 'Вопрос',
            'right_answers' => 'Количество правильных ответов'
        ];
    }

    public function fields() {
        return [
            'id', 'question', 'options', 'right_answers'
        ];
    }

    public function getSurvey() {
        return $this->hasOne(Survey::className(), ['id' => 'survey_id']);
    }

    public function getOptions() {
        return $this->hasMany(Option::className(), ['question_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach($this->options as $option)
            $option->delete();
    }

}
