<?php

namespace backend\controllers;

use backend\models\Admin;
use common\models\pharm_company\Comment;
use Yii;
use common\models\PharmCompany;
use backend\models\pharm_company\Search;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class PharmCompanyController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'add-comment' => ['POST']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'admins' => ArrayHelper::map(Admin::find()->asArray()->all(),'id','name'),
        ]);
    }

    public function actionAddComment()
    {
        $comment = new Comment();

        if ($comment->load(Yii::$app->request->post()) && $comment->save()) {
            return $this->redirect(['view', 'id' => $comment->pharm_company_id]);
        }
    }

    public function actionView($id)
    {
        $comment = new Comment();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'comment' => $comment,
        ]);
    }

    public function actionCreate()
    {
        $model = new PharmCompany();
        $model->admin_id = Yii::$app->admin->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->admin_id != Yii::$app->admin->id) {
            throw new ForbiddenHttpException;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->admin_id != Yii::$app->admin->id) {
            throw new ForbiddenHttpException;
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = PharmCompany::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}