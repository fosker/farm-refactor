<?php

namespace common\models\video;

use Yii;
use common\models\Video;
use common\models\User;

/**
 * This is the model class for table "videos_comments".
 *
 * @property integer $id
 * @property integer $video_id
 * @property integer $user_id
 * @property string $comment
 * @property string $date_add
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'videos_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['video_id', 'comment'], 'required'],
            [['video_id', 'user_id'], 'integer'],
            [['comment', 'date_add'], 'string'],
            [['video_id'], 'exist', 'targetClass'=>Video::className(), 'targetAttribute'=>'id'],
            [['user_id'], 'exist', 'targetClass'=>User::className(), 'targetAttribute'=>'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'video_id' => 'Видео',
            'user_id' => 'Пользователь',
            'comment' => 'Комментарий',
            'date_add' => 'Дата добавления',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getVideo()
    {
        return $this->hasOne(Video::className(), ['id' => 'video_id']);
    }
}
