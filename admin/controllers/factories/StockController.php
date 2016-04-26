<?php

namespace backend\controllers\factories;

use Yii;
use common\models\factory\Stock;
use backend\models\factory\stock\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\Factory;
use common\models\location\City;
use common\models\agency\Pharmacy;
use common\models\factory\City as Stock_City;
use common\models\factory\Pharmacy as Stock_Pharmacy;
use common\models\factory\Education as Stock_Education;
use common\models\profile\Education;
use common\models\agency\Firm;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

class StockController extends Controller
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
            'factories'=> ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
            'firms' => ArrayHelper::map(Firm::find()->asArray()->all(),'id','name'),
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
            'cities'=>ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
            'titles'=>ArrayHelper::map(Stock::find()->asArray()->all(), 'title','title'),
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
        $model = new Stock();
        $model->scenario = 'create';
        $stock_cities = new Stock_City();
        $stock_pharmacies = new Stock_Pharmacy();
        $stock_education = new Stock_Education();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->save()) {
                $model->loadCities(Yii::$app->request->post('cities'));
                $model->loadPharmacies(Yii::$app->request->post('pharmacies'));
                $model->loadEducation(Yii::$app->request->post('education'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'factories'=> ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
                'education' => Education::find()->asArray()->all(),
                'cities'=>City::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'stock_cities' => $stock_cities,
                'stock_pharmacies' => $stock_pharmacies,
                'stock_education' => $stock_education
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $stock_cities = new Stock_City();
        $stock_pharmacies = new Stock_Pharmacy();
        $stock_education = new Stock_Education();

        $old_cities = Stock_City::find()->select('city_id')->where(['stock_id' => $id])->asArray()->all();
        $old_pharmacies = Stock_Pharmacy::find()->select('pharmacy_id')->where(['stock_id' => $id])->asArray()->all();
        $old_education = Stock_Education::find()->select('education_id')->where(['stock_id' => $id])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->save()) {
                $model->updateCities(Yii::$app->request->post('cities'));
                $model->updatePharmacies(Yii::$app->request->post('pharmacies'));
                $model->updateEducation(Yii::$app->request->post('education'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'factories'=> ArrayHelper::map(Factory::find()->asArray()->all(),'id','title'),
                'cities'=>City::find()->asArray()->all(),
                'education' => Education::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'stock_cities' => $stock_cities,
                'stock_pharmacies' => $stock_pharmacies,
                'stock_education' => $stock_education,
                'old_cities' => $old_cities,
                'old_pharmacies' => $old_pharmacies,
                'old_education' => $old_education
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
        if (($model = Stock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена');
        }
    }

    public function actionApprove($id)
    {
        $this->findModel($id)->approve();

        return $this->redirect(['index']);
    }

    public function actionHide($id)
    {
        $this->findModel($id)->hide();

        return $this->redirect(['index']);
    }
}
