<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use rest\components\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use common\models\survey\Answer;
use common\models\survey\View;
use common\models\Survey;
use common\models\survey\Unique;
use common\models\survey\Start;

class SurveyController extends Controller
{

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                    QueryParamAuth::className(),
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return new ActiveDataProvider([
            'query' => Survey::getForCurrentUser(),
        ]);
    }

    public function actionView($id) {
        if(!Unique::find()->where(['survey_id' => $id, 'user_id' => Yii::$app->user->id])->exists()) {
            $view = new Unique();
            $view->survey_id = $id;
            $view->user_id = Yii::$app->user->id;
            $view->save();
        }
        $start = Start::find()->where(['user_id' => Yii::$app->user->id, 'survey_id' => $id])->one();
        if (!$start) {
            $start = new Start();
            $start->user_id = Yii::$app->user->id;
            $start->survey_id = $id;
            $start->save();
        } else {
            $start->delete();

            $start = new Start();
            $start->user_id = Yii::$app->user->id;
            $start->survey_id = $id;
            $start->save();
        }
        return Survey::getOneForCurrentUser($id);
    }

    public function actionAnswer() {

        $answers = [new Answer()];
        for($i = 1; $i < count($_POST['answer']); $i++) {
            $answers[] = new Answer();
        }

        if(Answer::loadMultiple($answers,$_POST,'answer')) {
            $answers = Answer::filterModels($answers);
            if(!View::find()->where(['user_id'=>Yii::$app->user->id, 'survey_id'=>reset($answers)->question->survey_id])->exists()) {
                if(Answer::validateMultiple($answers,['question_id','value'])) {
                    View::addByCurrentUser($answers);
                    Yii::$app->user->identity->answerSurvey(reset($answers)->question->survey);
                    return ['success'=>true];
                }
            }
        }
        return $answers;
    }

    public function actionIsSurveyAnswered($id) {
        return ['answered'=>Survey::isAnsweredByCurrentUser($id)];
    }
}