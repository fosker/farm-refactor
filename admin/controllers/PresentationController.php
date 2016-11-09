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
use PhpOffice\PhpWord\Shared\ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
                if(!Yii::$app->request->post('companies')) {
                    $model->deletePharmacies();
                }
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

        $radio_sums = Question::getRegionSums($radio_questions);
        $checkbox_sums = Question::getRegionSums($checkbox_questions);

        $radio_common = Question::transformRadioCommon($radio_questions);
        $radio_regions = Question::transformRadioRegions($radio_questions, $radio_sums);

        $checkbox_common = Question::transformCheckboxCommon($checkbox_questions, $model);
        $checkbox_regions = Question::transformCheckboxRegions($checkbox_questions, $checkbox_sums);

        FileHelper::createDirectory('temp/presentation/'.$id);

        $this->generateRadioCommon($radio_common, $model->id);
        $this->generateRadioRegions($radio_regions, $model->id);
        $this->generateCheckboxCommon($checkbox_common, $model->id);
        $this->generateCheckboxRegions($checkbox_regions, $model->id);

        $this->exportPDF($model, false);

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

        FileHelper::createDirectory('temp/presentation/'.$id);

        $this->generateRadioCommon($radio_common, $model->id);
        $this->generateRadioCompanies($radio_companies, $model);

        $this->generateCheckboxCommon($checkbox_common, $model->id);
        $this->generateCheckboxCompanies($checkbox_companies, $model);

        $this->exportPDF($model, true);

    }

    public function actionExportDocx($id)
    {
        $model = $this->findModel($id);
        $this->exportDocx($model);
    }

    public function actionExportImages($id)
    {
        $model = $this->findModel($id);

        $radio_questions = $model->devidedQuestions['radio'];
        $checkbox_questions = $model->devidedQuestions['checkbox'];

        $radio_sums = Question::getRegionSums($radio_questions);
        $checkbox_sums = Question::getRegionSums($checkbox_questions);

        $radio_common = Question::transformRadioCommon($radio_questions);
        $radio_regions = Question::transformRadioRegions($radio_questions, $radio_sums);
        $radio_companies = Question::transformRadioCompanies($radio_questions, $model, $radio_sums);

        $checkbox_common = Question::transformCheckboxCommon($checkbox_questions, $model);
        $checkbox_regions = Question::transformCheckboxRegions($checkbox_questions, $checkbox_sums);
        $checkbox_companies = Question::transformCheckboxCompanies($checkbox_questions, $model, $checkbox_sums);

        FileHelper::createDirectory('temp/presentation/'.$id);

        $this->generateRadioCommon($radio_common, $model->id);
        $this->generateRadioRegions($radio_regions, $model->id);
        $this->generateRadioCompanies($radio_companies, $model);

        $this->generateCheckboxCommon($checkbox_common, $model->id);
        $this->generateCheckboxRegions($checkbox_regions, $model->id);
        $this->generateCheckboxCompanies($checkbox_companies, $model);

        $path = realpath('temp/presentation/'.$id);
        $title = 'presentation_'.$id;
        $zip = new ZipArchive();
        $zip->open($title.'.zip', ZipArchive::CREATE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        FileHelper::removeDirectory('temp');
        $file = 'presentation_'.$id.'.zip';
        if (file_exists($file)) {
            Yii::$app->response->sendFile($file);
        }

    }

    private function generateRadioLegend($pData, $name, $options)
    {
        $height = $options * 48;
        $image = new \pImage(500,$height,$pData,1);
        $image->setFontProperties([
            "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
            "FontSize"=>18,
            "R"=>0,
            "G"=>0,
            "B"=>0,
        ]);

        $pie = new \pPie($image,$pData);
        $pie->drawPieLegend(50,15,[
            "Style"=>LEGEND_NOBORDER,
            "Mode"=>LEGEND_VERTICAL,
            "FontSize"=>12,
        ]);
        $legend_name = $name . "_legend";
        $image->render($legend_name.'.png');
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
        $image->render($legend_name.'.png');
    }

    private function generateRadioCommon($questions,$id)
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

            $pie_name = "temp/presentation/$id/".$question_id.'_common';
            $image->render($pie_name.'.png');

            $this->generateRadioLegend($data, $pie_name, count($legend));
        }
    }

    private function generateRadioRegions($questions,$id)
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

            $image = new \pImage(800,1000,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(350,20,700,1000);
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

            $bar_name = "temp/presentation/$id/".$question_id.'_region';
            $image->render($bar_name.'.png');
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

            $image = new \pImage(800,1000,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(350,20,700,1000);
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
            $id = $presentation->id;
            $bar_name = "temp/presentation/$id/".$question_id.'_company';
            $image->render($bar_name.'.png');
        }
    }

    private function generateCheckboxCommon($questions, $id)
    {
        foreach($questions as $question_id => $question) {
            $legend = [];
            foreach(array_keys($question) as $option) {
                $legend[] = wordwrap($option,70,"\n",0);
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

            $name = "temp/presentation/$id/".$question_id.'_common';

            $this->generateCheckboxLegend($data, $name, count($legend));
        }

        foreach($questions as $question_id => $question) {
            $legend = [];
            foreach(array_keys($question) as $option) {
                $legend[] = wordwrap($option,90,"\n",0);
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
                "FontSize"=>10,
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
            $palette = [
                "0"=>[
                    "R"=>244,
                    "G"=>67,
                    "B"=>54,
                ],
                "1"=>[
                    "R"=>33,
                    "G"=>150,
                    "B"=>243,
                ],
                "2"=>[
                    "R"=>139,
                    "G"=>195,
                    "B"=>74,
                ],
                "3"=>[
                    "R"=>255,
                    "G"=>235,
                    "B"=>59,
                ],
                "4"=>[
                    "R"=>121,
                    "G"=>85,
                    "B"=>72,
                ],
                "5"=>[
                    "R"=>255,
                    "G"=>87,
                    "B"=>34,
                ],
                "6"=>[
                    "R"=>49,
                    "G"=>27,
                    "B"=>146,
                ],
                "7"=>[
                    "R"=>91,
                    "G"=>12,
                    "B"=>39,
                ]
            ];

            $image->drawBarChart([
                "DisplayPos"=>LABEL_POS_INSIDE,
                "DisplayValues"=>1,
                "OverrideColors"=>$palette
            ]);
            $image->setShadow(0);

            $bar_name = "temp/presentation/$id/".$question_id.'_common';
            $image->render($bar_name.'.png');

            $this->generateCheckboxLegend($data, $bar_name, count($legend));
        }
    }

    private function generateCheckboxRegions($questions, $id)
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

            $image = new \pImage(800,1000,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);

            $image->setGraphArea(350,20,700,1000);
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

            $bar_name = "temp/presentation/$id/".$question_id.'_region';
            $image->render($bar_name.'.png');
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

            $image = new \pImage(800,1000,$data,1);

            $image->setFontProperties([
                "FontName"=>__DIR__."/../components/pChart/fonts/times.ttf",
                "FontSize"=>12,
                "R"=>0,
                "G"=>0,
                "B"=>0,
            ]);


            $image->setGraphArea(350,20,700,1000);
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

            $id = $presentation->id;
            $bar_name = "temp/presentation/$id/".$question_id.'_company';
            $image->render($bar_name.'.png');
        }
    }

    private function exportPDF($presentation, $company = false)
    {
        $filename = 'Статистика. Презентация: '.$presentation->title.'.pdf';
        $pdf = new Pdf([
            'content' => $this->renderPartial($company ? 'export-company' : 'export-region', ['presentation' => $presentation]),
            'options' => [
                'title' => 'Статистика. Презентация: '.$presentation->title.'.pdf',
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
        $regions = Region::find()
            ->orderBy('id')
            ->all();
        foreach($questions as $question) {
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
            foreach($regions as $region) {
                $text = $region->name;
                $fontStyle = [
                    'name'=>'Times New Roman',
                    'size'=>14,
                    'color'=>'000000',
                ];
                $parStyle = [
                    'align'=>'center'
                ];
                $section->addText(htmlspecialchars($text), $fontStyle, $parStyle);

                $array = [];
                $values = Answer::find()
                    ->select('value')
                    ->from([Answer::tableName(), View::tableName(), Pharmacist::tableName(), Pharmacy::tableName(), City::tableName()])
                    ->andWhere(Answer::tableName().'.view_id ='.View::tableName().'.id')
                    ->andWhere(View::tableName().'.user_id ='.Pharmacist::tableName().'.id')
                    ->andWhere(Pharmacist::tableName().'.pharmacy_id ='.Pharmacy::tableName().'.id')
                    ->andWhere(City::tableName().'.id ='.Pharmacy::tableName().'.city_id')
                    ->andWhere(City::tableName().'.region_id='.$region->id)
                    ->andWhere(['question_id' => $question->id])
                    ->asArray()
                    ->all();

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
                        $table->addCell(2000)->addText(htmlspecialchars($cell['value']));
                    }
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

    public function actionComment($id)
    {
        $model = Comment::findOne($id);
        $model->scenario = 'comment';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/']);
        } else {
            return $this->render('comment', [
                'model' => $model,
            ]);
        }
    }
}
