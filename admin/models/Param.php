<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "param".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 */
class Param extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'param';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'value' => 'Значение',
        ];
    }

    /**
     * Returns param's value by param's name
     * @return string
     */
    public static function getParam($name) {
        return static::findOne(['name'=>$name])->value;
    }
}
