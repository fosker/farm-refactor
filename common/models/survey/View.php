<?php

namespace common\models\survey;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Survey;
use common\models\survey\Answer;
/**
 * This is the model class for table "survey_views".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $survey_id
 * @property string $added
 */
class View extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_views';
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
            'added' => 'Дата добавления',
        ];
    }

    /**
     * @return \yii\db\Query
     */
    public static function findByCurrentUser()
    {
        return static::find()->where(['user_id'=>Yii::$app->user->id]);
    }

    public function getUser() {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    public function getSurvey() {
        return $this->hasOne(Survey::className(),['id'=>'survey_id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['view_id' => 'id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Answer::deleteAll(['view_id' => $this->id]);
    }

    public static function addByCurrentUser($answers)
    {
        $view = new static();
        $view->user_id = Yii::$app->user->id;
        $view->survey_id = reset($answers)->question->survey_id;
        $view->save(false);
        $survey = Survey::findOne($view->survey_id);
        $survey->updateCounters(['views_limit' => -1]);
        foreach($answers as $answer) {
            $answer->view_id = $view->id;
            $answer->save(false);
        }
    }
}
