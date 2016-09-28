<?php

namespace common\models\forms;

use Yii;
/**
 * This is the model class for table "form_section_field_ralations".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $child_id
 * @property integer $active_option_id
 */
class Relation extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'form_section_field_relations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'child_id', 'active_option_id'], 'required'],
            [['field_id', 'child_id', 'active_option_id'], 'integer'],
        ];
    }

    public function fields()
    {
        return [
            'related_field',
            'active_option_id'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    public function getRelated_Field()
    {
        return Field::findOne($this->child_id);
    }

}
