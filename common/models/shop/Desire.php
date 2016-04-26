<?php

namespace common\models\shop;

use Yii;
use yii\db\ActiveRecord;

use common\models\User;
use common\models\Item;

/**
 * This is the model class for table "user_desires".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $item_id
 */
class Desire extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_desires';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id','user_id'], 'required'],
            [['item_id'], 'isAvailable'],
            [['item_id'], 'unique', 'targetAttribute'=>['item_id','user_id'], 'message'=>'Подарок уже у вас в желаниях.'],
        ];
    }

    public function isAvailable($attribute)
    {
        if (!$this->hasErrors()) {
            if (!Item::getOneForCurrentUser($this->item_id)) {
                $this->addError($attribute, 'Этот подарок недоступен для ваc.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'user_id' => 'Пользователь',
            'item_id' => 'Подарок',
        ];
    }

    public function extraFields() {
        return [
            'item'
        ];
    }

    /**
     * @return \yii\db\Query
     */
    public static function getForCurrentUser()
    {
        return static::find()->where(['user_id'=>Yii::$app->user->id])->orderBy(['id'=>SORT_DESC]);
    }

    public function getItem() {
        return $this->hasOne(Item::className(), ['id'=>'item_id']);
    }

}
