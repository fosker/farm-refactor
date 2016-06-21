<?php

namespace app\controllers;

use Yii;
use app\models\News;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\models\news\Education;
use yii\data\ActiveDataProvider;
use app\models\news\View;
use app\models\news\Comment;

class NewsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAgent;
                        }
                    ],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $query = News::find()
            ->joinWith('education')
            ->andWhere([Education::tableName().'.education_id'=>Yii::$app->user->identity->education_id])
            ->orderBy(['id'=>SORT_DESC])
            ->groupBy(News::tableName().'.id');

        $provider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $this->render('index', [
            'dataProvider' => $provider,
        ]);
    }

    public function actionView($id) {
        $view = new View();
        $view->user_id = Yii::$app->user->id;
        $view->news_id = $id;

        $comment = new Comment();
        if($view->save()) {
            if($comment->load(Yii::$app->request->post())) {
                $comment->user_id = Yii::$app->user->id;
                $comment->news_id = $id;
                $comment->save();
                return $this->refresh();
            }
            return $this->render('view', [
                'model' => $this->findModel($id),
                'comment' => $comment
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Новость не найдена. ');
        }
    }
}
