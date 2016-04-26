<?php

namespace backend\controllers;

use Yii;
use common\models\Vacancy;
use backend\models\vacancy\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\agency\Firm;
use common\models\location\City;
use common\models\vacancy\City as Vacancy_City;
use common\models\vacancy\Pharmacy as Vacancy_Pharmacy;
use yii\web\UploadedFile;
use common\models\agency\Pharmacy;

class VacancyController extends Controller
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
            'vacancies' => ArrayHelper::map(Vacancy::find()->asArray()->all(),'title','title'),
            'emails' => ArrayHelper::map(Vacancy::find()->asArray()->all(),'email','email'),
            'firms' => ArrayHelper::map(Firm::find()->asArray()->all(),'id','name'),
            'cities'=>ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
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
        $model = new Vacancy();
        $model->scenario = 'create';
        $vacancy_cities = new Vacancy_City();
        $vacancy_pharmacies = new Vacancy_Pharmacy();

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
                'cities'=>City::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'vacancy_cities' => $vacancy_cities,
                'vacancy_pharmacies' => $vacancy_pharmacies
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $vacancy_cities = new Vacancy_City();
        $vacancy_pharmacies = new Vacancy_Pharmacy();

        $old_cities = Vacancy_City::find()->select('city_id')->where(['vacancy_id' => $id])->asArray()->all();
        $old_pharmacies = Vacancy_Pharmacy::find()->select('pharmacy_id')->where(['vacancy_id' => $id])->asArray()->all();

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
                'cities'=>City::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'vacancy_cities' => $vacancy_cities,
                'vacancy_pharmacies' => $vacancy_pharmacies,
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
        if (($model = Vacancy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
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
