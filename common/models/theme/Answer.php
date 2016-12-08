<?php

namespace common\models\theme;

use common\models\Theme;
use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "theme_answers".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $theme_id
 * @property string $date_added
 * @property integer $is_answered
 * @property string $comment
 */
class Answer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'theme_answers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme_id'], 'required'],
            [['is_answered'], 'integer'],
            [['comment'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'theme_id' => 'Тема',
            'is_answered' => 'Отвечено',
            'comment' => 'Комментарий',
            'date_added' => 'Дата'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getTheme()
    {
        return $this->hasOne(Theme::className(),['id'=>'theme_id']);
    }
}
