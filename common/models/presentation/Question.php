<?php

namespace common\models\presentation;

use Yii;
use yii\db\ActiveRecord;

use common\models\Presentation;
use common\models\presentation\Option;

/**
 * This is the model class for table "presentation_questions".
 *
 * @property integer $id
 * @property string $question
 * @property integer $presentation_id
 * @property integer $order_index
 * @property integer $right_answers
 */
class Question extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'presentation_questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question', 'order_index', 'right_answers'], 'required'],
            [['order_index', 'right_answers'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question' => 'Вопрос',
            'order_index' => 'Порядковый номер',
            'right_answers' => 'Количество правильных ответов'
        ];
    }

    public function fields()
    {
        return [
            'id', 'question', 'options', 'order'=>'order_index', 'right_answers'
        ];
    }

    public function getOptions()
    {
        return $this->hasMany(Option::className(), ['question_id' => 'id']);
    }

    public function getPresentation()
    {
        return $this->hasOne(Presentation::className(), ['id' => 'presentation_id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach($this->options as $option)
            $option->delete();
    }

}
