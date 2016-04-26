<?php

namespace backend\controllers;

use Yii;
use common\models\News;
use backend\models\news\Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use common\models\news\City as News_City;
use common\models\news\Pharmacy as News_Pharmacy;
use common\models\news\Education as News_Education;
use common\models\location\City;
use common\models\agency\Pharmacy;
use common\models\agency\Firm;
use common\models\profile\Education;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'news' => ArrayHelper::map(News::find()->asArray()->all(),'title','title'),
            'firms' => ArrayHelper::map(Firm::find()->asArray()->all(),'id','name'),
            'cities'=> ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
        ]);
    }

    /**
     * Displays a single News model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        $model->scenario = 'create';
        $news_cities = new News_City();
        $news_pharmacies = new News_Pharmacy();
        $news_education = new News_Education();

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
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'education' => Education::find()->asArray()->all(),
                'news_cities' => $news_cities,
                'news_pharmacies' => $news_pharmacies,
                'news_education' => $news_education
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $news_cities = new News_City();
        $news_pharmacies = new News_Pharmacy();
        $news_education = new News_Education();

        $old_cities = News_City::find()->select('city_id')->where(['news_id' => $id])->asArray()->all();
        $old_pharmacies = News_Pharmacy::find()->select('pharmacy_id')->where(['news_id' => $id])->asArray()->all();
        $old_education = News_Education::find()->select('education_id')->where(['news_id' => $id])->asArray()->all();

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
                'news_cities' => $news_cities,
                'news_pharmacies' => $news_pharmacies,
                'news_education' => $news_education,
                'old_cities' => $old_cities,
                'old_pharmacies' => $old_pharmacies,
                'old_education' => $old_education
            ]);
        }
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Новость не найдена. ');
        }
    }
}
