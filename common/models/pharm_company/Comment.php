<?php

namespace common\models\pharm_company;

use Yii;
use backend\models\Admin;

/**
 * This is the model class for table "pharm_company_comments".
 *
 * @property integer $id
 * @property integer $pharm_company_id
 * @property string $date_add
 * @property string $text
 * @property integer $author_id
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pharm_company_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pharm_company_id', 'text', 'author_id'], 'required'],
            [['pharm_company_id', 'author_id'], 'integer'],
            [['date_add'], 'safe'],
            [['text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pharm_company_id' => 'Фарм. компания',
            'date_add' => 'Дата добавления',
            'text' => 'Комментарий',
            'author_id' => 'Автор',
        ];
    }

    public function getAdmin()
    {
        return $this->hasOne(Admin::className(),['id'=>'author_id']);
    }
}
