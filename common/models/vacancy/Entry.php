<?php

namespace common\models\vacancy;

use Yii;
use common\models\Vacancy;
use common\models\User;
use common\models\Mailer;

/**
 * This is the model class for table "vacancy_entries".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $vacancy_id
 * @property string $contact
 * @property string $date_contact
 * @property string $date_add
 */
class Entry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vacancy_entries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vacancy_id', 'contact', 'date_contact', 'user_id'], 'required'],
            [['date_contact'], 'string'],
            [['contact'], 'string', 'max' => 20],
            [['vacancy_id'], 'unique', 'targetAttribute'=>['vacancy_id','user_id'], 'message'=>'Вы уже подали заявку на вакансию.'],
            [['vacancy_id'], 'exist', 'targetClass'=>Vacancy::className(), 'targetAttribute'=>'id'],
        ];
    }

    public function fields() {
        return [
            'vacancy_id','contact','date_contact',
            'date_add'=>function($model) {
                return strtotime($model->date_add);
            }
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'vacancy_id' => 'Вакансия',
            'contact' => 'Контактные данные',
            'date_contact' => 'Связаться',
            'date_add' => 'Дата записи',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Mailer::sendVacancy(Yii::$app->user->identity, Vacancy::findOne($this->vacancy_id), $this);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getVacancy()
    {
        return $this->hasOne(Vacancy::className(),['id'=>'vacancy_id']);
    }
}
