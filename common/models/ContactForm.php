<?php

namespace common\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "contact_form".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $subject
 * @property string $message
 * @property string $date
 */
class ContactForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_form';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'message'], 'required'],
            [['user_id'], 'integer'],
            [['message'], 'string'],
            [['date'], 'safe'],
            [['subject'], 'string', 'max' => 50],
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
            'subject' => 'Тема сообщения',
            'message' => 'Сообщение',
            'date' => 'Дата отправки',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
