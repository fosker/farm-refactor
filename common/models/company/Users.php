<?php

namespace common\models\company;

use Yii;
use common\models\User;
use common\models\Push;
use common\models\Company;

/**
 * This is the model class for table "company_push_for_users".
 *
 * @property integer $id
 * @property integer $push_id
 * @property integer $user_id
 * @property integer $company_id
 * @property integer $isViewed
 * @property integer $isRead
 *
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_push_for_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['push_id', 'user_id', 'company_id'], 'required'],
            [['push_id', 'user_id', 'isViewed', 'isRead'], 'integer'],
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

    public function getCompany()
    {
        return $this->hasOne(Company::className(),['id'=>'company_id']);
    }
}
