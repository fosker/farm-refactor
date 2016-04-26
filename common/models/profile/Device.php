<?php

namespace common\models\profile;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_devices".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $access_token
 * @property string $push_token
 * @property integer $type
 */
class Device extends ActiveRecord
{
    const TYPE_ANDROID = 1;
    const TYPE_IOS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_devices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['push_token'],'string'],
            [['type'],'in','range'=>[static::TYPE_ANDROID, static::TYPE_IOS]],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            if($insert)
                $this->id = Yii::$app->security->generateRandomString();
            return true;
        } else {
            return false;
        }
    }

    public function fields() {
        return [
            'type','push_token'
        ];
    }

    public static function getUserByAccessToken($token)
    {
        return static::findOne(['access_token'=>$token])->user_id;
    }
}
