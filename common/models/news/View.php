<?php

namespace common\models\news;

use Yii;

/**
 * This is the model class for table "news_views".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $news_id
 * @property string $date
 */
class View extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_views';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'news_id'], 'required'],
            [['user_id', 'news_id'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'news_id' => 'News ID',
            'date' => 'Date',
        ];
    }
}
