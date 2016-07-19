<?php

namespace common\models\forms;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Theme;
use yii\helpers\ArrayHelper;
use common\models\forms\Field;
use common\models\forms\Option;
use common\models\Form;
use yii\web\BadRequestHttpException;

class Answer extends Model
{

    public $field_id;
    public $value;


    public function rules()
    {
        return [
            [['field_id'], 'required'],
            [['field_id'], 'integer'],
            [['value'], 'string'],
            [['value'],'validatorInOptionList'],
            [['field_id'], 'requiredForm']
        ];
    }

    public function validatorInOptionList($attribute)
    {
        if($options = ArrayHelper::map(Option::find()->where(['field_id' => $this->field_id])->all(),'id','value')) {
            if(!in_array($this->value, $options)) {
                $this->addError($attribute, 'Ответ не соответствует предложенным вариантам.');
            }
        }
    }

    public function requiredForm()
    {
        $field = Field::findOne($this->field_id);
        if($field->isRequired) {
            if(!$this->value) {
                $this->addError($field->label, 'Поле не заполнено.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'field_id' => 'Поле',
            'value' => 'Значение',
        ];
    }

    public static function filterModels($models)
    {
        $form = Form::findOne(Field::findOne(['id' => $models[0]->field_id])->form_id);

        $fields = ArrayHelper::map($form->fields,'id','id');

        $answers = [];

        foreach($models as $answer) {
            if(in_array($answer->field_id,$fields))
                $answers[$answer->field_id] = $answer;
        }

        return $answers;
    }

}