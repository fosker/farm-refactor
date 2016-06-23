<?php

namespace common\models\presentation;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "presentation_question_options".
 *
 * @property integer $id
 * @property string $value
 * @property integer $question_id
 * @property boolean $isValid
 */
class Option extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'presentation_question_options';
    }

    public function fields() {
        return [
            'value', 'isValid'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'required'],
            ['value', 'trim'],
            ['value', 'string'],
            ['isValid', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value' => 'Вариант ответа',
            'isValid' => 'Правильный ответ'
        ];
    }

    public static function findAllByQuestionId($question_id)
    {
        return Option::find()->select('*')->from(Option::tableName())->where(['question_id' => $question_id]);
    }

}
