<?php

namespace common\models;

use Yii;
use common\models\video\Comment;
/**
 * This is the model class for table "videos".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $tags
 * @property string $link
 */
class Video extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'videos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'link'], 'required'],
            [['description', 'tags'], 'string'],
            [['title', 'link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название видео',
            'description' => 'Описание',
            'tags' => 'Тэги',
            'link' => 'Ссылка',
        ];
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['video_id' => 'id'])->orderBy('date_add ASC');
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Comment::deleteAll(['video_id'=>$this->id]);
    }
}
