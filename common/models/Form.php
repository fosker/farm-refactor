<?php

namespace common\models;

use Yii;
use common\models\forms\Section;
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
            'sections',
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

    public function getSections()
    {
        return $this->hasMany(Section::className(),['form_id'=>'id']);
    }

    public function getFields()
    {
        $fields = [];
        foreach($this->sections as $section) {
            $fields[] = $section->allFields;
        }
        return $fields;
    }
}
