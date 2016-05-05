<?php

namespace common\models\profile;

use Yii;
use common\models\User;

/**
 * This is the model class for table "user_types".
 *
 * @property integer $id
 * @property string $name
 */
class Type extends \yii\db\ActiveRecord
{
    const TYPE_PHARMACIST = 1;
    const TYPE_AGENT = 2;

    public static function tableName()
    {
        return 'user_types';
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
            'name' => 'Тип пользователя',
        ];
    }

    public function getUsers()
    {
        return $this->hasMany(User::className(), ['type_id' => 'id']);
    }

    public function afterDelete()
    {
        foreach($this->users as $user)
            $user->delete();
        parent::afterDelete();
    }
}
