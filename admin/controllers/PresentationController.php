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
use common\models\presentation\Comment;
use backend\models\presentation\Search;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use common\models\presentation\Answer;
use kartik\mpdf\Pdf;
use common\models\user\Pharmacist;
use common\models\presentation\View;
use yii\helpers\FileHelper;

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

    public function actionDeleteComment($id)
    {
        $model = Comment::findOne($id);
        $presentation_id = $model->presentation_id;
        $model->delete();
        $this->redirect(['view', 'id' => $presentation_id]);
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

    public function actionExportRegions($id)
    {
        $model = $this->findModel($id);
        $radio_questions = $model->devidedQuestions['radio'];
        $checkbox_questions = $model->devidedQuestions['checkbox'];

        $radio_common = Question::transformCommon($radio_questions);
        $radio_regions = Question::transformForRegions($radio_questions);


        $sums_radio = [];
        foreach($radio_regions as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $region_id => $region) {
                    $sums_radio[$question_id][$region_id] += $region;
                }
            }
        }


        foreach($radio_regions as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $region_id => $region) {
                    $radio_regions[$question_id][$option_id][$region_id] = $radio_regions[$question_id][$option_id][$region_id]/$sums_radio[$question_id][$region_id]*100;
                }
            }
        }

        $checkbox_common = Question::transformCommon($checkbox_questions);
        $checkbox_regions = Question::transformForRegions($checkbox_questions);


        foreach($checkbox_common as $question_id => $question) {
            foreach($question as $option_id => $option) {
                $checkbox_common[$question_id][$option_id] = $checkbox_common[$question_id][$option_id]/$model->answersCount*100;
            }
        }

        foreach($checkbox_regions as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $region_id => $region) {
                    $checkbox_regions[$question_id][$option_id][$region_id] = $checkbox_regions[$question_id][$option_id][$region_id]/reset($sums_radio)[$region_id]*100;
                }
            }
        }

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

        $radio_common = Question::transformCommon($radio_questions);
        $radio_companies = Question::transformForCompanies($radio_questions, $model);

        $sums_radio = [];
        foreach($radio_companies as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $company_id => $company) {
                    $sums_radio[$question_id][$company_id] += $company;
                }
            }
        }

        foreach($radio_companies as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $company_id => $company) {
                    $radio_companies[$question_id][$option_id][$company_id] = $radio_companies[$question_id][$option_id][$company_id]/$sums_radio[$question_id][$company_id]*100;
                }
            }
        }

        $checkbox_common = Question::transformCommon($checkbox_questions);
        $checkbox_companies = Question::transformForCompanies($checkbox_questions, $model);

        foreach($checkbox_common as $question_id => $question) {
            foreach($question as $option_id => $option) {
                $checkbox_common[$question_id][$option_id] = $checkbox_common[$question_id][$option_id]/$model->answersCount*100;
            }
        }

        foreach($checkbox_companies as $question_id => $question) {
            foreach($question as $option_id => $option) {
                foreach($option as $company_id => $company) {
                    $checkbox_companies[$question_id][$option_id][$company_id] = $checkbox_companies[$question_id][$option_id][$company_id]/reset($sums_radio)[$company_id]*100;
                }
            }
        }

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
        $image = new \pImage(100,200,$pData,1);
        $image->setFontProperties([
            "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
            "FontSize"=>18,
            "R"=>0,
            "G"=>0,
            "B"=>0,
        ]);

        $pie = new \pPie($image,$pData);
        $pie->drawPieLegend(0,10,[
            "Style"=>LEGEND_NOBORDER,
            "Mode"=>LEGEND_VERTICAL,
            "FontSize"=>12,
        ]);
        $legend_name = $name . "_legend";
        $image->render("temp/".$legend_name.'.png');
    }

    private function generateCheckboxLegend($pData, $name)
    {
        $image = new \pImage(1000,200,$pData,1);
        $image->setFontProperties([
            "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
            "FontSize"=>18,
            "R"=>0,
            "G"=>0,
            "B"=>0,
        ]);

        $pie = new \pPie($image,$pData);
        $pie->setSliceColor(0, [
            "R"=>30,
            "G"=>105,
            "B"=>172,
        ]);
        $pie->setSliceColor(1, [
            "R"=>18,
            "G"=>145,
            "B"=>15,
        ]);
        $pie->setSliceColor(2, [
            "R"=>138,
            "G"=>179,
            "B"=>61,
        ]);
        $pie->setSliceColor(3, [
            "R"=>242,
            "G"=>139,
            "B"=>8,
        ]);
        $pie->drawPieLegend(0,10,[
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
            $data = new \pData();
            $data->addPoints(array_values($question),"ScoreA");
            $data->setSerieDescription("ScoreA","Количество ответов");

            $data->addPoints(array_keys($question),"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(1000,400,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>24,
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
                "R"=>30,
                "G"=>105,
                "B"=>172,
            ]);
            $pie->setSliceColor(1, [
                "R"=>18,
                "G"=>145,
                "B"=>15,
            ]);
            $pie->setSliceColor(2, [
                "R"=>138,
                "G"=>179,
                "B"=>61,
            ]);
            $pie->setSliceColor(3, [
                "R"=>242,
                "G"=>139,
                "B"=>8,
            ]);

            $pie->draw2DPie(450,190,[
                "DrawLabels"=>0,
                "Radius"=>190,
                "Border"=>1,
                "WriteValues"=>PIE_VALUE_PERCENTAGE,
                "ValuePosition"=>PIE_VALUE_INSIDE,
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
                "R"=>30,
                "G"=>105,
                "B"=>172,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>18,
                "G"=>145,
                "B"=>15,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>138,
                "G"=>179,
                "B"=>61,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>242,
                "G"=>139,
                "B"=>8,
            ]);
            $data->addPoints($regions,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(700,500,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>15,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(300,20,600,400);
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
            $image->drawBarChart();
            $image->setShadow(0);

            $bar_name = $question_id.'_region';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function generateRadioCompanies($questions, $presentation)
    {
        $companies = ArrayHelper::map(Company::find()
            ->select(Company::tableName().'.id, '.Company::tableName().'.title, count('.View::tableName().'.presentation_id) as count')
            ->from([Presentation::tableName(), View::tableName(),
                Pharmacist::tableName(), Pharmacy::tableName(), Company::tableName()])
            ->where(Presentation::tableName().'.id ='.View::tableName().'.presentation_id')
            ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
            ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Pharmacy::tableName().'.id')
            ->andWhere(Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
            ->andWhere([Presentation::tableName().'.id' => $presentation->id])
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
                "R"=>30,
                "G"=>105,
                "B"=>172,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>18,
                "G"=>145,
                "B"=>15,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>138,
                "G"=>179,
                "B"=>61,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>242,
                "G"=>139,
                "B"=>8,
            ]);
            $data->addPoints($companies,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(700,500,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>15,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(300,20,600,400);
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
            $image->drawBarChart();
            $image->setShadow(0);

            $bar_name = $question_id.'_company';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function generateCheckboxCommon($questions)
    {
        foreach($questions as $question_id => $question) {
            $data = new \pData();
            $data->addPoints(array_values($question),"ScoreA");
            $data->setSerieDescription("ScoreA","Количество ответов");

            $data->addPoints(array_keys($question),"Labels");
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

            $this->generateCheckboxLegend($data, $name);
        }

        foreach($questions as $question_id => $question) {
            $data = new \pData();
            $data->addPoints(array_values($question),"Probe 1");
            $data->addPoints(array_keys($question), "Labels");
            $data->setAbscissa("Labels");
            $data->setPalette("Probe 1", [
                "R"=>30,
                "G"=>105,
                "B"=>172
            ]);

            $image = new \pImage(1200,500,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>15,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(400,20,1000,400);
            $image->drawScale([
                "Pos"=>SCALE_POS_LEFTRIGHT,
                "DrawSubTicks"=>0,
                "LabelRotation"=>10,
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
                'LabelRotation'=>50
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
                "R"=>30,
                "G"=>105,
                "B"=>172,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>18,
                "G"=>145,
                "B"=>15,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>138,
                "G"=>179,
                "B"=>61,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>242,
                "G"=>139,
                "B"=>8,
            ]);
            $data->addPoints($regions,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(700,500,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>15,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);


            $image->setGraphArea(300,20,600,400);
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
            $image->drawBarChart();
            $image->setShadow(0);

            $bar_name = $question_id.'_region';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function generateCheckboxCompanies($questions, $presentation)
    {

        $companies = ArrayHelper::map(Company::find()
            ->select(Company::tableName().'.id, '.Company::tableName().'.title, count('.View::tableName().'.presentation_id) as count')
            ->from([Presentation::tableName(), View::tableName(),
                Pharmacist::tableName(), Pharmacy::tableName(), Company::tableName()])
            ->where(Presentation::tableName().'.id ='.View::tableName().'.presentation_id')
            ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
            ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Pharmacy::tableName().'.id')
            ->andWhere(Pharmacy::tableName().'.company_id ='.Company::tableName().'.id')
            ->andWhere([Presentation::tableName().'.id' => $presentation->id])
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
                "R"=>30,
                "G"=>105,
                "B"=>172,
            ]);
            $data->setPalette("Probe 1", [
                "R"=>18,
                "G"=>145,
                "B"=>15,
            ]);
            $data->setPalette("Probe 2", [
                "R"=>138,
                "G"=>179,
                "B"=>61,
            ]);
            $data->setPalette("Probe 3", [
                "R"=>242,
                "G"=>139,
                "B"=>8,
            ]);
            $data->addPoints($companies,"Labels");
            $data->setAbscissa("Labels");

            $image = new \pImage(700,500,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>15,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);


            $image->setGraphArea(300,20,600,400);
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
            $image->drawBarChart();
            $image->setShadow(0);

            $bar_name = $question_id.'_company';
            $image->render("temp/".$bar_name.'.png');
        }
    }

    private function exportPDF($presentation, $company = false)
    {
        $filename = 'Статистика. Презентация: '.$presentation->title.'.pdf';
        $pdf = new Pdf([
            'content' => $this->renderPartial($company ? 'export-company' : 'export-region', ['presentation' => $presentation]),
            'options' => [
                'title' => 'Статистика. Анкета: '.$presentation->title.'.pdf',
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

    private function exportDocx($presentation)
    {
        $phpWord = new  \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(14);
        $questions = $presentation->devidedQuestions['free'];
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
            $section->addText(htmlspecialchars($text), $fontStyle, $parStyle);

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
                    $table->addCell(1500)->addText($cell['value']);
                }
            }
        }

        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename='.$presentation->title.'. Свободные вопросы.docx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("php://output");
    }
}
