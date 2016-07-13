<?php

namespace common\models;

use Yii;
use common\models\forms\Field;

/**
 * This is the model class for table "forms".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 */
class Form extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'forms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'title',
            'fields',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'description' => 'Описание',
        ];
    }

    public function getFields()
    {
        return $this->hasMany(Field::className(),['form_id'=>'id']);
    }
}
