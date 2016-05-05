<?php

namespace common\models\profile;

use Yii;
use common\models\location\City;
use common\models\User;
use common\models\Factory;


/**
 * This is the model class for table "agent_update_requests".
 *
 * @property integer $agent_id
 * @property string $name
 * @property string $email
 * @property integer $factory_id
 * @property integer $city_id
 * @property string $phone
 * @property string $details
 * @property string date_add
 */

class AgentUpdateRequest extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'agent_update_requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'factory_id', 'city_id'], 'required'],
            [['name','email'], 'string', 'max'=>100],
            [['phone'], 'string', 'max' => 30],
            [['email'],'email'],
            [['email'],'unique', 'targetClass'=> User::className(), 'targetAttribute'=>'email'],
            [['city_id'], 'exist', 'targetClass'=> City::className(), 'targetAttribute'=>'id'],
            [['details'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'agent_id' => 'ID',
            'name' => 'Имя Фамилия',
            'email' => 'Email',
            'factory_id' => 'Фабрика',
            'city_id' => 'Город',
            'details'=>'Дополнительные сведения',
            'date_add'=>'Дата запроса',
            'phone' => 'Мобильный телефон',
        ];
    }

    public function loadCurrentAttributes($user)
    {
        $this->attributes = $user->attributes;
        $this->agent_id = $user->id;
        $this->factory_id = $user->agent->factory_id;
        $this->city_id = $user->agent->city_id;
    }

    public function getFactory()
    {
        return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'agent_id']);
    }

    public function fields() {
        return [
            'agent_id', 'name', 'factory_id', 'city_id', 'details', 'phone', 'email'
        ];
    }
}
