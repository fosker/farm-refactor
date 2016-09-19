<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use common\models\news\View;
use common\models\News;
use common\models\location\City;
use common\models\location\Region;
use common\models\company\Pharmacy;
use common\models\news\Education as News_Education;
use common\models\news\Type as News_Type;
use common\models\profile\Type;
use common\models\profile\Education;
use common\models\Company;
use common\models\Factory;
use common\models\news\Comment;
use backend\models\news\Search;


class NewsController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'delete-comment' => ['post']
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
            'news' => ArrayHelper::map(News::find()->asArray()->all(),'title','title'),
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
            'pharmacies'=>ArrayHelper::map(Pharmacy::find()->asArray()->all(),'id','name'),
            'types' => ArrayHelper::map(Type::find()->asArray()->all(),'id','name'),
            'companies' => ArrayHelper::map(Company::find()->asArray()->all(),'id','title'),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionStatistics($id)
    {
        $model = $this->findModel($id);
        $days_views = ArrayHelper::map(View::find()
            ->select('news_id, dayofweek(date) as day, count(news_id) as count')
            ->where(['news_id' => $id])
            ->groupBy('news_id, day')
            ->asArray()
            ->all(), 'day', 'count');
        $hours_views = ArrayHelper::map(View::find()
            ->select('news_id, hour(date) as hour, count(news_id) as count')
            ->where(['news_id' => $id])
            ->groupBy('news_id, hour')
            ->asArray()
            ->all(), 'hour', 'count');

        return $this->render('stats', [
            'model' => $model,
            'days_views' => $days_views,
            'hours_views' => $hours_views
        ]);
    }

    public function actionCreate()
    {
        $model = new News();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
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
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'companies'=>Company::find()->asArray()->all(),
                'factories'=>ArrayHelper::map(Factory::find()->asArray()->all(), 'id','title'),
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_cities = Pharmacy::find()->select('city_id')->joinWith('newsPharmacies')
            ->where(['news_id' => $id])->asArray()->all();
        $old_companies = Pharmacy::find()->select('company_id')->joinWith('newsPharmacies')
            ->where(['news_id' => $id])->asArray()->all();
        $old_education = News_Education::find()->select('education_id')->where(['news_id' => $id])->asArray()->all();
        $old_types = News_Type::find()->select('type_id')->where(['news_id' => $id])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                if(Yii::$app->request->post('pharmacies')) {
                    $model->updatePharmacies(Yii::$app->request->post('pharmacies'));
                }
                $model->updateEducation(Yii::$app->request->post('education'));
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
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'companies'=>Company::find()->asArray()->all(),
                'factories'=>ArrayHelper::map(Factory::find()->asArray()->all(), 'id','title'),
                'old_types' => $old_types,
                'old_cities' => $old_cities,
                'old_companies' => $old_companies,
                'old_education' => $old_education
            ]);
        }
    }

    public function actionDeleteComment($id)
    {
        $model = Comment::findOne($id);
        $news_id = $model->news_id;
        $model->delete();
        $this->redirect(['view', 'id' => $news_id]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Новость не найдена. ');
        }
    }
}
