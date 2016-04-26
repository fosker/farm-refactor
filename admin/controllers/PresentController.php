<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\shop\Vendor;
use common\models\location\City;
use common\models\agency\Pharmacy;
use common\models\shop\City as Item_City;
use common\models\shop\Pharmacy as Item_Pharmacy;
use common\models\agency\Firm;
use common\models\Item;
use backend\models\present\Search;


class PresentController extends Controller
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
            'vendors'=>ArrayHelper::map(Vendor::find()->asArray()->all(), 'id','name'),
            'firms' => ArrayHelper::map(Firm::find()->asArray()->all(),'id','name'),
            'cities'=>ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
            'titles'=>ArrayHelper::map(Item::find()->asArray()->all(), 'title','title'),
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
        $model = new Item();
        $model->scenario = 'create';
        $item_cities = new Item_City();
        $item_pharmacies = new Item_Pharmacy();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                $model->loadCities(Yii::$app->request->post('cities'));
                $model->loadPharmacies(Yii::$app->request->post('pharmacies'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'vendors'=>ArrayHelper::map(Vendor::find()->asArray()->all(), 'id','name'),
                'cities'=>City::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'item_cities' => $item_cities,
                'item_pharmacies' => $item_pharmacies
            ]);

        }

    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $item_cities = new Item_City();
        $item_pharmacies = new Item_Pharmacy();

        $old_cities = Item_City::find()->select('city_id')->where(['item_id' => $id])->asArray()->all();
        $old_pharmacies = Item_Pharmacy::find()->select('pharmacy_id')->where(['item_id' => $id])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                $model->updateCities(Yii::$app->request->post('cities'));
                $model->updatePharmacies(Yii::$app->request->post('pharmacies'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'vendors'=>ArrayHelper::map(Vendor::find()->asArray()->all(), 'id','name'),
                'cities'=>City::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'item_cities' => $item_cities,
                'item_pharmacies' => $item_pharmacies,
                'old_cities' => $old_cities,
                'old_pharmacies' => $old_pharmacies
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
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена.');
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
