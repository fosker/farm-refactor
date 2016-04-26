<?php

namespace common\models\profile;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_set_notifications".
 *
 * @property integer $user_id
 * @property integer $notification_id
 * @property integer $value
 */
class SetNotification extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_set_notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','value'], 'required'],
            [['id'],function($model,$attribute) {
                if (static::findOne($this->id)->user_id != Yii::$app->user->id) {
                    $this->addError($attribute, 'Неккоректные данные.');
                }
            }],
            [['id','value'], 'integer'],
            [['value'],'in','range'=>[0,1]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'value' => 'Флаг',
        ];
    }


    public static function registerNewUser($user_id)
    {
        foreach(Notification::find()->all() as $notify) {
            $static = new static();
            $static->user_id = $user_id;
            $static->notification_id = $notify->id;
            $static->save();
        }
    }

    public function getNotification()
    {
        return $this->hasOne(Notification::className(),['id'=>'notification_id']);
    }
}
