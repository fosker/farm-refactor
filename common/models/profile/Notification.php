<?php

namespace common\models\profile;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_notifications".
 *
 * @property integer $id
 * @property string $name
 */
class Notification extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
}
