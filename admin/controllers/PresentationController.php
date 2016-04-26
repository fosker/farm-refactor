<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use common\models\presentation\Question;
use common\models\presentation\Slide;
use common\models\presentation\Option;
use common\models\Presentation;
use common\models\agency\Firm;
use common\models\location\City;
use common\models\agency\Pharmacy;
use common\models\presentation\City as Presentation_City;
use common\models\presentation\Pharmacy as Presentation_Pharmacy;
use common\models\presentation\Education as Presentation_Education;
use backend\models\presentation\Search;
use common\models\profile\Education;



class PresentationController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-question'=>['POST'],
                    'delete-slide'=>['POST'],
                    'delete-option'=>['POST'],
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
            'firms' => ArrayHelper::map(Firm::find()->asArray()->all(),'id','name'),
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
            'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
            'titles' =>ArrayHelper::map(Presentation::find()->asArray()->all(), 'title','title'),
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
        $model = new Presentation();
        $model->scenario = 'create';
        $presentation_cities = new Presentation_City();
        $presentation_pharmacies = new Presentation_Pharmacy();
        $presentation_education = new Presentation_Education();

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->imageFile = UploadedFile::getInstance($model,'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model,'thumbFile');
            if ($model->save(false)) {
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
                'presentation_cities' => $presentation_cities,
                'presentation_pharmacies' => $presentation_pharmacies,
                'presentation_education' => $presentation_education
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $presentation_cities = new Presentation_City();
        $presentation_pharmacies = new Presentation_Pharmacy();
        $presentation_education = new Presentation_Education();

        $old_cities = Presentation_City::find()->select('city_id')->where(['presentation_id' => $id])->asArray()->all();
        $old_pharmacies = Presentation_Pharmacy::find()->select('pharmacy_id')->where(['presentation_id' => $id])->asArray()->all();
        $old_education = Presentation_Education::find()->select('education_id')->where(['presentation_id' => $id])->asArray()->all();

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->imageFile = UploadedFile::getInstance($model,'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model,'thumbFile');
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
                'presentation_cities' => $presentation_cities,
                'presentation_pharmacies' => $presentation_pharmacies,
                'presentation_education' => $presentation_education,
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
        if (($model = Presentation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Презентация не найдена.');
        }
    }

    public function actionApprove($id)
    {
        $this->findModel($id)->approve();

        return $this->redirect(['index']);
    }

    public function actionApproveHome($id)
    {
        $this->findModel($id)->approveHome();

        return $this->redirect(['index']);
    }

    public function actionHide($id)
    {
        $this->findModel($id)->hide();

        return $this->redirect(['index']);
    }

    public function actionHideHome($id)
    {
        $this->findModel($id)->hideHome();

        return $this->redirect(['index']);
    }

    public function actionAddSlide($presentation_id) {
        $model = new Slide();
        $model->scenario = 'create';

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->imageFile = UploadedFile::getInstance($model,'imageFile');
            $model->presentation_id = $presentation_id;
            if($model->validate()) {
                $model->loadImage();
                $model->save(false);
                return $this->redirect(['view','id'=>$presentation_id]);
            }
        }

        return $this->render('slide/create', [
            'model'=>$model
        ]);
    }

    public function actionEditSlide($id) {
        $model = $this->findSlideModel($id);

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->imageFile = UploadedFile::getInstance($model,'imageFile');
            if($model->validate()) {
                $model->loadImage();
                $model->save(false);
                return $this->redirect(['view','id'=>$model->presentation_id]);
            }
        }

        return $this->render('slide/update', [
            'model'=>$model
        ]);
    }

    public function actionDeleteSlide($id) {
        $model = $this->findSlideModel($id);
        $model->delete();
        return $this->redirect(['view','id'=>$model->presentation_id]);
    }

    public function findSlideModel($id) {
        if (($model = Slide::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Слайд не найден.');
        }
    }

    public function actionAddQuestion($presentation_id) {
        $model = new Question();

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->presentation_id = $presentation_id;
            if($model->save())
                return $this->redirect(['view-option', 'question_id' => $model->primaryKey, 'presentation_id'=>$presentation_id]);
        }

        return $this->render('question/create', [
            'model'=>$model
        ]);
    }

    public function actionEditQuestion($id) {
        $model = $this->findQuestionModel($id);

        if($model->load(Yii::$app->request->getBodyParams()) && $model->save()) {
            return $this->redirect(['view','id'=>$model->presentation_id]);
        }

        return $this->render('question/update', [
            'model'=>$model
        ]);
    }

    public function actionDeleteQuestion($id) {
        $model = $this->findQuestionModel($id);
        $model->delete();
        return $this->redirect(['view','id'=>$model->presentation_id]);
    }

    public function findQuestionModel($id) {
        if (($model = Question::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Вопрос не найден.');
        }
    }

    public function actionViewOption($question_id) {
        return $this->render('question/option/index', [
            'options'=>Option::findAllByQuestionId($question_id)->all(),
        ]);
    }

    public function actionAddOption($question_id) {
        $model = new Option();

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->question_id = $question_id;
            if($model->save())
                return $this->redirect(['view-option','question_id'=>$question_id,
                    'presentation_id' => Yii::$app->request->get('presentation_id')]);
        }

        return $this->render('question/option/create', [
            'model'=>$model
        ]);
    }

    public function actionEditOption($id) {
        $model = $this->findOptionModel($id);

        if($model->load(Yii::$app->request->getBodyParams()) && $model->save()) {
            return $this->redirect(['view-option','question_id'=>$model->question_id,
                'presentation_id' => Yii::$app->request->get('presentation_id')]);
        }

        return $this->render('question/option/update', [
            'model'=>$model
        ]);
    }

    public function actionDeleteOption($id) {
        $model = $this->findOptionModel($id);
        $model->delete();
        return $this->redirect(['view-option','question_id'=>$model->question_id,
            'presentation_id' => Yii::$app->request->get('presentation_id')]);
    }

    public function findOptionModel($id) {
        if (($model = Option::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Вариант ответа не найден.');
        }
    }

}
