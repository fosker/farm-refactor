<?php

namespace common\models\seminar;

use Yii;
use yii\db\ActiveRecord;

use common\models\Seminar;
use common\models\User;
use common\models\Mailer;
use common\models\profile\Device;

/**
 * This is the model class for table "seminar_entries".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $seminar_id
 * @property string $contact
 * @property string $date_contact
 * @property string $date_add
 */
class Entry extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seminar_entries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['seminar_id', 'contact', 'date_contact', 'user_id'], 'required'],
            [['date_contact'], 'string'],
            [['contact'], 'KoCTblJIb'],
            [['contact'], 'string', 'max' => 20],
            [['seminar_id'], 'unique', 'targetAttribute'=>['seminar_id','user_id'], 'message'=>'Вы уже записаны на этот семинар.'],
            [['seminar_id'], 'exist', 'targetClass'=>Seminar::className(), 'targetAttribute'=>'id'],
        ];
    }

    public function KoCTblJIb($attribute)
    {
        if (!$this->hasErrors()) {
            $device = Device::findOne(['access_token' => Yii::$app->request->get('access-token')]);
            if ($device->type == Device::TYPE_IOS) {
                $this->addError($attribute, 'Временно нельзя записываться на семинары. ');
            }
        }
    }

    public function fields() {
        return [
            'seminar_id','contact','date_contact',
            'date_add'=>function($model) {
                return strtotime($model->date_add);
            }
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
            'seminar_id' => 'Семинар',
            'contact' => 'Контактные данные',
            'date_contact' => 'Связаться',
            'date_add' => 'Дата записи',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Mailer::sendSeminar(Yii::$app->user->identity, Seminar::findOne($this->seminar_id), $this);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getSeminar()
    {
        return $this->hasOne(Seminar::className(),['id'=>'seminar_id']);
    }

}
