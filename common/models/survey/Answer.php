<?php

namespace common\models\survey;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use common\models\survey\View;
use common\models\survey\Question;
use common\models\Survey;

/**
 * This is the model class for table "survey_view_answers".
 *
 * @property integer $id
 * @property integer $view_id
 * @property integer $question_id
 * @property string $value
 */
class Answer extends ActiveRecord

{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_view_answers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['view_id','question_id', 'value'], 'required'],
            [['question_id','view_id'], 'integer'],
            [['question_id'], 'exist', 'targetClass'=>Question::className(), 'targetAttribute'=>'id'],
            [['view_id'], 'exist', 'targetClass'=>View::className(), 'targetAttribute'=>'id'],
            [['value'], 'string', 'max' => 255],
            [['value'],'validatorInOptionList'],
            [['value'],'validatorAnswersCount'],
        ];
    }

    public function validatorAnswersCount($attribute) {
        if (!$this->hasErrors()) {
            if(stristr($this->value, ';')) {
                $values = explode(';', $this->value);
                if(count($values) > $this->question->right_answers) {
                    $this->addError($attribute, 'Неправильное количество ответов. ');
                }
            }
        }
    }

    public function validatorInOptionList($attribute) {
        if (!$this->hasErrors() && $this->question->options) {
            $values = explode(';', $this->value);
            $valid = true;
            $options = ArrayHelper::map($this->question->options,'id','value');
            foreach($values as $value) {
                $valid = $valid && in_array(trim($value), $options);
            }
            if (!$valid) {
                $this->addError($attribute, 'Ответ не соответствует предложенным вариантам.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    public static function filterModels($models) {

        $survey = $models[0]->question->survey;

        // Доступна ли анкета пользователю
        if(Survey::getOneForCurrentUser($survey->id)==null) {
            throw new BadRequestHttpException('Вам недоступна эта анкета.');
        }

        // Получаем список вопросов анкеты
        $questions = ArrayHelper::map($survey->questions,'id','id');

        // Ответы на вопросы анкеты
        $answers = [];

        // Если ответ относится к анкеты - копируем в отвеченные вопросы
        foreach($models as $answer) {
            if(in_array($answer->question_id,$questions))
                $answers[$answer->question_id] = $answer;
        }

        // Проверяем, ответил ли пользователь на все вопросы
        if(count($answers) !== count($questions)) {
            throw new BadRequestHttpException('Вы ответили не на все вопросы.');
        }

        return $answers;
    }

    public function getView() {
        return $this->hasOne(View::className(),['id'=>'view_id']);
    }

    public function getQuestion() {
        return $this->hasOne(Question::className(),['id'=>'question_id']);
    }

}
