<?php

namespace common\models\vacancy;

use Yii;
use common\models\User;
use common\models\Vacancy;
/**
 * This is the model class for table "vacancy_comments".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property integer $vacancy_id
 * @property string $date_add
 */
class Comment extends \yii\db\ActiveRecord
{
    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'add'=> ['comment','vacancy_id'],
        ]);
    }


    public static function tableName()
    {
        return 'vacancy_comments';
    }

    public function fields() {
        if($this->scenario == 'default')
            return [
                'id',
                'user',
                'comment',
                'date_add'=>function($model) {
                    return strtotime($model->date_add);
                }
            ];
        else
            return $this->scenarios()[$this->scenario];
    }

    public function rules()
    {
        return [
            [['comment', 'vacancy_id'], 'required'],
            [['comment'], 'string', 'max'=>400],
            [['vacancy_id'], 'exist', 'targetClass'=>Vacancy::className(), 'targetAttribute'=>'id'],
            [['user_id'], 'exist', 'targetClass'=>User::className(), 'targetAttribute'=>'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'comment' => 'Комментарий',
            'seminar_id' => 'Вакансия',
            'date_add' => 'Дата добавления',
        ];
    }

    public static function findByVacancy($vacancy_id)
    {
        return static::find()->where(['vacancy_id'=>$vacancy_id])->orderBy(['date_add'=>SORT_DESC]);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getVacancy() {
        return $this->hasOne(Vacancy::className(), ['id' => 'vacancy_id']);
    }
}
