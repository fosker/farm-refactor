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
use common\models\Company;
use common\models\location\City;
use common\models\location\Region;
use common\models\company\Pharmacy;
use common\models\presentation\Education as Presentation_Education;
use common\models\presentation\Type as Presentation_Type;
use common\models\profile\Type;
use common\models\profile\Education;
use common\models\Factory;
use backend\models\presentation\Search;




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
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
            'pharmacies'=>ArrayHelper::map(Pharmacy::find()->asArray()->all(),'id','name'),
            'types' => ArrayHelper::map(Type::find()->asArray()->all(),'id','name'),
            'companies' => ArrayHelper::map(Company::find()->asArray()->all(),'id','title'),
            'titles'=>ArrayHelper::map(Presentation::find()->asArray()->all(), 'title','title'),
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

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->imageFile = UploadedFile::getInstance($model,'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model,'thumbFile');
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

        $old_cities = Pharmacy::find()->select('city_id')->joinWith('presentationPharmacies')
            ->where(['presentation_id' => $id])->asArray()->all();
        $old_companies = Pharmacy::find()->select('company_id')->joinWith('presentationPharmacies')
            ->where(['presentation_id' => $id])->asArray()->all();
        $old_education = Presentation_Education::find()->select('education_id')->where(['presentation_id' => $id])->asArray()->all();
        $old_types = Presentation_Type::find()->select('type_id')->where(['presentation_id' => $id])->asArray()->all();

        if($model->load(Yii::$app->request->getBodyParams())) {
            $model->imageFile = UploadedFile::getInstance($model,'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model,'thumbFile');
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

    public function actionAddSlide($presentation_id)
    {
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

    public function actionEditSlide($id)
    {
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

    public function actionDeleteSlide($id)
    {
        $model = $this->findSlideModel($id);
        $model->delete();
        return $this->redirect(['view','id'=>$model->presentation_id]);
    }

    public function findSlideModel($id)
    {
        if (($model = Slide::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Слайд не найден.');
        }
    }

    public function actionAddQuestion($presentation_id)
    {
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

    public function actionEditQuestion($id)
    {
        $model = $this->findQuestionModel($id);

        if($model->load(Yii::$app->request->getBodyParams()) && $model->save()) {
            return $this->redirect(['view','id'=>$model->presentation_id]);
        }

        return $this->render('question/update', [
            'model'=>$model
        ]);
    }

    public function actionDeleteQuestion($id)
    {
        $model = $this->findQuestionModel($id);
        $model->delete();
        return $this->redirect(['view','id'=>$model->presentation_id]);
    }

    public function findQuestionModel($id)
    {
        if (($model = Question::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Вопрос не найден.');
        }
    }

    public function actionViewOption($question_id)
    {
        return $this->render('question/option/index', [
            'options'=>Option::findAllByQuestionId($question_id)->all(),
        ]);
    }

    public function actionAddOption($question_id)
    {
        $model = new Option();

        if($model->load(Yii::$app->request->getBodyParams())) {
            $question = Question::findOne($question_id);
            $question->validAnswer = "";
            $question->save(false);
            $model->question_id = $question_id;
            if($model->save())
                return $this->redirect(['view-option','question_id'=>$question_id,
                    'presentation_id' => Yii::$app->request->get('presentation_id')]);
        }

        return $this->render('question/option/create', [
            'model'=>$model
        ]);
    }

    public function actionEditOption($id)
    {
        $model = $this->findOptionModel($id);

        if($model->load(Yii::$app->request->getBodyParams()) && $model->save()) {
            return $this->redirect(['view-option','question_id'=>$model->question_id,
                'presentation_id' => Yii::$app->request->get('presentation_id')]);
        }

        return $this->render('question/option/update', [
            'model'=>$model
        ]);
    }

    public function actionDeleteOption($id)
    {
        $model = $this->findOptionModel($id);
        $model->delete();
        return $this->redirect(['view-option','question_id'=>$model->question_id,
            'presentation_id' => Yii::$app->request->get('presentation_id')]);
    }

    public function findOptionModel($id)
    {
        if (($model = Option::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Вариант ответа не найден.');
        }
    }

}
