<?php

namespace backend\models\admin;

use Yii;

/**
 * This is the model class for table "admin_has_right".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $right_id
 * @property integer $value
 */
class HasRight extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'administrator_has_rights';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'right_id', 'value'], 'required'],
            [['admin_id', 'right_id', 'value'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => 'Админ',
            'right_id' => 'Права',
            'value' => 'Значение',
        ];
    }

    public function getRight()
    {
        return $this->hasOne(Right::className(), ['id' => 'right_id']);
    }


}
