<?php

namespace common\models\presentation;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use common\models\presentation\View;
use common\models\Presentation;

/**
 * This is the model class for table "presentation_view_answers".
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
        return 'presentation_view_answers';
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

    /**
     * @param $attribute
     */
    public function validatorAnswersCount($attribute)
    {
        if (!$this->hasErrors()) {
            if(stristr($this->value, ';')) {
                $values = explode(';', $this->value);
                if(count($values) > $this->question->right_answers) {
                    $this->addError($attribute, 'Неправильное количество ответов. ');
                }
            }
        }
    }

    public function validatorInOptionList($attribute)
    {
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


    public static function filterModels($models)
    {

        $presentation = $models[0]->question->presentation;

        // Доступна ли презентация пользователю
        if(Presentation::getOneForCurrentUser($presentation->id)==null) {
            throw new BadRequestHttpException('Вам недоступна эта презентация.');
        }

        // Просматривал ли пользователь ранее эту презентацию
        if(Presentation::isViewedByCurrentUser($presentation->id)) {
            throw new BadRequestHttpException('Вы уже просматривали эту презентацию.');
        }

        // Получаем список вопросов презентации
        $questions = ArrayHelper::map($presentation->questions,'id','id');

        // Ответы на вопросы презентации
        $answers = [];

        // Если ответ относится к презентации - копируем в отвеченные вопросы
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

    public function getQuestion()
    {
        return $this->hasOne(Question::className(),['id'=>'question_id']);
    }

    public function getView()
    {
        return $this->hasOne(View::className(),['id'=>'view_id']);
    }

}
