<?php

namespace common\models\banner;

use Yii;

/**
 * This is the model class for table "banner_for_types".
 *
 * @property integer $banner_id
 * @property integer $type_id
 */
class Type extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'banner_for_types';
    }

    public function rules()
    {
        return [
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getType()
    {
        return $this->hasOne(\common\models\profile\Type::className(),['id'=>'type_id']);
    }
}
