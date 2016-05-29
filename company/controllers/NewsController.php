<?php


namespace company\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use common\models\news\Comment;
use common\models\News;
use common\models\location\City;
use common\models\location\Region;
use common\models\company\Pharmacy;
use common\models\news\Education as News_Education;
use common\models\news\Type as News_Type;
use common\models\news\Pharmacy as News_Pharmacy;
use common\models\profile\Type;
use common\models\profile\Education;
use common\models\Company;
use common\models\Factory;
use company\models\search\news\Search;


class NewsController extends Controller
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
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
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
            'news' => ArrayHelper::map(News::find()
                ->joinWith('pharmacies')
                ->joinWith('types')
                ->join('LEFT JOIN', Pharmacy::tableName(),
                    Pharmacy::tableName().'.id = '.News_Pharmacy::tableName().'.pharmacy_id')
                ->where(['company_id' => Yii::$app->user->identity->company_id])
                ->andWhere(['type_id' => 1])
                ->groupBy(News::tableName().'.id')->asArray()->all(),'title','title'),
            'education' => ArrayHelper::map(Education::find()
                ->asArray()->all(),'id','name'),
            'companies' => ArrayHelper::map(Company::find()->asArray()->all(),'id','title'),
        ]);
    }

    public function actionView($id)
    {
        $comment = new Comment();
        $comment->admin_type = 1;
        $comment->news_id = $id;
        $comment->user_id = Yii::$app->user->id;
        if($comment->load(Yii::$app->request->post())) {
            $comment->save(false);
            $this->refresh();
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new News();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save(false)) {
                $model->loadPharmacies(Yii::$app->request->post('pharmacies'));
                $model->loadEducation(Yii::$app->request->post('education'));
                $type = new News_Type();
                $type->type_id = Type::TYPE_PHARMACIST;
                $type->news_id = $model->id;
                $type->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'education' => Education::find()->asArray()->all(),
                'regions'=>Region::find()->asArray()->all(),
                'cities'=>City::find()->all(),
                'pharmacies'=>Pharmacy::find()
                    ->where(['company_id' => Yii::$app->user->identity->company_id])
                    ->asArray()
                    ->all(),
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_cities = Pharmacy::find()->select('city_id')->joinWith('newsPharmacies')
            ->where(['news_id' => $id])->asArray()->all();

        $old_pharmacies = News_Pharmacy::find()->select('pharmacy_id')->where(['news_id' => $id])->asArray()->all();

        $old_education = News_Education::find()->select('education_id')->where(['news_id' => $id])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                if(Yii::$app->request->post('pharmacies')) {
                    $model->updatePharmacies(Yii::$app->request->post('pharmacies'));
                }
                $model->updateEducation(Yii::$app->request->post('education'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'education' => Education::find()->asArray()->all(),
                'regions'=>Region::find()->asArray()->all(),
                'cities'=>City::find()->all(),
                'pharmacies'=>Pharmacy::find()
                    ->where(['company_id' => Yii::$app->user->identity->company_id])
                    ->asArray()->all(),
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
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Новость не найдена. ');
        }
    }
}
