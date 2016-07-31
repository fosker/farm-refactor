<?php

namespace common\models\pharmbonus;

use Yii;
use common\models\User;
use common\models\Push;

/**
 * This is the model class for table "pharmbonus_push_for_users".
 *
 * @property integer $id
 * @property integer $push_id
 * @property integer $user_id
 * @property integer $isViewed
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pharmbonus_push_for_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['push_id', 'user_id'], 'required'],
            [['push_id', 'user_id', 'isViewed'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'push_id' => 'Push ID',
            'user_id' => 'User ID',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getPush()
    {
        return $this->hasOne(Push::className(),['id'=>'push_id']);
    }
}
