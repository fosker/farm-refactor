<?php

namespace common\models\news;

use Yii;
use common\models\News;
use common\models\User;

use common\models\factory\Admin as FactoryAdmin;
use common\models\company\Admin as CompanyAdmin;

/**
 * This is the model class for table "news_comments".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property integer $news_id
 * @property string $date_add
 * @property integer $admin_type
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'add'=> ['comment','news_id'],
        ]);
    }

    public static function tableName()
    {
        return 'news_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment', 'news_id'], 'required'],
            [['comment'], 'string', 'max'=>400],
            [['news_id'], 'exist', 'targetClass'=>News::className(), 'targetAttribute'=>'id'],
            [['user_id'], 'exist', 'targetClass'=>User::className(), 'targetAttribute'=>'id'],
            ['isAdmin', 'integer']
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
                },
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
            'news_id' => 'Новость',
            'date_add' => 'Дата добавления',
        ];
    }

    public static function findByNews($news_id)
    {
        return static::find()->where(['news_id'=>$news_id])->orderBy(['date_add'=>SORT_DESC]);
    }

    public function getUser() {
        if($this->admin_type == 1) {
            return $this->hasOne(FactoryAdmin::className(), ['id' => 'user_id']);
        } elseif($this->admin_type == 2) {
            return $this->hasOne(CompanyAdmin::className(), ['id' => 'user_id']);
        } else
            return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getNews() {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }
}
