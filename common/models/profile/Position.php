<?php

namespace common\models\profile;

use Yii;
use common\models\User;

/**
 * This is the model class for table "user_positions".
 *
 * @property integer $id
 * @property string $name
 */
class Position extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'user_positions';
    }

    public function rules()
    {
        return [
            ['name', 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название должности',
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::className(), ['position_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
    }
}
