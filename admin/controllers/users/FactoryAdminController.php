<?php


namespace backend\controllers\users;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use backend\models\profile\factory_admins\Search;
use common\models\Company;
use common\models\Factory;
use common\models\factory\Admin;

class FactoryAdminController extends Controller
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
            'names' => ArrayHelper::map(Admin::find()->asArray()->all(), 'name', 'name'),
            'factories' => ArrayHelper::map(Factory::find()->asArray()->all(), 'id', 'title'),
            'emails' => ArrayHelper::map(Admin::find()->asArray()->all(), 'email', 'email'),
        ]);
    }


    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new Admin();
        $model->scenario = 'join';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->register();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'factories'=> ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->save(false))
                return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'factories'=> ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
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
        if (($model = Admin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена. ');
        }
    }

    public function actionAccept($id)
    {

        $model = $this->findModel($id);
        $model->verified();
        return $this->redirect(['index']);
    }

    public function actionBan($id)
    {
        $model = $this->findModel($id);
        $model->ban();
        return $this->redirect(['index']);
    }

}