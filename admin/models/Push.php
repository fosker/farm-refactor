<?php

namespace backend\models;
use yii\base\Model;
use common\models\Seminar;
use common\models\Survey;
use common\models\Item;
use common\models\Stock;
use common\models\Presentation;

class Push extends Model
{
    public $regions = [];
    public $cities = [];
    public $pharmacies = [];
    public $educations = [];
    public $users = [];

    public $message;
    public $link;

    public function attributeLabels() {
        return [
            'regions' => 'Регионы',
            'cities' => 'Города',
            'pharmacies' => 'Аптеки',
            'education' => 'Образования',
            'message' => 'Сообщение',
            'users' => 'Пользователи',
            'link' => 'Ссылка'
        ];
    }

    public function rules()
    {
        return [
            [['message', 'users'], 'required'],
            ['link', 'string']
        ];
    }

    public function getLinkTitle()
    {
        $path = explode('/',$this->link);
        $name = '';
        $item = ['title'=>''];
        switch($path[0]) {
            case 'present':
                $item = Item::findOne($path[1]);
                $name = 'Подарок: ';
                break;
            case 'survey':
                $item = Survey::findOne($path[1]);
                $name = 'Анкета: ';
                break;
            case 'seminar':
                $item = Seminar::findOne($path[1]);
                $name = 'Семинар: ';
                break;
            case 'stock':
                $item = Stock::findOne($path[1]);
                $name = 'Акция: ';
                break;
            case 'presentation':
                $item = Presentation::findOne($path[1]);
                $name = 'Презентация: ';
                break;
        }
        return $name.$item['title'];
    }

}