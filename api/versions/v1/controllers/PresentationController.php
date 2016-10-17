<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use rest\components\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use common\models\presentation\Comment;
use common\models\presentation\View;
use common\models\Presentation;
use common\models\presentation\Answer;
use common\models\presentation\Unique;

class PresentationController extends Controller
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

    public function actionHomeList()
    {
        return new ActiveDataProvider([
            'query' => Presentation::getForCurrentUser()
                ->andWhere(['home'=>1])
                ->andWhere(['or', ['!=', 'views_limit', '0'], [
                    'exists',
                    View::findByCurrentUser()
                        ->andWhere(View::tableName().'.presentation_id='.Presentation::tableName().'.id')
                ]])
                ->orderBy(['home_priority'=>SORT_DESC]),
        ]);
    }

    public function actionViewedList()
    {
        return new ActiveDataProvider([
            'query' => Presentation::getViewedForCurrentUser(),
        ]);
    }

    public function actionNotViewedList()
    {
        return new ActiveDataProvider([
            'query' => Presentation::getNotViewedForCurrentUser(),
        ]);
    }

    public function actionView($id) {
        if(!Unique::find()->where(['presentation_id' => $id, 'user_id' => Yii::$app->user->id])->exists()) {
            $view = new Unique();
            $view->presentation_id = $id;
            $view->user_id = Yii::$app->user->id;
            $view->save();
        }
        return Presentation::getOneForCurrentUser($id);
    }

    public function actionIsPresentationViewed($id) {
        return ['viewed'=>Presentation::isViewedByCurrentUser($id)];
    }

    public function actionAnswer() {

        $answers = [new Answer()];
        for($i = 1; $i < count($_POST['answer']); $i++) {
            $answers[] = new Answer();
        }

        if(Answer::loadMultiple($answers,$_POST,'answer')) {
            if(!View::find()->where(['user_id'=>Yii::$app->user->id, 'presentation_id'=>reset($answers)->question->presentation_id])->exists()) {
                $answers = Answer::filterModels($answers);
                if(Answer::validateMultiple($answers,['question_id','value'])) {
                    View::addByCurrentUser($answers);
                    Yii::$app->user->identity->viewPresentation(reset($answers)->question->presentation);
                    return ['success'=>true];
                }
            }
        }
        return $answers;
    }

    public function actionComments($presentation_id) {
        return new ActiveDataProvider([
            'query' => Comment::findByPresentation($presentation_id),
        ]);
    }

    public function actionComment($id) {
        return Comment::findOne($id);
    }

    public function actionAddComment() {
        $comment = new Comment(['scenario'=>'add']);

        if($comment->load(Yii::$app->request->getBodyParams(), '') && $comment->validate(['presentation_id','comment'])) {
            $comment->user_id = Yii::$app->user->id;
            $comment->save(false);
            return ['success'=>true];
        } else return $comment;
    }

    public function actionDeleteComment($id) {
        if($comment = Comment::findOne(['id'=>$id,'user_id'=>Yii::$app->user->id])) {
            $comment->delete();
            return ['success'=>true];
        } else {
            return ['success'=>false];
        }
    }
}