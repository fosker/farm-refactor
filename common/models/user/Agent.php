<?php

namespace common\models\user;

use Yii;
use common\models\Factory;
use common\models\User;

/**
 * This is the model class for table "agents".
 *
 * @property integer $id
 * @property integer $factory_id
 */

class Agent extends \yii\db\ActiveRecord
{

    const STATUS_VERIFY = 0;
    const STATUS_ACTIVE = 1;


    public static function tableName()
    {
        return 'agents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['factory_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'factory_id' => 'Фабрика',
        ];
    }

    public function fields() {

        return ['id', 'factory'];
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }

    public function verified()
    {
        $this->user->status = static::STATUS_ACTIVE;
        $this->user->save(false);
    }

    public function ban()
    {
        $this->user->status = static::STATUS_VERIFY;
        $this->user->save(false);
    }

}
