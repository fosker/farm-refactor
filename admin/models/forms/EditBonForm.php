<?php

namespace backend\models\forms;

use yii\base\Model;

class EditBonForm extends Model
{
    public $amount;
    public $message;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'required'],
            [['amount'], 'integer'],
            [['message'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => 'Бонусы (для вычитания бонусов укажите отрицательное значение)',
            'message' => 'Сообщение'
        ];
    }
}