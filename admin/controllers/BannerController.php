<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use common\models\Presentation;
use common\models\Seminar;
use common\models\Survey;
use common\models\Banner;
use common\models\Company;
use common\models\Stock;
use common\models\Item;
use common\models\location\City;
use common\models\location\Region;
use common\models\company\Pharmacy;
use common\models\banner\Education as Banner_Education;
use common\models\banner\Type as Banner_Type;
use common\models\profile\Type;
use common\models\profile\Education;
use common\models\Factory;
use backend\models\banner\Search;
use common\models\News;


class BannerController extends Controller
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
            'positions' => Banner::positions(),
            'pages' => Banner::pages(),
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
            'pharmacies'=>ArrayHelper::map(Pharmacy::find()->asArray()->all(),'id','name'),
            'types' => ArrayHelper::map(Type::find()->asArray()->all(),'id','name'),
            'companies' => ArrayHelper::map(Company::find()->asArray()->all(),'id','title'),
            'titles'=>ArrayHelper::map(Banner::find()->asArray()->all(), 'title','title'),
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
        $model = new Banner();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->save(false)) {
                $model->loadPharmacies(Yii::$app->request->post('pharmacies'));
                $model->loadEducation(Yii::$app->request->post('education'));
                $model->loadTypes(Yii::$app->request->post('types'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'education' => Education::find()->asArray()->all(),
                'regions'=>Region::find()->asArray()->all(),
                'types'=>Type::find()->asArray()->all(),
                'cities'=>City::find()->all(),
                'companies'=>Company::find()->asArray()->all(),
                'factories'=>ArrayHelper::map(Factory::find()->asArray()->all(), 'id','title'),
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_cities = Pharmacy::find()->select('city_id')->joinWith('bannerPharmacies')
            ->where(['banner_id' => $id])->asArray()->all();
        $old_companies = Pharmacy::find()->select('company_id')->joinWith('bannerPharmacies')
            ->where(['banner_id' => $id])->asArray()->all();
        $old_education = Banner_Education::find()->select('education_id')->where(['banner_id' => $id])->asArray()->all();
        $old_types = Banner_Type::find()->select('type_id')->where(['banner_id' => $id])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->save()) {
                $model->hide();
                if (!Yii::$app->request->post('companies') || !Yii::$app->request->post('cities')) {
                    $model->deletePharmacies();
                }
                if (Yii::$app->request->post('pharmacies')) {
                    $model->updatePharmacies(Yii::$app->request->post('pharmacies'));
                }                $model->updateEducation(Yii::$app->request->post('education'));
                $model->updateTypes(Yii::$app->request->post('types'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'education' => Education::find()->asArray()->all(),
                'regions'=>Region::find()->asArray()->all(),
                'types'=>Type::find()->asArray()->all(),
                'cities'=>City::find()->all(),
                'companies'=>Company::find()->asArray()->all(),
                'factories'=>ArrayHelper::map(Factory::find()->asArray()->all(), 'id','title'),
                'old_types' => $old_types,
                'old_cities' => $old_cities,
                'old_companies' => $old_companies,
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
        if (($model = Banner::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Баннер не найден.');
        }
    }

    public function actionLinkList($q = null, $id = null) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {

            $survey = Survey::find()->select('CONCAT("survey/",`id`) as id, CONCAT("Анкета: ",`title`) as text')->where(['like','CONCAT("Анкета: ",title)',$q])->asArray();

            $seminar = Seminar::find()->select('CONCAT("seminar/",`id`) as id, CONCAT("Семинар: ",`title`) as text')->where(['like','CONCAT("Семинар: ",title)',$q])->asArray();

            $present = Item::find()->select('CONCAT("present/",`id`) as id, CONCAT("Подарок: ",`title`) as text')->where(['like','CONCAT("Подарок: ",title)',$q])->asArray();

            $presentation = Presentation::find()->select('CONCAT("presentation/",`id`) as id, CONCAT("Презентация: ",`title`) as text')->where(['like','CONCAT("Презентация: ",title)',$q])->asArray();

            $stock = Stock::find()->select('CONCAT("stock/",`id`) as id, CONCAT("Акция: ",`title`) as text')->where(['like','CONCAT("Акция: ",title)',$q])->asArray();

            $news = News::find()->select('CONCAT("news/",`id`) as id, CONCAT("Новость: ",`title`) as text')->where(['like','CONCAT("Новость: ",title)',$q])->asArray();

            $survey->union($seminar)->union($present)->union($stock)->union($presentation)->union($news);

            $out['results'] = array_values($survey->limit(20)->all());
        }
        elseif (!is_null($id)) {
            $path = explode("/",$id);
            switch($path[0]) {
                case 'present':
                    $item = Item::findOne($path[1]);
                    break;
                case 'presentation':
                    $item = Presentation::findOne($path[1]);
                    break;
                case 'survey':
                    $item = Survey::findOne($path[1]);
                    break;
                case 'seminar':
                    $item = Seminar::findOne($path[1]);
                    break;
                case 'news':
                    $item = News::findOne($path[1]);
                    break;
                case 'stock':
                    $item = Stock::findOne($path[1]);
                    break;
            }
            $out['results'] = ['id' => $id, 'text' => $item->title];
        }

        return $out;
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
