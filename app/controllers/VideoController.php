<?php

namespace app\controllers;

use app\models\video\Comment;
use Yii;
use yii\web\Controller;
use app\models\Video;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class VideoController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = Video::find()->orderBy('id');

        $provider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $this->render('index', [
            'dataProvider' => $provider,
        ]);
    }

    public function actionView($id)
    {
        $comment = new Comment();

        if($comment->load(Yii::$app->request->post())) {
            $comment->user_id = Yii::$app->user->id;
            $comment->video_id = $id;
            $comment->save();
            return $this->refresh();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'comment' => $comment
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Video::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Видео не найдено. ');
        }
    }

}