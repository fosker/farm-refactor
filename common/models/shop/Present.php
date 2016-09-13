<?php

namespace common\models\shop;

use Yii;

use common\models\Item;
use common\models\User;
use common\models\Mailer;
/**
 * This is the model class for table "user_presents".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $item_id
 * @property integer $count
 * @property string $promo
 * @property string date_buy
 */
class Present extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_presents';
    }

    public function usePromo() {
        $this->promo = null;
        $this->save(false);
    }

    public function fields() {
        return [
            'id', 'item', 'count',
            'date_buy'=>function($model) {
                return strtotime($model->date_buy);
            }
        ];
    }

    public function extraFields() {
        return [
            'promo','user',
            'date_buy'=>function($model) {
                return strtotime($model->date_buy);
            },
            'description'=>function($model) {
                return $model->item->description;
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'count', 'user_id'], 'required'],
            ['date_buy', 'string'],
            [['count'], 'integer','min'=>1],
            [['count'], 'validatePoints'],
            [['item_id'], 'isAvailable'],
        ];
    }

    public function validatePoints($attribute)
    {
        if (!$this->hasErrors()) {
            $item = Item::findOne($this->item_id);
            if ($this->count*$item->points > Yii::$app->user->identity->points) {
                $this->addError($attribute, 'У вас недостаточно баллов.');
            }
        }
    }

    public function isAvailable($attribute)
    {
        if (!$this->hasErrors()) {
            if (!Item::getOneForCurrentUser($this->item_id)) {
                $this->addError($attribute, 'Этот подарок недоступен для ваc.');
            }
            if ($this->count > Item::findOne($this->item_id)->count) {
                $this->addError($attribute, 'Подарок отсутствует в данный момент.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'item_id' => 'Подарок',
            'count' => 'Количество',
            'promo' => 'Промо-код',
            'date_buy'=>'Дата покупки',
        ];
    }

    public static function getForCurrentUser()
    {
        return static::find()->where(['user_id'=>Yii::$app->user->id])->orderBy(['date_buy'=>SORT_DESC]);
    }

    public static function findByPromo($promo)
    {
        return static::findOne(['promo'=>$promo]);
    }

    public static function findBoughtToday()
    {
        return static::find()->where('date_buy > CURDATE()')->andWhere(['not', ['promo'=>""]]);
    }

    public function getItem() {
        return $this->hasOne(Item::className(), ['id'=>'item_id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

}
