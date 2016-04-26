<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;

use common\models\News;
use common\models\news\View;
use common\models\news\Comment;

class NewsController extends Controller
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
            'query' => News::getForCurrentUser(),
        ]);
    }

    public function actionView($id) {
        $view = new View();
        $view->user_id = Yii::$app->user->id;
        $view->news_id = $id;
        if($view->save()) {
            return News::getOneForCurrentUser($id);
        }
    }

    public function actionComments($news_id) {
        return new ActiveDataProvider([
            'query' => Comment::findByNews($news_id),
        ]);
    }

    public function actionComment($id) {
        return Comment::findOne($id);
    }

    public function actionAddComment() {
        $comment = new Comment(['scenario'=>'add']);

        if($comment->load(Yii::$app->request->getBodyParams(), '') && $comment->validate(['comment','news_id'])) {
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