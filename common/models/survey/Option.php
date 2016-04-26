<?php

namespace common\models\survey;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "survey_question_options".
 *
 * @property integer $id
 * @property string $value
 * @property integer $question_id
 */
class Option extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_question_options';
    }

    public function fields() {
        return [
            'value',
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
            ['value', 'string']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'value' => 'Вариант ответа'

        ];
    }

}
