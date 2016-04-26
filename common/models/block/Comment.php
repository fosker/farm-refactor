<?php

namespace common\models\block;

use Yii;

use common\models\Block;
use common\models\User;

/**
 * This is the model class for table "block_comments".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property integer $block_id
 * @property string $date_add
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'block_comments';
    }

    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'add'=> ['comment','block_id'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment', 'block_id', 'user_id'], 'required'],
            [['comment'], 'string', 'max'=>400],
            [['user_id'], 'exist', 'targetClass'=>User::className(), 'targetAttribute'=>'id'],
            [['block_id'], 'exist', 'targetClass'=>Block::className(), 'targetAttribute'=>'id'],
        ];
    }

    public function fields() {
        if($this->scenario == 'default')
            return [
                'id',
                'user',
                'comment',
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
            'comment' => 'Комментарий',
            'block_id' => 'Блок',
            'date_add' => 'Дата добавления',
        ];
    }

    /**
     * @return \yii\db\Query
     */
    public static function findByBlock($block_id) {
        return static::find()->where(['block_id'=>$block_id])->orderBy(['date_add'=>SORT_DESC]);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getBlock() {
        return $this->hasOne(Block::className(), ['id' => 'block_id']);
    }

    public static function CountForBlock($block_id)
    {
        return static::findByBlock($block_id)->count();
    }

}
