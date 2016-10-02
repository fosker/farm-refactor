<?php

namespace common\models\seminar;

use Yii;

use common\models\Seminar;
use common\models\User;

/**
 * This is the model class for table "seminar_comments".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property integer $seminar_id
 * @property integer $admin_comment
 */
class Comment extends \yii\db\ActiveRecord
{
    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'add'=> ['comment','seminar_id'],
            'comment' => ['admin_comment'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seminar_comments';
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment', 'seminar_id'], 'required'],
            [['comment', 'admin_comment'], 'string'],
            [['seminar_id'], 'exist', 'targetClass'=>Seminar::className(), 'targetAttribute'=>'id'],
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
            'seminar_id' => 'Семинар',
            'date_add' => 'Дата добавления',
            'admin_comment' => 'Комментарий',
        ];
    }

    public static function findBySeminar($seminar_id)
    {
        return static::find()->where(['seminar_id'=>$seminar_id])->orderBy('date_add desc');
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getSeminar() {
        return $this->hasOne(Seminar::className(), ['id' => 'seminar_id']);
    }

}
