<?php

namespace backend\controllers;

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


class UserController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'names' => ArrayHelper::map(User::find()->asArray()->all(), 'name','name'),
            'firms' => ArrayHelper::map(Firm::find()->asArray()->all(), 'id','name'),
            'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
            'emails' => ArrayHelper::map(User::find()->asArray()->all(), 'email','email'),
            'pharmacies' => ArrayHelper::map(Pharmacy::find()->asArray()->all(), 'id','name'),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id, $update_id = null)
    {
        $model = $this->findModel($id);
        if($update_id) {
            $user = UpdateRequest::findOne(['user_id' => $update_id]);
        }
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if($model->save(false))
                if($update_id) {
                    UpdateRequest::deleteAll(['user_id' => $update_id]);
                }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'firms' => ArrayHelper::map(Firm::find()->asArray()->all(), 'id','name'),
                'regions' => ArrayHelper::map(Region::find()->asArray()->all(), 'id','name'),
                'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
                'education' => ArrayHelper::map(Education::find()->asArray()->all(), 'id','name'),
                'pharmacies' => ArrayHelper::map(Pharmacy::find()
                    ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
                    ->asArray()->all(), 'id','name'),
                'positions' => ArrayHelper::map(Position::find()->asArray()->all(), 'id','name'),
                'user' => $user
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
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Пользователя не существует.');
        }
    }

    public function actionAccept($id)
    {
        $this->findModel($id)->verified();

        $android_tokens = ArrayHelper::map(Device::find()->select('id, push_token')->where(['user_id' => $id])
            ->andWhere(['not',['push_token' => null]])
            ->andWhere(['type' => 1])
            ->asArray()
            ->all(), 'id', 'push_token');
        $ios_tokens = ArrayHelper::map(Device::find()->select('id, push_token')->where(['user_id' => $id])
            ->andWhere(['not',['push_token' => null]])
            ->andWhere(['type' => 2])
            ->asArray()
            ->all(), 'id', 'push_token');

        $android_tokens = array_values($android_tokens);
        $android_tokens = array_filter(array_unique($android_tokens));
        $ios_tokens = array_values($ios_tokens);
        $ios_tokens = array_filter(array_unique($ios_tokens));

        $message = 'Ваш аккаунт верифицирован. ';

        if($ios_tokens)
        {
            Yii::$app->apns->sendMulti($ios_tokens, $message, [], [
                'sound' => 'default',
                'badge' => 1
            ]);
        }

        if($android_tokens)
        {
            Yii::$app->gcm->sendMulti($android_tokens, $message);
        }

        Yii::$app->mailer->compose('@common/mail/user-verify', [
            'user' => User::findOne($id),
        ])
            ->setFrom('pharmbonus@gmail.com')
            ->setTo(User::findOne($id)->email)
            ->setSubject('Ваш аккаунт верифицирован.')
            ->send();

        return $this->redirect(['index']);
    }

    public function actionBan($id)
    {
        $this->findModel($id)->ban();

        return $this->redirect(['index']);
    }

    public function actionCityList() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $region_id = $parents[0];
                $out = City::getCityList($region_id);
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionFirmList() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $city_id = $parents[0];
                $out = Firm::getFirmList($city_id);
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionPharmacyList() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $firm_id = $parents[0];
                $city_id = $parents[1];
                $out = Pharmacy::getPharmacyList($firm_id, $city_id);
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

}
