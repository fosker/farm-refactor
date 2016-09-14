<?php

namespace common\models\survey;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Survey;
/**
 * This is the model class for table "survey_view_unique".
 *
 * @property integer $user_id
 * @property integer $survey_id
 */
class Unique extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_view_unique';
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

    /**
     * @return \yii\db\Query
     */

    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getSurvey()
    {
        return $this->hasOne(Survey::className(),['id'=>'survey_id']);
    }

}
