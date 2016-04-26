<?php

namespace common\models\vacancy;

use backend\models\Param;
use Yii;
use common\models\Vacancy;
use common\models\User;
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

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)) {
            if($insert) {
                $this->sendInfoMail(Yii::$app->user->identity, Vacancy::findOne($this->vacancy_id));
            }
            return true;
        } else return false;
    }

    public function getUser() {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getVacancy() {
        return $this->hasOne(Vacancy::className(),['id'=>'vacancy_id']);
    }

    private function sendInfoMail($user, $vacancy)
    {
        Yii::$app->mailer->compose('@common/mail/sign-up-vacancy', [
            'vacancy_title'=>$vacancy->title,
            'username'=>$user->name,
            'contact'=> $this->contact,
            'date'=>$this->date_contact,
        ])
            ->setFrom(Param::getParam('email'))
            ->setTo($vacancy->email)
            ->setSubject('Новая заявка на вакансию!')
            ->send();
    }
}
