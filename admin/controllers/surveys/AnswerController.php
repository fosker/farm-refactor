<?php

namespace backend\controllers\surveys;

use kartik\mpdf\Pdf;
use PHPExcel;
use PHPExcel_Style_Alignment;
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

    public function actionDelete($id)
    {
        View::findOne($id)->delete();
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
