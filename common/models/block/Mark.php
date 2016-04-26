<?php

namespace common\models\block;

use Yii;

use common\models\Block;
use common\models\User;

/**
 * This is the model class for table "block_marks".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $block_id
 * @property string $date_add
 * @property integer $mark
 */
class Mark extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'block_marks';
    }

    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'add'=> ['mark','block_id'],
        ]);
    }

    public function fields() {
        if($this->scenario == 'default')
            return [
                'id',
                'user',
                'block_id',
                'mark',
                'date_add'=>function($model) {
                    return strtotime($model->date_add);
                }
            ];
        else
            return $this->scenarios()[$this->scenario];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'block_id' => 'Блок',
            'date_add' => 'Дата добавления',
            'mark' => 'Оценка'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['block_id'], 'required'],
            [['block_id', 'mark'], 'integer'],
            [['mark'],'in', 'range'=>[1,2,3,4,5]],
            [['block_id'], 'unique', 'targetAttribute'=>['block_id','user_id'], 'message'=>'Вы уже ставили оценку.'],
            [['block_id'], 'exist', 'targetClass'=>Block::className(), 'targetAttribute'=>'id'],
            [['user_id'], 'exist', 'targetClass'=>User::className(), 'targetAttribute'=>'id'],
        ];
    }

    public static function isMarkedByCurrentUser($block_id) {
        return static::find()->where(['user_id'=>Yii::$app->user->id, 'block_id'=>$block_id])->exists();
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getBlock() {
        return $this->hasOne(Block::className(), ['id' => 'block_id']);
    }

    public static function calculateForBlock($id)
    {
        $marks = static::find()->where(['block_id'=>$id]);

        try{
            return round($marks->sum('mark')/$marks->count(), 1);
        } catch(\yii\base\ErrorException $e) {
            return 0;
        }
    }

}
