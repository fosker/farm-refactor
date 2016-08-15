<?php

namespace backend\controllers;


use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\Company;
use common\models\Survey;
use common\models\survey\Question;
use common\models\survey\Option;
use common\models\location\City;
use common\models\location\Region;
use common\models\company\Pharmacy;
use common\models\survey\Education as Survey_Education;
use common\models\survey\Type as Survey_Type;
use common\models\profile\Type;
use common\models\profile\Education;
use common\models\Factory;
use backend\models\survey\Search;
use backend\base\Model;



class SurveyController extends Controller
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
            'education' => ArrayHelper::map(Education::find()->asArray()->all(),'id','name'),
            'pharmacies'=>ArrayHelper::map(Pharmacy::find()->asArray()->all(),'id','name'),
            'types' => ArrayHelper::map(Type::find()->asArray()->all(),'id','name'),
            'companies' => ArrayHelper::map(Company::find()->asArray()->all(),'id','title'),
            'titles'=>ArrayHelper::map(Survey::find()->asArray()->all(), 'title','title'),
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
        $model = new Survey();
        $model->scenario = 'create';

        $questions = [new Question];
        $options = [[new Option]];

        if ($model->load(Yii::$app->request->post())) {

            $questions = Model::createMultiple(Question::className());
            Model::loadMultiple($questions, Yii::$app->request->post());

            $optionsData['_csrf'] =  Yii::$app->request->post()['_csrf'];
            for ($i=0; $i<count($questions); $i++) {
                $optionsData['Option'] =  Yii::$app->request->post()['Option'][$i];
                $options[$i] = Model::createMultiple(Option::classname(),[] ,$optionsData);
                Model::loadMultiple($options[$i], $optionsData);
            }

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');

            if ($model->validate()) {

                if ($this->saveSurvey($model,$questions,$options)) {
                    $model->loadPharmacies(Yii::$app->request->post('pharmacies'));
                    $model->loadEducation(Yii::$app->request->post('education'));
                    $model->loadTypes(Yii::$app->request->post('types'));
                    return $this->redirect(['view', 'id'=>$model->id]);
                }
            }

        }

        return $this->render('create', [
            'model' => $model,
            'questions' => (empty($questions)) ? [new Question] : $questions,
            'options' => (empty($options)) ? [new Option] : $options,
            'education' => Education::find()->asArray()->all(),
            'regions'=>Region::find()->asArray()->all(),
            'types'=>Type::find()->asArray()->all(),
            'cities'=>City::find()->all(),
            'companies'=>Company::find()->asArray()->all(),
            'factories'=>ArrayHelper::map(Factory::find()->asArray()->all(), 'id','title'),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_cities = Pharmacy::find()->select('city_id')->joinWith('surveyPharmacies')
            ->where(['survey_id' => $id])->asArray()->all();
        $old_companies = Pharmacy::find()->select('company_id')->joinWith('surveyPharmacies')
            ->where(['survey_id' => $id])->asArray()->all();
        $old_education = Survey_Education::find()->select('education_id')->where(['survey_id' => $id])->asArray()->all();
        $old_types = Survey_Type::find()->select('type_id')->where(['survey_id' => $id])->asArray()->all();

        $oldQuestionIds = Question::find()->select('id')
            ->where(['survey_id' => $id])->asArray()->all();
        $oldQuestionIds = ArrayHelper::getColumn($oldQuestionIds,'id');
        $questions = Question::findAll(['id' => $oldQuestionIds]);
        $questions = (empty($questions)) ? [new Question] : $questions;

        $oldOptionIds = [];
        foreach ($questions as $i => $question) {
            $oldOptions = Option::findAll(['question_id' => $question->id]);
            $options[$i] = $oldOptions;
            $oldOptionIds = array_merge($oldOptionIds,ArrayHelper::getColumn($oldOptions,'id'));

            $options[$i] = empty($options[$i]) ? [new Option] : $options[$i];
        }

        if ($model->load(Yii::$app->request->post())) {
            $questions = Model::createMultiple(Question::classname(), $questions);
            Model::loadMultiple($questions, Yii::$app->request->post());
            $newQuestionIds = ArrayHelper::getColumn($questions,'id');

            $newOptionIds = [];
            $optionData['_csrf'] =  Yii::$app->request->post()['_csrf'];
            for ($i=0; $i<count($questions); $i++) {
                $optionData['Option'] =  Yii::$app->request->post()['Option'][$i];

                $options[$i] = Model::createMultiple(Option::classname(),$options[$i] ,$optionData);

                Model::loadMultiple($options[$i], $optionData);
                $newOptionIds = array_merge($newOptionIds,empty($optionData['Option']) ? [] : ArrayHelper::getColumn($optionData['Option'],'id'));
            }

            // delete removed data
            $delOptionIds = array_diff($oldOptionIds,$newOptionIds);
            if (! empty($delOptionIds)) Option::deleteAll(['id' => $delOptionIds]);
            $delQuestionIds = array_diff($oldQuestionIds,$newQuestionIds);
            if (! empty($delQuestionIds))
                foreach($delQuestionIds as $id)
                    Question::findOne($id)->delete();

            // validate all models
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            $valid = $model->validate();

            // save deposit data
            if ($valid) {
                if ($this->saveSurvey($model,$questions,$options)) {
                    if(Yii::$app->request->post('pharmacies')) {
                        $model->updatePharmacies(Yii::$app->request->post('pharmacies'));
                    }
                    $model->updateEducation(Yii::$app->request->post('education'));
                    $model->updateTypes(Yii::$app->request->post('types'));
                    return $this->redirect(['view', 'id'=>$model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'questions' => (empty($questions)) ? [new Question] : $questions,
            'options' => (empty($options)) ? [new Option] : $options,
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

    /**
     * This function saves each part of the survey dynamic form controls.
     *
     * @param $model mixed The Survey model.
     * @param $questions mixed The Question model from the survey.
     * @param $options mixed The Option model from the question.
     * @return bool Returns TRUE if successful.
     * @throws NotFoundHttpException When record cannot be saved.
     */
    protected function saveSurvey($model,$questions,$options ) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($go = $model->save(false)) {
                // loop through each question
                foreach ($questions as $i => $question) {
                    // save the question record
                    $question->survey_id = $model->id;
                    if ($go = $question->save(false)) {
                        // loop through each option
                        foreach ($options[$i] as $id => $option) {
                            // save the option record
                            $option->question_id = $question->id;
                            if (! ($go = $option->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }
            }
            if ($go) {
                $transaction->commit();
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        return $go;
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Survey::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Анкета не найдена. ');
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
