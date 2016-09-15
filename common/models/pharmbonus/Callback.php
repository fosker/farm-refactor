<?php

namespace common\models\pharmbonus;

use Yii;
use yii\base\Model;


class Callback extends Model
{

    public $name;
    public $company;
    public $email;
    public $phone;

    public function rules()
    {
        return [
            [['name', 'company', 'email', 'phone'], 'required'],
            [['name', 'company', 'phone'], 'string'],
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
            'company' => 'Компания',
            'email' => 'Email',
            'phone' => 'Номер телефона',
        ];
    }
}
