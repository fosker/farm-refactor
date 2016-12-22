<?php

namespace common\models\survey;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Survey;
/**
 * This is the model class for table "survey_view_start".
 *
 * @property integer $user_id
 * @property integer $survey_id
 * @property string $date_start
 */
class Start extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_view_start';
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
