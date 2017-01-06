<?php

namespace backend\controllers\presentations;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\presentation\View;
use common\models\presentation\Answer;
use common\models\User;
use common\models\Presentation;
use backend\models\presentation\answer\Search;
use kartik\mpdf\Pdf;
use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Writer_Excel5;

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
            'titles' => ArrayHelper::map(Presentation::find()->asArray()->all(), 'title','title'),
        ]);
    }

    public function actionView($user_id,$presentation_id)
    {
        return $this->render('view', [
            'answers' => Answer::find()->joinWith('view')->where(['user_id'=>$user_id, 'presentation_views.presentation_id'=>$presentation_id])->all(),
        ]);
    }

    public function actionDelete($id)
    {
        View::findOne($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionExportPdf($presentation_id)
    {

        $answers = Answer::find()->joinWith('view')->where([View::tableName().'.presentation_id'=>$presentation_id])->all();

        $pdf = new Pdf([
            'content' => $this->renderPartial('pdf-export', ['answers'=>$answers]),
            'options' => [
                'title' => 'Экспорт презентации',
                'subject' => 'Отчет по заполненной презентации',
                'defaultfooterline'=>false,
                'margin_footer'=>0,
            ],
            'cssInline'=>file_get_contents('../admin/css/pdf-export.css'),
            'marginLeft'=>10,
            'marginTop'=>10,
            'marginRight'=>10,
            'marginBottom'=>10,
            'destination' => Pdf::DEST_BROWSER,
        ]);
        $pdf->render();
    }

    public function actionExportXls($presentation_id)
    {
        $answers = Answer::find()->joinWith('view')->where([View::tableName().'.presentation_id'=>$presentation_id])->all();

        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        $sheet->setTitle('Результаты по презентации');
        $sheet->setCellValue("A1",'Результаты по презентации "'.$answers[0]->question->presentation->title.'"');
        $sheet->setCellValue("C1",'Регион/Город');
        $sheet->setCellValue("D1",'Организация/Аптека');
        $sheet->setCellValue("E1",'Дата/Время');
        $sheet->setCellValue("F1",'Образование');
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
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
                    $i, $answer->view->user->pharmacist->pharmacy->city->region->name . '/' . $answer->view->user->pharmacist->pharmacy->city->name
                );
                $sheet->setCellValueByColumnAndRow(
                    3,
                    $i, $answer->view->user->pharmacist->pharmacy->company->title
                    . '/' . $answer->view->user->pharmacist->pharmacy->name . ' (' . $answer->view->user->pharmacist->pharmacy->address . ')'
                );
                $sheet->setCellValueByColumnAndRow(
                    4,
                    $i, $answer->view->added
                );
                $sheet->setCellValueByColumnAndRow(
                    5,
                    $i, $answer->view->user->pharmacist->education->name
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
        header ( "Content-Disposition: attachment; filename=Презентация.xls" );

        $objWriter = new PHPExcel_Writer_Excel5($xls);
        $objWriter->save('php://output');
    }

    protected function findModel($id)
    {
        if (($model = Answer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена. ');
        }
    }

    public function actionMultipleDelete()
    {
        $pk = Yii::$app->request->post('row_id');
        foreach ($pk as $key => $value)
        {
            View::findOne($value)->delete();
        }
        return $this->redirect(['index']);

    }

}
