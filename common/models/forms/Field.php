<?php

namespace common\models\forms;

use Yii;
/**
 * This is the model class for table "form_section_fields".
 *
 * @property integer $id
 * @property integer $section_id
 * @property integer $type
 * @property string $placeholder
 * @property string $label
 * @property string $isRequired
 */
class Field extends \yii\db\ActiveRecord
{

    const TYPE_TEXT = 1;
    const TYPE_RADIO = 2;
    const TYPE_TEXTAREA = 3;
    const TYPE_CHECKBOX = 4;
    const TYPE_DROPDOWN = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'form_section_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['section_id', 'type', 'label'], 'required'],
            [['section_id', 'type', 'isRequired'], 'integer'],
            [['label', 'placeholder'], 'string']
        ];
    }

    public function fields()
    {
        return [
            'id',
            'type',
            'label',
            'options',
            'relations'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_id' => 'Раздел',
            'type' => 'Тип поля',
            'isRequired' => 'Обязательное поле',
            'label' => 'Поле'
        ];
    }

    public function getOptions()
    {
        return $this->hasMany(Option::className(),['field_id'=>'id']);
    }

    public function getSection()
    {
        return $this->hasOne(Section::className(),['id'=>'section_id']);
    }

    public function getRelations()
    {
        return Relation::find()
            ->where(['parent_id' => $this->id])
            ->all();
    }
}
