<?php

namespace common\models\presentation;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Presentation;

/**
 * This is the model class for table "presentation_view_start".
 *
 * @property integer $user_id
 * @property integer $presentation_id
 * @property string $date_start
 */
class Start extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'presentation_view_start';
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
