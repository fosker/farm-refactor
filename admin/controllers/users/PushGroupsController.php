<?php

namespace backend\controllers\users;

use common\models\profile\Position;
use Yii;

use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Push;
use common\models\agency\Firm;
use common\models\agency\Pharmacy;
use common\models\User;
use common\models\location\City;
use backend\models\profile\Search;
use common\models\profile\Device;
use common\models\profile\Education;
use common\models\profile\UpdateRequest;
use common\models\location\Region;
use yii\helpers\Json;
use common\models\Seminar;
use common\models\Survey;
use common\models\Block;
use common\models\Item;
use common\models\factory\Stock;
use common\models\Presentation;
use common\models\News;
use common\models\Vacancy;


class PushGroupsController extends Controller
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

    public function actionLinkList($q = null, $id = null) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $block = Block::find()->select('CONCAT("block/",`id`) as id, CONCAT("Страница: ",`title`) as text')->where(['like','CONCAT("Страница: ",title)',$q])->asArray()->limit(20);

            $survey = Survey::find()->select('CONCAT("survey/",`id`) as id, CONCAT("Анкета: ",`title`) as text')->where(['like','CONCAT("Анкета: ",title)',$q])->asArray();

            $seminar = Seminar::find()->select('CONCAT("seminar/",`id`) as id, CONCAT("Семинар: ",`title`) as text')->where(['like','CONCAT("Семинар: ",title)',$q])->asArray();

            $present = Item::find()->select('CONCAT("present/",`id`) as id, CONCAT("Подарок: ",`title`) as text')->where(['like','CONCAT("Подарок: ",title)',$q])->asArray();

            $presentation = Presentation::find()->select('CONCAT("presentation/",`id`) as id, CONCAT("Презентация: ",`title`) as text')->where(['like','CONCAT("Презентация: ",title)',$q])->asArray();

            $stock = Stock::find()->select('CONCAT("stock/",`id`) as id, CONCAT("Акция: ",`title`) as text')->where(['like','CONCAT("Акция: ",title)',$q])->asArray();

            $news = News::find()->select('CONCAT("news/",`id`) as id, CONCAT("Новость: ",`title`) as text')->where(['like','CONCAT("Новость: ",title)',$q])->asArray();

            $vacancy = Vacancy::find()->select('CONCAT("vacancy/",`id`) as id, CONCAT("Вакансия: ",`title`) as text')->where(['like','CONCAT("Вакансия: ",title)',$q])->asArray();

            $block->union($survey)->union($seminar)->union($present)->union($stock)->union($presentation)->union($news)->union($vacancy);

            $out['results'] = array_values($block->limit(20)->all());
        }
        elseif (!is_null($id)) {
            $path = explode("/",$id);
            switch($path[0]) {
                case 'block':
                    $item = Block::findOne($path[1]);
                    break;
                case 'present':
                    $item = Item::findOne($path[1]);
                    break;
                case 'presentation':
                    $item = Presentation::findOne($path[1]);
                    break;
                case 'survey':
                    $item = Survey::findOne($path[1]);
                    break;
                case 'seminar':
                    $item = Seminar::findOne($path[1]);
                    break;
                case 'stock':
                    $item = Stock::findOne($path[1]);
                    break;
                case 'news':
                    $item = News::findOne($path[1]);
                    break;
                case 'vacancy':
                    $item = Vacancy::findOne($path[1]);
                    break;
            }
            $out['results'] = ['id' => $id, 'text' => $item->title];
        }

        return $out;
    }

    public function actionIndex()
    {
        $model = new Push();

        if(Yii::$app->request->post()) {

            $cities = Yii::$app->request->post('cities') ?  Yii::$app->request->post('cities') : [];
            $educations = Yii::$app->request->post('education') ?  Yii::$app->request->post('education') : [];
            $pharmacies = Yii::$app->request->post('pharmacies') ?  Yii::$app->request->post('pharmacies') : [];
            $model->load(Yii::$app->request->post());

            $users = ArrayHelper::map(User::find()->select(User::tableName().'.id')->andWhere(['in', 'education_id', $educations])
                ->andWhere(['in', 'pharmacy_id', $pharmacies])
                ->join('LEFT JOIN', Pharmacy::tableName(),
                    User::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
                ->andWhere(['in', 'city_id', $cities])
                ->asArray()
                ->all(), 'id', 'id');

            $android_tokens = ArrayHelper::map(Device::find()->select('id, push_token')->where(['in', 'user_id', $users])
                ->andWhere(['not',['push_token' => null]])
                ->andWhere(['type' => 1])
                ->asArray()
                ->all(), 'id', 'push_token');

            $ios_tokens = ArrayHelper::map(Device::find()->select('id, push_token')->where(['in', 'user_id', $users])
                ->andWhere(['not',['push_token' => null]])
                ->andWhere(['type' => 2])
                ->asArray()
                ->all(), 'id', 'push_token');

            $android_tokens = array_values($android_tokens);
            $android_tokens = array_filter(array_unique($android_tokens));

            $ios_tokens = array_values($ios_tokens);
            $ios_tokens = array_filter(array_unique($ios_tokens));

//            echo '<pre>';
//            var_dump($ios_tokens);
//            var_dump($android_tokens);
//            echo '</pre>';
//            die();

            if($ios_tokens)
            {
                if(Yii::$app->apns->sendMulti($ios_tokens, $model->message, ['link' => $model->link], [
                    'sound' => 'default',
                    'badge' => 1,
                ])){
                    Yii::$app->session->setFlash('PushMessage',
                        'Push-уведомление успешно отправлено на '.count($ios_tokens).' ios-устройств');
                }
            }
            if($android_tokens)
            {
                if(Yii::$app->gcm->sendMulti($android_tokens, $model->message, ['link' => $model->link])){
                    Yii::$app->session->setFlash('PushMessage2',
                        'Push-уведомление успешно отправлено на ' . count($android_tokens) . ' android-устройств');
                }
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('index', [
                'model' => $model,
                'cities'=>City::find()->asArray()->all(),
                'pharmacies'=>Pharmacy::find()->asArray()->all(),
                'education' => Education::find()->asArray()->all()
            ]);
        }
    }

}
