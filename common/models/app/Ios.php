<?php

namespace common\models\app;

use Yii;

/**
 * This is the model class for table "app_ios_version".
 *
 * @property integer $id
 * @property string $version
 * @property integer $is_forced
 * @property string $message
 */
class Ios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_ios_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version'], 'required'],
            [['is_forced'], 'integer'],
            [['version'], 'string', 'max' => 10],
            [['message'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Версия',
            'is_forced' => 'Обязательная',
            'message' => 'Сообщение'
        ];
    }
}
