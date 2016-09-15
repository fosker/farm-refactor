<?php

namespace common\models\pharmbonus;

use Yii;
use yii\base\Model;


class AgentRequest extends Model
{

    public $name;
    public $factory;
    public $email;
    public $phone;

    public function rules()
    {
        return [
            [['name', 'factory', 'email', 'phone'], 'required'],
            [['name', 'factory', 'phone'], 'string'],
            [['email'], 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Фамилия Имя',
            'factory' => 'Фабрика',
            'email' => 'Email',
            'phone' => 'Номер телефона',
        ];
    }
}
