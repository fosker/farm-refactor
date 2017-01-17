<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use common\models\pharmbonus\Users;
use common\models\company\Users as CompanyUsers;
use common\models\factory\Users as FactoryUsers;
/**
 * This is the model class for table "pushes".
 *
 * @property integer $id
 * @property string $message
 * @property string $link
 * @property integer $device_count
 * @property integer $views
 * @property integer $date_send
 * @property integer $forList
 * @property integer $type
 */
class Push extends \yii\db\ActiveRecord
{

    public $regions = [];
    public $cities = [];
    public $pharmacies = [];
    public $educations = [];
    public $users = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pushes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'users', 'type'], 'required'],
            [['message', 'date_send'], 'string'],
            [['device_count', 'views', 'forList', 'type'], 'integer'],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'regions' => 'Регионы',
            'cities' => 'Города',
            'pharmacies' => 'Аптеки',
            'education' => 'Образования',
            'message' => 'Сообщение',
            'users' => 'Пользователи',
            'device_count' => 'Количество пользователей',
            'link' => 'Ссылка',
            'views' => 'Количество просмотров',
            'date_send' => 'Дата отправки',
            'forList' => 'Отправлять списку',
            'type' => 'Тип'
        ];
    }

    public function fields() {
        return [
            'id', 'message', 'link',
            'from' => function() {
                if($this->companyPushes) {
                    return $this->companyPushes[0]->company->title;
                } elseif($this->factoryPushes) {
                    return $this->factoryPushes[0]->factory->title;
                } else {
                    return 'PharmBonus';
                }
            },
            'isViewed' => function() {
                if($this->companyPushes) {
                    return CompanyUsers::findOne(['push_id' => $this->id, 'user_id' => Yii::$app->user->id])->isViewed;
                } elseif($this->pharmPushes) {
                    return Users::findOne(['push_id' => $this->id, 'user_id' => Yii::$app->user->id])->isViewed;
                } elseif($this->factoryPushes) {
                    return FactoryUsers::findOne(['push_id' => $this->id, 'user_id' => Yii::$app->user->id])->isViewed;
                }
            },
            'isRead' => function() {
                if($this->companyPushes) {
                    return CompanyUsers::findOne(['push_id' => $this->id, 'user_id' => Yii::$app->user->id])->isRead;
                } elseif($this->pharmPushes) {
                    return Users::findOne(['push_id' => $this->id, 'user_id' => Yii::$app->user->id])->isRead;
                } elseif($this->factoryPushes) {
                    return FactoryUsers::findOne(['push_id' => $this->id, 'user_id' => Yii::$app->user->id])->isRead;
                }
            },
            'date_send'=>function($model) {
                return strtotime($model->date_send);
            },
            'type'
        ];
    }

    public static function links()
    {
        return [
            'present'=>'Подарки',
            'presentation'=>'Презентации',
            'survey'=>'Анкеты',
            'seminar'=>'Семинары',
            'stock'=>'Акции',
            'news' => 'Новости',
            'vacancy' => 'Вакансии',
        ];
    }

    public function getLinkTitleHref()
    {
        return Html::a($this->linkTitle,['/'.explode('/',$this->link)[0].'/view', 'id'=>explode('/',$this->link)[1]]);
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
            case 'presentation':
                $item = Presentation::findOne($path[1]);
                $name = 'Презентация: ';
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
            case 'news':
                $item = News::findOne($path[1]);
                $name = 'Новость: ';
                break;
            case 'vacancy':
                $item = Vacancy::findOne($path[1]);
                $name = 'Вакансия: ';
                break;
        }
        return $name.$item['title'];
    }

    public static function getForCurrentUser()
    {
        return static::find()
            ->joinWith('companyPushes')
            ->joinWith('pharmPushes')
            ->joinWith('factoryPushes')
            ->andWhere([CompanyUsers::tableName().'.user_id' => Yii::$app->user->id])
            ->orWhere([Users::tableName().'.user_id' => Yii::$app->user->id])
            ->orWhere([FactoryUsers::tableName().'.user_id' => Yii::$app->user->id])
            ->orderBy(['id'=>SORT_DESC]);
    }

    public static function getOneForCurrentUser($id)
    {
        return static::getForCurrentUser()->andWhere([static::tableName().'.id'=>$id])->one();
    }

    public function getCompanyPushes()
    {
        return $this->hasMany(CompanyUsers::className(),['push_id'=>'id']);
    }

    public function getPharmPushes()
    {
        return $this->hasMany(Users::className(),['push_id'=>'id']);
    }

    public function getFactoryPushes()
    {
        return $this->hasMany(FactoryUsers::className(),['push_id'=>'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Users::deleteAll(['push_id'=>$this->id]);
        CompanyUsers::deleteAll(['push_id'=>$this->id]);
    }
}
