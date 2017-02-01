<?php

namespace backend\controllers;

use Yii;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\Factory;
use common\models\Theme;
use backend\models\theme\Search;

class ThemeController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'user'=>'admin',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->admin->identity->can($action);
                        }
                    ],
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
            'emails'=>ArrayHelper::map(Theme::find()->asArray()->all(),'email','email'),
            'factories' => ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
            'titles'=>ArrayHelper::map(Theme::find()->asArray()->all(), 'title','title'),
        ]);
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Theme();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(!$model->form_id)
                $model->form_id = 0;
            if (Yii::$app->request->post('forList')) {
                $model->forList = implode(',',Yii::$app->request->post('forList'));
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'factories'=> ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_lists = explode(',', $model->forList);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(!$model->form_id)
                $model->form_id = 0;
            if (Yii::$app->request->post('forList')) {
                $model->forList = implode(',',Yii::$app->request->post('forList'));
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'factories'=> ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
                'old_lists' => $old_lists
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Theme::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Тема не найдена.');
        }
    }
}
