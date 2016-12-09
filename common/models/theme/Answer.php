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
 * @property string $phone
 * @property string $text
 * @property string $email
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

    public function scenarios()
    {
        return array_merge(
            parent::scenarios(),
            [
                'comment' => ['comment'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme_id'], 'required'],
            [['is_answered'], 'integer'],
            [['comment', 'phone', 'text', 'email'], 'string'],
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
            'date_added' => 'Дата',
            'phone' => 'Телефон',
            'text' => 'Текст',
            'email' => 'Email'
        ];
    }

    public function answered()
    {
        $this->is_answered = true;
        $this->save(false);
    }

    public function not_answered()
    {
        $this->is_answered = false;
        $this->save(false);
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
