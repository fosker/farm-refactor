<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use common\models\Seminar;
use common\models\location\City;
use common\models\agency\Pharmacy;
use common\models\seminar\City as Seminar_City;
use common\models\seminar\Pharmacy as Seminar_Pharmacy;
use common\models\seminar\Education as Seminar_Education;
use common\models\agency\Firm;
use common\models\User;
use backend\models\seminar\Search;
use common\models\profile\Education;

class SeminarController extends Controller
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
            'seminars' => ArrayHelper::map(Seminar::find()->asArray()->all(),'title','title'),
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
            'emails' => ArrayHelper::map(Seminar::find()->asArray()->all(),'email','email'),
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
        $model = new Seminar();
        $model->scenario = 'create';
        $seminar_cities = new Seminar_City();
        $seminar_pharmacies = new Seminar_Pharmacy();
        $seminar_education = new Seminar_Education();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                $model->loadCities(Yii::$app->request->post('cities'));
                $model->loadPharmacies(Yii::$app->request->post('pharmacies'));
                $model->loadEducation(Yii::$app->request->post('education'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'cities'=>City::find()->asArray()->all(),
                'education' => Education::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'seminar_cities' => $seminar_cities,
                'seminar_pharmacies' => $seminar_pharmacies,
                'seminar_education' => $seminar_education
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $seminar_cities = new Seminar_City();
        $seminar_pharmacies = new Seminar_Pharmacy();
        $seminar_education = new Seminar_Education();

        $old_cities = Seminar_City::find()->select('city_id')->where(['seminar_id' => $id])->asArray()->all();
        $old_pharmacies = Seminar_Pharmacy::find()->select('pharmacy_id')->where(['seminar_id' => $id])->asArray()->all();
        $old_education = Seminar_Education::find()->select('education_id')->where(['seminar_id' => $id])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                $model->updateCities(Yii::$app->request->post('cities'));
                $model->updatePharmacies(Yii::$app->request->post('pharmacies'));
                $model->updateEducation(Yii::$app->request->post('education'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'cities'=>City::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'education' => Education::find()->asArray()->all(),
                'seminar_cities' => $seminar_cities,
                'seminar_pharmacies' => $seminar_pharmacies,
                'seminar_education' => $seminar_education,
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
        if (($model = Seminar::findOne($id)) !== null) {
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
