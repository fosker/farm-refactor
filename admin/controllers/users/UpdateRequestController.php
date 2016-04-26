<?php

namespace backend\controllers\users;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\profile\update_request\Search;
use common\models\profile\UpdateRequest;


class UpdateRequestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
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
        ]);
    }

    public function actionView($user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($user_id),
        ]);
    }

    public function actionDelete($user_id)
    {
        UpdateRequest::deleteAll(['user_id' => $user_id]);

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = UpdateRequest::findOne(['user_id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена. ');
        }
    }
} 