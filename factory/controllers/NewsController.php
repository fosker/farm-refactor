<?php


namespace factory\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use common\models\news\Comment;
use common\models\News;
use common\models\news\Type as News_Type;
use common\models\profile\Type;
use common\models\Company;
use common\models\Factory;
use factory\models\search\news\Search;


class NewsController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
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
            'news' => ArrayHelper::map(News::find()->where(['factory_id' => Yii::$app->user->identity->factory_id])->asArray()->all(),'title','title'),
        ]);
    }

    public function actionView($id)
    {
        $comment = new Comment();
        $comment->admin_type = 1;
        $comment->news_id = $id;
        $comment->user_id = Yii::$app->user->id;
        if($comment->load(Yii::$app->request->post())) {
            $comment->save(false);
            $this->refresh();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'comment' => $comment
        ]);
    }

    public function actionCreate()
    {
        $model = new News();
        $model->scenario = 'create';
        $model->factory_id = Yii::$app->user->identity->factory_id;

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                $type = new News_Type();
                $type->news_id = $model->id;
                $type->type_id = Type::TYPE_AGENT;
                $type->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->thumbFile = UploadedFile::getInstance($model, 'thumbFile');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
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
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Новость не найдена. ');
        }
    }
}
