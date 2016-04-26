<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;

use common\models\Vacancy;
use common\models\vacancy\Comment;
use common\models\vacancy\Entry;

class VacancyController extends Controller
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
            'query' => Vacancy::getForCurrentUser(),
        ]);
    }

    public function actionView($id) {
        return Vacancy::getOneForCurrentUser($id);
    }

    public function actionEntry() {
        $entry = new Entry();

        $entry->user_id = Yii::$app->user->id;

        if($entry->load(Yii::$app->request->post(),'') && $entry->save()) {
            return ['success'=>true];
        }
        return $entry;
    }

    public function actionComments($vacancy_id) {
        return new ActiveDataProvider([
            'query' => Comment::findByVacancy($vacancy_id),
        ]);
    }

    public function actionComment($id) {
        return Comment::findOne($id);
    }

    public function actionAddComment() {
        $comment = new Comment(['scenario'=>'add']);

        if($comment->load(Yii::$app->request->getBodyParams(), '') && $comment->validate(['comment','vacancy_id'])) {
            $comment->user_id = Yii::$app->user->id;
            $comment->save();
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