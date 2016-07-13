<?php

namespace common\models\forms;

use Yii;
/**
 * This is the model class for table "forms_fields".
 *
 * @property integer $id
 * @property integer $form_id
 * @property integer $type
 * @property string $placeholder
 * @property string $label
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
        return 'forms_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_id', 'type'], 'required'],
            [['form_id', 'type'], 'integer'],
            [['placeholder'], 'string', 'max' => 255],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'type',
            'placeholder',
            'label',
            'options' => function() {
                if($this->options) {
                    return $this->options;
                }
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'form_id' => 'Форма',
            'type' => 'Тип поля',
            'placeholder' => 'Название поля',
        ];
    }

    public function getOptions()
    {
        return $this->hasMany(Option::className(),['field_id'=>'id']);
    }
}
