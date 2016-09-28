<?php

namespace common\models\forms;

use Yii;

/**
 * This is the model class for table "form_section_field_options".
 *
 * @property integer $id
 * @property integer $field_id
 * @property string $value
 */
class Option extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'form_section_field_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field_id', 'value'], 'required'],
            [['field_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'value'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_id' => 'Поле',
            'value' => 'Значение',
        ];
    }
}
