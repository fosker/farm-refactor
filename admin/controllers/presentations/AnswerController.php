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

    public function actionDelete($user_id,$presentation_id)
    {
        View::deleteAll(['user_id' => $user_id, 'presentation_id' => $presentation_id]);
        //Answer::deleteAll(['user_id'=>$user_id, 'question_id'=>ArrayHelper::getColumn(Question::find()->select('id')->where(['presentation_id'=>$presentation_id])->asArray()->all(),'id')]);

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

}
