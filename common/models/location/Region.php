<?php

namespace common\models\location;
use Yii;

/**
 * This is the model class for table "regions".
 *
 * @property integer $id
 * @property string $name
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regions';
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
            'name' => 'Название региона'
        ];
    }

    public function afterDelete()
    {
        parent::afterDelete();
        City::deleteAll(['id'=>'region_id']);
    }

}
