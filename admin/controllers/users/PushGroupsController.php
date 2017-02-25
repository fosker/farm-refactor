<?php

namespace backend\controllers\users;


use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


use common\models\User;
use common\models\location\City;
use common\models\profile\Device;
use common\models\profile\Education;
use common\models\Seminar;
use common\models\Survey;
use common\models\Item;
use common\models\Stock;
use common\models\Presentation;
use common\models\News;
use common\models\Vacancy;
use common\models\Factory;
use common\models\company\Pharmacy;
use common\models\Company;
use common\models\location\Region;
use common\models\Push;
use common\models\pharmbonus\Users;

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
            $survey = Survey::find()->select('CONCAT("survey/",`id`) as id, CONCAT("Анкета: ",`title`) as text')->where(['like','CONCAT("Анкета: ",title)',$q])->asArray();

            $seminar = Seminar::find()->select('CONCAT("seminar/",`id`) as id, CONCAT("Семинар: ",`title`) as text')->where(['like','CONCAT("Семинар: ",title)',$q])->asArray();

            $present = Item::find()->select('CONCAT("present/",`id`) as id, CONCAT("Подарок: ",`title`) as text')->where(['like','CONCAT("Подарок: ",title)',$q])->asArray();

            $presentation = Presentation::find()->select('CONCAT("presentation/",`id`) as id, CONCAT("Презентация: ",`title`) as text')->where(['like','CONCAT("Презентация: ",title)',$q])->asArray();

            $stock = Stock::find()->select('CONCAT("factory/stock/",`id`) as id, CONCAT("Акция: ",`title`) as text')->where(['like','CONCAT("Акция: ",title)',$q])->asArray();

            $news = News::find()->select('CONCAT("news/",`id`) as id, CONCAT("Новость: ",`title`) as text')->where(['like','CONCAT("Новость: ",title)',$q])->asArray();

            $vacancy = Vacancy::find()->select('CONCAT("vacancy/",`id`) as id, CONCAT("Вакансия: ",`title`) as text')->where(['like','CONCAT("Вакансия: ",title)',$q])->asArray();

            $survey->union($seminar)->union($present)->union($stock)->union($presentation)->union($news)->union($vacancy);

            $out['results'] = array_values($survey->limit(20)->all());
        }
        elseif (!is_null($id)) {
            $path = explode("/",$id);
            switch($path[0]) {
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
                case 'factory/stock':
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
            $factories = Yii::$app->request->post('factories') ?  Yii::$app->request->post('factories') : [];
            $pharmacies = Yii::$app->request->post('pharmacies') ?  Yii::$app->request->post('pharmacies') : [];

            $model->load(Yii::$app->request->post());
            if (Yii::$app->request->post('forList')) {
                $model->forList = implode(',',Yii::$app->request->post('forList'));
            }

            if($factories && !$pharmacies && !$educations) {
                $users = ArrayHelper::map(
                    User::find()
                        ->select(User::tableName().'.id')
                        ->joinWith('agent')
                        ->andWhere(['in', 'factory_id', $factories])
                        ->andFilterWhere(['in', 'inList', explode(',',$model->forList)])
                        ->asArray()
                        ->all(), 'id', 'id'
                );
            } else {
                $users = ArrayHelper::map(
                    User::find()
                        ->select(User::tableName().'.id')
                        ->joinWith('pharmacist')
                        ->join('LEFT JOIN', Pharmacy::tableName(),
                            'pharmacy_id = '.Pharmacy::tableName().'.id')
                        ->where(['in', 'city_id', $cities])
                        ->orWhere(['in', 'education_id', $educations])
                        ->orWhere(['in', 'pharmacy_id', $pharmacies])
                        ->andFilterWhere(['in', 'inList', explode(',',$model->forList)])
                        ->asArray()
                        ->all(), 'id', 'id'
                );
            }

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
            $android_tokens = array_values(array_filter(array_unique($android_tokens)));

            $ios_tokens = array_values($ios_tokens);
            $ios_tokens = array_values(array_filter(array_unique($ios_tokens)));


            if($android_tokens) {
                if(Yii::$app->gcm->sendMulti($android_tokens, $model->message, ['link' => $model->link])) {
                    Yii::$app->session->setFlash('PushMessage',
                        'Push-уведомление успешно отправлено пользователям (' . count($users) . ')');
                }
            }

            if($ios_tokens) {
                if(Yii::$app->apns->sendMulti($ios_tokens, $model->message, ['link' => $model->link], [
                    'sound' => 'default',
                    'badge' => $model->link ? 1 : 0
                ])) {
                    Yii::$app->session->setFlash('PushMessage',
                        'Push-уведомление успешно отправлено пользователям (' . count($users) . ')');
                }
            }

            $model->device_count = count($users);
            $model->views = 0;
            if($model->save(false)) {
                foreach($users as $id) {
                    $users = new Users();
                    $users->push_id = $model->id;
                    $users->user_id = $id;
                    $users->save();
                }
            }

            return $this->redirect(['index']);

        } else {
            return $this->render('index', [
                'model' => $model,
                'regions' => Region::find()->asArray()->all(),
                'cities' => City::find()->asArray()->all(),
                'education' => Education::find()->asArray()->all(),
                'companies' => Company::find()->asArray()->all(),
                'pharmacies' => Pharmacy::find()->asArray()->all(),
                'factories' => Factory::find()->asArray()->all(),
            ]);
        }
    }

}
