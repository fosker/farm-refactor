<?php

namespace backend\models\admin;

use Yii;

/**
 * This is the model class for table "right".
 *
 * @property integer $id
 * @property string $name
 * @property string $action
 */
class Right extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'administrator_rights';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'action'], 'required'],
            [['name', 'action'], 'string', 'max' => 255]
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
            'action' => 'Действие',
        ];
    }

    public static function HasAdmin($id, $url) {
        $rule = HasRight::find()->joinWith('right')->where(['admin_id'=>$id,'action'=>$url])->one();
        if($rule->value == 1) return true;
        else return false;
    }
}
