<?php

namespace common\models\forms;

use Yii;
/**
 * This is the model class for table "form_sections".
 *
 * @property integer $id
 * @property integer $form_id
 * @property string $title
 * @property string $description
 */
class Section extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'form_sections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['form_id', 'title'], 'required'],
            [['form_id'], 'integer'],
            [['title', 'description'], 'string']
        ];
    }

    public function fields()
    {
        return [
            'id',
            'title',
            'description',
            'fields'
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
            'title' => 'Заголовок',
            'description' => 'Описание',
        ];
    }

    public function getFields()
    {
        $not_in = Relation::find()->select('child_id');
        return Field::find()->where(['section_id' => $this->id])->andWhere(['not in', 'id', $not_in])->all();
    }
}
