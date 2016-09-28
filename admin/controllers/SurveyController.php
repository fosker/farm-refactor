<?php

namespace backend\controllers;


use common\models\survey\Answer;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use kartik\mpdf\Pdf;
use yii\helpers\FileHelper;

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
use common\models\survey\View;
use common\models\user\Pharmacist;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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


    public function actionExportRegions($id)
    {
        $model = $this->findModel($id);

        $radio_questions = $model->devidedQuestions['radio'];
        $checkbox_questions = $model->devidedQuestions['checkbox'];

        $radio_sums = Question::getRegionSums($radio_questions);
        $checkbox_sums = Question::getRegionSums($checkbox_questions);

        $radio_common = Question::transformRadioCommon($radio_questions);
        $radio_regions = Question::transformRadioRegions($radio_questions, $radio_sums);

        $checkbox_common = Question::transformCheckboxCommon($checkbox_questions, $model);
        $checkbox_regions = Question::transformCheckboxRegions($checkbox_questions, $checkbox_sums);

        FileHelper::createDirectory('temp');

        $this->generateRadioCommon($radio_common);
        $this->generateRadioRegions($radio_regions);
        $this->generateCheckboxCommon($checkbox_common);
        $this->generateCheckboxRegions($checkbox_regions);

        $this->exportPDF($model, false);

        FileHelper::removeDirectory('temp');
    }

    public function actionExportCompanies($id)
    {
        $model = $this->findModel($id);

        $radio_questions = $model->devidedQuestions['radio'];
        $checkbox_questions = $model->devidedQuestions['checkbox'];

        $radio_sums = Question::getCompanySums($radio_questions);
        $checkbox_sums = Question::getCompanySums($checkbox_questions);

        $radio_common = Question::transformRadioCommon($radio_questions);
        $radio_companies = Question::transformRadioCompanies($radio_questions, $model, $radio_sums);

        $checkbox_common = Question::transformCheckboxCommon($checkbox_questions, $model);
        $checkbox_companies = Question::transformCheckboxCompanies($checkbox_questions, $model, $checkbox_sums);

        FileHelper::createDirectory('temp');

        $this->generateRadioCommon($radio_common);
        $this->generateRadioCompanies($radio_companies, $model);

        $this->generateCheckboxCommon($checkbox_common);
        $this->generateCheckboxCompanies($checkbox_companies, $model);

        $this->exportPDF($model, true);

        FileHelper::removeDirectory('temp');
    }

    public function actionExportDocx($id)
    {
        $model = $this->findModel($id);
        $this->exportDocx($model);
    }

    private function generateRadioLegend($pData, $name)
    {
        $image = new \pImage(500,200,$pData,1);
        $image->setFontProperties([
            "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
            "FontSize"=>18,
            "R"=>0,
            "G"=>0,
            "B"=>0,
        ]);

        $pie = new \pPie($image,$pData);
        $pie->drawPieLegend(50,10,[
            "Style"=>LEGEND_NOBORDER,
            "Mode"=>LEGEND_VERTICAL,
            "FontSize"=>12,
        ]);
        $legend_name = $name . "_legend";
        $image->render("temp/".$legend_name.'.png');
    }

    private function generateCheckboxLegend($pData, $name, $options)
    {
        $height = $options * 45;
        $image = new \pImage(500,$height,$pData,1);
        $image->setFontProperties([
            "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
            "FontSize"=>18,
            "R"=>0,
            "G"=>0,
            "B"=>0,
        ]);

        $pie = new \pPie($image,$pData);
        $pie->setSliceColor(0, [
            "R"=>244,
            "G"=>67,
            "B"=>54,
        ]);
        $pie->setSliceColor(1, [
            "R"=>33,
            "G"=>150,
            "B"=>243,
        ]);
        $pie->setSliceColor(2, [
            "R"=>139,
            "G"=>195,
            "B"=>74,
        ]);
        $pie->setSliceColor(3, [
            "R"=>255,
            "G"=>235,
            "B"=>59,
        ]);
        $pie->setSliceColor(4, [
            "R"=>121,
            "G"=>85,
            "B"=>72,
        ]);
        $pie->setSliceColor(5, [
            "R"=>255,
            "G"=>87,
            "B"=>34,
        ]);
        $pie->setSliceColor(6, [
            "R"=>49,
            "G"=>27,
            "B"=>146,
        ]);
        $pie->setSliceColor(7, [
            "R"=>91,
            "G"=>12,
            "B"=>39,
        ]);
        $pie->drawPieLegend(50,20,[
            "Style"=>LEGEND_NOBORDER,
            "Mode"=>LEGEND_VERTICAL,
            "FontSize"=>12,
        ]);
        $legend_name = $name . "_legend";
        $image->render("temp/".$legend_name.'.png');
    }

    private function generateRadioCommon($questions)
    {
        foreach($questions as $question_id => $question) {
            $legend = [];
            foreach(array_keys($question) as $option) {
                $legend[] = wordwrap($option,100,"\n",0);
            }
            $data = new \pData();
            $data->addPoints(array_values($question),"ScoreA");
            $data->setSerieDescription("ScoreA","Количество ответов");

            $data->addPoints($legend,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(850,400,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>20,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setShadow(1,[
                "X"=>2,
                "Y"=>2,
                "R"=>0,
                "G"=>0,
                "B"=>0,
                "Alpha"=>50
            ]);

            $pie = new \pPie($image,$data);

            $pie->setSliceColor(0, [
                "R"=>244,
                "G"=>67,
                "B"=>54,
            ]);
            $pie->setSliceColor(1, [
                "R"=>33,
                "G"=>150,
                "B"=>243,
            ]);
            $pie->setSliceColor(2, [
                "R"=>139,
                "G"=>195,
                "B"=>74,
            ]);
            $pie->setSliceColor(3, [
                "R"=>255,
                "G"=>235,
                "B"=>59,
            ]);
            $pie->setSliceColor(4, [
                "R"=>121,
                "G"=>85,
                "B"=>72,
            ]);
            $pie->setSliceColor(5, [
                "R"=>255,
                "G"=>87,
                "B"=>34,
            ]);
            $pie->setSliceColor(6, [
                "R"=>49,
                "G"=>27,
                "B"=>146,
            ]);
            $pie->setSliceColor(7, [
                "R"=>91,
                "G"=>12,
                "B"=>39,
            ]);

            $pie->draw2DPie(400,200,[
                "DrawLabels"=>0,
                "LabelStacked"=>0,
                "Radius"=>140,
                "Border"=>1,
                "WriteValues"=>PIE_VALUE_PERCENTAGE,
                "ValuePosition"=>PIE_VALUE_OUTSIDE,
                'ValueR'=>0,
                'ValueG'=>0,
                'ValueB'=>0
            ]);
            $image->setShadow(0);

            $pie_name = $question_id.'_common';
            $image->render("temp/".$pie_name.'.png');

            $this->generateRadioLegend($data, $pie_name);
        }
    }

    private function generateRadioRegions($questions)
    {
        $regions = ArrayHelper::map(Region::find()
            ->orderBy('id')
            ->asArray()
            ->all(),'id','name');

        foreach($questions as $question_id => $question) {
            $data = new \pData();
            for($j = 0; $j < count($question); $j++) {
                $option = array_values($question);
                $data->addPoints($option[$j],"Probe "."$j");
            }
            $data->setPalette("Probe 0", [
                "R"=>244,
                "G"=>67,
                "B"=>54,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>33,
                "G"=>150,
                "B"=>243,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>139,
                "G"=>195,
                "B"=>74,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>255,
                "G"=>235,
                "B"=>59,
            ]);
            $data->setPalette("Probe 4", [
                "R"=>121,
                "G"=>85,
                "B"=>72,
            ]);
            $data->setPalette("Probe 5", [
                "R"=>255,
                "G"=>87,
                "B"=>34,
            ]);
            $data->setPalette("Probe 6", [
                "R"=>49,
                "G"=>27,
                "B"=>146,
            ]);
            $data->setPalette("Probe 7", [
                "R"=>91,
                "G"=>12,
                "B"=>39,
            ]);
            $data->addPoints($regions,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(800,800,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(350,20,700,800);
            $image->drawScale([
                "Pos"=>SCALE_POS_TOPBOTTOM,
                "DrawSubTicks"=>0,
                "Mode"=>SCALE_MODE_MANUAL,
                "ManualScale"=>[0=>["Min"=>0, "Max"=>100]]
            ]);
            $image->setShadow(1,[
                "X"=>1,
                "Y"=>1,
                "R"=>0,
                "G"=>0,
                "B"=>0,
                "Alpha"=>10
            ]);
            $image->drawBarChart([
                "DisplayPos"=>LABEL_POS_INSIDE,
                "DisplayValues"=>1
            ]);
            $image->setShadow(0);

            $bar_name = $question_id.'_region';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function generateRadioCompanies($questions, $survey)
    {
        $companies = ArrayHelper::map(Company::find()
            ->select(Company::tableName().'.id, '.Company::tableName().'.title, count('.View::tableName().'.survey_id) as count')
            ->from([Survey::tableName(), View::tableName(),
                Pharmacist::tableName(), Pharmacy::tableName(), Company::tableName()])
            ->where(Survey::tableName().'.id ='.View::tableName().'.survey_id')
            ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
            ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Pharmacy::tableName().'.id')
            ->andWhere(Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
            ->andWhere([Survey::tableName().'.id' => $survey->id])
            ->groupBy(Company::tableName().'.id')
            ->orderBy('count DESC')
            ->limit(7)
            ->asArray()
            ->all(),'id','title');

        foreach($questions as $question_id => $question) {
            $data = new \pData();
            for($j = 0; $j < count($question); $j++) {
                $option = array_values($question);
                $data->addPoints($option[$j],"Probe "."$j");
            }
            $data->setPalette("Probe 0", [
                "R"=>244,
                "G"=>67,
                "B"=>54,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>33,
                "G"=>150,
                "B"=>243,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>139,
                "G"=>195,
                "B"=>74,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>255,
                "G"=>235,
                "B"=>59,
            ]);
            $data->setPalette("Probe 4", [
                "R"=>121,
                "G"=>85,
                "B"=>72,
            ]);
            $data->setPalette("Probe 5", [
                "R"=>255,
                "G"=>87,
                "B"=>34,
            ]);
            $data->setPalette("Probe 6", [
                "R"=>49,
                "G"=>27,
                "B"=>146,
            ]);
            $data->setPalette("Probe 7", [
                "R"=>91,
                "G"=>12,
                "B"=>39,
            ]);
            $data->addPoints($companies,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(800,800,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(350,20,700,800);
            $image->drawScale([
                "Pos"=>SCALE_POS_TOPBOTTOM,
                "DrawSubTicks"=>0,
                "Mode"=>SCALE_MODE_MANUAL,
                "ManualScale"=>[0=>["Min"=>0, "Max"=>100]]
            ]);
            $image->setShadow(1,[
                "X"=>1,
                "Y"=>1,
                "R"=>0,
                "G"=>0,
                "B"=>0,
                "Alpha"=>10
            ]);
            $image->drawBarChart([
                "DisplayPos"=>LABEL_POS_INSIDE,
                "DisplayValues"=>1
            ]);
            $image->setShadow(0);

            $bar_name = $question_id.'_company';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function generateCheckboxCommon($questions)
    {
        foreach($questions as $question_id => $question) {
            $legend = [];
            foreach(array_keys($question) as $option) {
                $legend[] = wordwrap($option,150,"\n",0);
            }
            $data = new \pData();
            $data->addPoints(array_values($question),"ScoreA");
            $data->setSerieDescription("ScoreA","Количество ответов");

            $data->addPoints($legend,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(1000,260,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>18,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setShadow(1,[
                "X"=>2,
                "Y"=>2,
                "R"=>0,
                "G"=>0,
                "B"=>0,
                "Alpha"=>50
            ]);

            $pie = new \pPie($image,$data);
            $image->setShadow(0);

            $name = $question_id.'_common';

            $this->generateCheckboxLegend($data, $name, count($legend));
        }

        foreach($questions as $question_id => $question) {
            $legend = [];
            foreach(array_keys($question) as $option) {
                $legend[] = wordwrap($option,70,"\n",0);
            }
            $data = new \pData();
            $data->addPoints(array_values($question),"Probe 1");
            $data->addPoints($legend, "Labels");
            $data->setAbscissa("Labels");
            $data->setPalette("Probe 1", [
                "R"=>30,
                "G"=>105,
                "B"=>172
            ]);

            $image = new \pImage(800,450,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(320,20,700,400);
            $image->drawScale([
                "Pos"=>SCALE_POS_TOPBOTTOM,
                "DrawSubTicks"=>0,
                "Mode"=>SCALE_MODE_MANUAL,
                "ManualScale"=>[0=>["Min"=>0, "Max"=>100]]
            ]);
            $image->setShadow(1,[
                "X"=>1,
                "Y"=>1,
                "R"=>0,
                "G"=>0,
                "B"=>0,
                "Alpha"=>10
            ]);
            $image->drawBarChart([
                "DisplayPos"=>LABEL_POS_INSIDE,
                "DisplayValues"=>1
            ]);
            $image->setShadow(0);

            $bar_name = $question_id.'_common';
            $image->render("temp/".$bar_name.'.png');

            $this->generateCheckboxLegend($data, $bar_name);
        }

    }

    private function generateCheckboxRegions($questions)
    {

        $regions = ArrayHelper::map(Region::find()
            ->orderBy('id')
            ->asArray()
            ->all(),'id','name');

        foreach($questions as $question_id => $question) {
            $data = new \pData();
            for($j = 0; $j < count($question); $j++) {
                $option = array_values($question);
                $data->addPoints($option[$j],"Probe "."$j");
            }
            $data->setPalette("Probe 0", [
                "R"=>244,
                "G"=>67,
                "B"=>54,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>33,
                "G"=>150,
                "B"=>243,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>139,
                "G"=>195,
                "B"=>74,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>255,
                "G"=>235,
                "B"=>59,
            ]);
            $data->setPalette("Probe 4", [
                "R"=>121,
                "G"=>85,
                "B"=>72,
            ]);
            $data->setPalette("Probe 5", [
                "R"=>255,
                "G"=>87,
                "B"=>34,
            ]);
            $data->setPalette("Probe 6", [
                "R"=>49,
                "G"=>27,
                "B"=>146,
            ]);
            $data->setPalette("Probe 7", [
                "R"=>91,
                "G"=>12,
                "B"=>39,
            ]);
            $data->addPoints($regions,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(800,800,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(350,20,700,800);
            $image->drawScale([
                "Pos"=>SCALE_POS_TOPBOTTOM,
                "DrawSubTicks"=>0,
                "Mode"=>SCALE_MODE_MANUAL,
                "ManualScale"=>[0=>["Min"=>0, "Max"=>100]]
            ]);
            $image->setShadow(1,[
                "X"=>1,
                "Y"=>1,
                "R"=>0,
                "G"=>0,
                "B"=>0,
                "Alpha"=>10
            ]);
            $image->drawBarChart([
                "DisplayPos"=>LABEL_POS_INSIDE,
                "DisplayValues"=>1
            ]);
            $image->setShadow(0);

            $bar_name = $question_id.'_region';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function generateCheckboxCompanies($questions, $survey)
    {

        $companies = ArrayHelper::map(Company::find()
            ->select(Company::tableName().'.id, '.Company::tableName().'.title, count('.View::tableName().'.survey_id) as count')
            ->from([Survey::tableName(), View::tableName(),
                Pharmacist::tableName(), Pharmacy::tableName(), Company::tableName()])
            ->where(Survey::tableName().'.id ='.View::tableName().'.survey_id')
            ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
            ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Pharmacy::tableName().'.id')
            ->andWhere(Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
            ->andWhere([Survey::tableName().'.id' => $survey->id])
            ->groupBy(Company::tableName().'.id')
            ->orderBy('count DESC')
            ->limit(7)
            ->asArray()
            ->all(),'id','title');

        foreach($questions as $question_id => $question) {
            $data = new \pData();
            for($j = 0; $j < count($question); $j++) {
                $option = array_values($question);
                $data->addPoints($option[$j],"Probe "."$j");
            }
            $data->setPalette("Probe 0", [
                "R"=>244,
                "G"=>67,
                "B"=>54,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>33,
                "G"=>150,
                "B"=>243,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>139,
                "G"=>195,
                "B"=>74,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>255,
                "G"=>235,
                "B"=>59,
            ]);
            $data->setPalette("Probe 4", [
                "R"=>121,
                "G"=>85,
                "B"=>72,
            ]);
            $data->setPalette("Probe 5", [
                "R"=>255,
                "G"=>87,
                "B"=>34,
            ]);
            $data->setPalette("Probe 6", [
                "R"=>49,
                "G"=>27,
                "B"=>146,
            ]);
            $data->setPalette("Probe 7", [
                "R"=>91,
                "G"=>12,
                "B"=>39,
            ]);
            $data->addPoints($companies,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(800,800,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);


            $image->setGraphArea(350,20,700,800);
            $image->drawScale([
                "Pos"=>SCALE_POS_TOPBOTTOM,
                "DrawSubTicks"=>0,
                "Mode"=>SCALE_MODE_MANUAL,
                "ManualScale"=>[0=>["Min"=>0, "Max"=>100]]
            ]);
            $image->setShadow(1,[
                "X"=>1,
                "Y"=>1,
                "R"=>0,
                "G"=>0,
                "B"=>0,
                "Alpha"=>10
            ]);
            $image->drawBarChart([
                "DisplayPos"=>LABEL_POS_INSIDE,
                "DisplayValues"=>1
            ]);
            $image->setShadow(0);

            $bar_name = $question_id.'_company';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function exportPDF($survey, $company = false)
    {
        $filename = 'Статистика. Анкета: '.$survey->title.'.pdf';
        $pdf = new Pdf([
            'content' => $this->renderPartial($company ? 'export-company' : 'export-region', ['survey' => $survey]),
            'options' => [
                'title' => 'Статистика. Анкета: '.$survey->title.'.pdf',
                'subject' => 'Статистика',
                'defaultfooterline'=>false,
                'margin_footer'=>0,
            ],
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'marginLeft'=>10,
            'marginTop'=>10,
            'marginRight'=>10,
            'marginBottom'=>10,
            'filename' => Yii::getAlias('@uploads/temp/'.$filename),
            'destination' => Pdf::DEST_BROWSER,
        ]);
        $pdf->render();
        @unlink(Yii::getAlias('@uploads/temp/'.$filename));
    }

    private function exportDocx($survey)
    {
        $phpWord = new  \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(14);
        $questions = $survey->devidedQuestions['free'];
        $array = [];
        foreach($questions as $question) {
            $values = Answer::find()
                ->select('value')
                ->where(['question_id' => $question->id])
                ->asArray()
                ->all();

            $section = $phpWord->addSection();
            $text = $question->question;
            $fontStyle = [
                'name'=>'Times New Roman',
                'size'=>18,
                'color'=>'000000',
            ];
            $parStyle = [
                'align'=>'center'
            ];
            $section->addText($text, $fontStyle, $parStyle);

            $styleTable = [
                'borderSize'=>6,
                'borderColor'=>'000000',
                'cellMargin'=>80
            ];
            $phpWord->addTableStyle('table', $styleTable);

            $table = $section->addTable('table');

            for($i = 0; $i < count($values)/5; $i++) {
                for($j = 0; $j < 5; $j++) {
                    $array[$i][$j] = $values[$i*5+$j];
                }
            }

            foreach($array as $row) {
                $table->addRow();
                foreach($row as $cell) {
                    $table->addCell(2000)->addText($cell['value']);
                }
            }

        }

        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename='.$survey->title.'. Свободные вопросы.docx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("php://output");
    }
}
