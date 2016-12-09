<?php

namespace backend\controllers\themes;

use common\models\Theme;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\User;

use backend\models\theme\answer\Search;
use common\models\theme\Answer;

class AnswerController extends Controller
{
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
            'logins'=> ArrayHelper::map(User::find()->asArray()->all(),'login','login'),
            'names'=> ArrayHelper::map(User::find()->asArray()->all(),'name','name'),
            'themes'=> ArrayHelper::map(Theme::find()->asArray()->all(),'title','title'),
        ]);
    }

    public function actionAnswered($id)
    {
        $model = $this->findModel($id);
        $model->answered();

        return $this->redirect(['index']);
    }

    public function actionNotAnswered($id)
    {
        $model = $this->findModel($id);
        $model->not_answered();

        return $this->redirect(['index']);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionComment($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'comment';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('comment', [
                'model' => $model,
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = Answer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException;
        }
    }
}
