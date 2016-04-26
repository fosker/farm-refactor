<?php

namespace backend\controllers\surveys;

use common\models\survey\Question;
use kartik\mpdf\Pdf;
use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Writer_Excel5;
use Yii;
use common\models\survey\Answer;
use backend\models\survey\answer\Search;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\survey\View;
use common\models\User;
use common\models\Survey;

class AnswerController extends Controller
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
            'logins' => ArrayHelper::map(User::find()->asArray()->all(), 'id','login'),
            'surveys' => ArrayHelper::map(Survey::find()->asArray()->all(), 'title','title'),
        ]);
    }

    public function actionView($user_id,$survey_id)
    {
        return $this->render('view', [
            'answers' => Answer::find()->joinWith('view')->where(['user_id'=>$user_id, 'survey_views.survey_id'=>$survey_id])->all(),
        ]);
    }

    public function actionDelete($user_id,$survey_id)
    {
        View::deleteAll(['user_id' => $user_id, 'survey_id' => $survey_id]);
        //Answer::deleteAll(['user_id'=>$user_id, 'question_id'=>ArrayHelper::getColumn(Question::find()->select('id')->where(['survey_id'=>$survey_id])->asArray()->all(),'id')]);

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Answer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена. ');
        }
    }

    public function actionExportPdf($survey_id) {

        $answers = Answer::find()->joinWith('view')->where([View::tableName().'.survey_id'=>$survey_id])->all();

        $pdf = new Pdf([
            'content' => $this->renderPartial('pdf-export', ['answers'=>$answers]),
            'options' => [
                'title' => 'Экспорт анкет',
                'subject' => 'Отчет по заполненным анкетам',
                'defaultfooterline'=>false,
                'margin_footer'=>0,
            ],
            'cssInline'=>file_get_contents('../web/css/pdf-export.css'),
            'marginLeft'=>10,
            'marginTop'=>10,
            'marginRight'=>10,
            'marginBottom'=>10,
            'destination' => Pdf::DEST_BROWSER,
        ]);
        $pdf->render();
    }

    public function actionExportXls($survey_id) {
        $answers = Answer::find()->joinWith('view')->where([View::tableName().'.survey_id'=>$survey_id])->all();

        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        $sheet->setTitle('Результаты по анкете');
        $sheet->setCellValue("A1",'Результаты по анкете "'.$answers[0]->question->survey->title.'"');
        $sheet->setCellValue("C1",'Регион/город');
        $sheet->setCellValue("D1",'Дата/время');
        $sheet->setCellValue("E1",'Образование');
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $author = null;
        $i = 2;
        foreach($answers as $answer) {
            if ($author != $answer->view->user->name) {
                $i++;
                $sheet->mergeCells('A' . $i . ':B' . $i);
                $sheet->setCellValueByColumnAndRow(
                    0,
                    $i, $answer->view->user->name
                );
                $sheet->setCellValueByColumnAndRow(
                    2,
                    $i, $answer->view->user->pharmacy->city->region->name . '/' . $answer->view->user->pharmacy->city->name
                );
                $sheet->setCellValueByColumnAndRow(
                    3,
                    $i, $answer->view->added
                );
                $sheet->setCellValueByColumnAndRow(
                    4,
                    $i, $answer->view->user->education->name
                );
                $i++;
            }
            $sheet->setCellValueByColumnAndRow(
                0,
                $i, $answer->question->question
            );
            $sheet->setCellValueByColumnAndRow(
                1,
                $i, $answer->value
            );
            $author = $answer->view->user->name;
            $i++;
        }

        header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
        header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
        header ( "Cache-Control: no-cache, must-revalidate" );
        header ( "Pragma: no-cache" );
        header ( "Content-type: application/vnd.ms-excel" );
        header ( "Content-Disposition: attachment; filename=Анкеты.xls" );

        $objWriter = new PHPExcel_Writer_Excel5($xls);
        $objWriter->save('php://output');
    }

}
