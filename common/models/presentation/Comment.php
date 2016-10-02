<?php

namespace common\models\presentation;

use Yii;
use yii\db\ActiveRecord;

use common\models\Presentation;
use common\models\User;

/**
 * This is the model class for table "presentation_comments".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property integer $presentation_id
 * @property string $date_add
 * @property string $admin_comment
 */
class Comment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'presentation_comments';
    }

    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'add'=> ['comment','presentation_id'],
            'comment' => ['admin_comment'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment', 'presentation_id', 'user_id'], 'required'],
            [['comment','admin_comment'], 'string'],
            [['presentation_id'], 'validatePresentation'],
            [['user_id'], 'exist', 'targetClass'=>User::className(), 'targetAttribute'=>'id'],
        ];
    }

    public function validatePresentation($attribute)
    {
        if (!$this->hasErrors()) {
            if (!Presentation::isViewedByCurrentUser($this->presentation_id)) {
                $this->addError($attribute, 'Вы не можете комментировать эту презентацию.');
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
            'comment' => 'Комментарий',
            'presentation_id' => 'Презентация',
            'date_add' => 'Дата добавления',
            'admin_comment' => 'Комментарий',
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

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPresentation() {
        return $this->hasOne(Presentation::className(), ['id' => 'presentation_id']);
    }

    public static function findByPresentation($presentation_id)
    {
        return static::find()->where(['presentation_id'=>$presentation_id])->orderBy('date_add desc');
    }

}
