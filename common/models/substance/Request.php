<?php

namespace common\models\substance;

use Yii;

use common\models\Substance;
use common\models\User;

/**
 * This is the model class for table "substance_requests".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $substance_id
 * @property string $date_request
 */
class Request extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'substance_requests';
    }

    public function rules()
    {
        return [
            [['user_id', 'substance_id'], 'required'],
            [['user_id', 'substance_id'], 'integer'],
            [['user_id'], 'exist', 'targetClass'=>User::className(), 'targetAttribute'=>'id'],
            [['substance_id'], 'exist', 'targetClass'=>Substance::className(), 'targetAttribute'=>'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'substance_id' => 'Вещество',
            'date_request' => 'Дата запроса',
        ];
    }

    public static function add($substance_id)
    {
        $object = new static();
        $object->user_id = Yii::$app->user->id;
        $object->substance_id = $substance_id;
        $object->save(false);
    }

    public function getUser()
    {
        return $this->hasOne(User::classname(),['id'=>'user_id']);
    }

    public function getSubstance()
    {
        return $this->hasOne(Substance::classname(),['id'=>'substance_id']);
    }
}
