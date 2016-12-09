<?php

namespace common\models\news;

use common\models\News;

class ForList extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'news_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id', 'list'], 'required'],
            [['news_id', 'list'], 'integer']
        ];
    }

    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }
}