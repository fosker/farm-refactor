<?php

namespace backend\controllers;

use common\models\profile\Position;
use Yii;

use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use common\models\profile\AgentUpdateRequest;
use common\models\profile\PharmacistUpdateRequest;
use common\models\Company;
use common\models\user\Agent;
use common\models\user\Pharmacist;
use common\models\Factory;
use common\models\company\Pharmacy;
use common\models\User;
use common\models\location\City;
use common\models\profile\Device;
use common\models\profile\Education;
use common\models\location\Region;
use common\models\profile\Type;
use backend\models\profile\agent\Search as Agent_Search;
use backend\models\profile\pharmacist\Search as Pharmacist_Search;



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

    public function actionAgents()
    {
        $searchModel = new Agent_Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('agents/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'names' => ArrayHelper::map(User::find()->where(['type_id' => 2])->asArray()->all(), 'name', 'name'),
            'factories' => ArrayHelper::map(Factory::find()->asArray()->all(), 'id', 'title'),
            'emails' => ArrayHelper::map(User::find()->where(['type_id' => 2])->asArray()->all(), 'email', 'email'),
        ]);
    }

    public function actionPharmacists()
    {
        $searchModel = new Pharmacist_Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pharmacists/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'names' => ArrayHelper::map(User::find()->where(['type_id' => 1])->asArray()->all(), 'name', 'name'),
            'pharmacies' => ArrayHelper::map(Pharmacy::find()->asArray()->all(), 'id', 'name'),
            'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id', 'name'),
            'companies' => ArrayHelper::map(Company::find()->asArray()->all(), 'id', 'title'),
            'emails' => ArrayHelper::map(User::find()->where(['type_id' => 1])->asArray()->all(), 'email', 'email'),
        ]);
    }

    public function actionView($id)
    {
        $type = $this->findModel($id)->type_id;
        return $this->render('view_'.$type, [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id, $update_id = null)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        switch($model->type_id) {
            case Type::TYPE_PHARMACIST: $type = Pharmacist::findOne($id);
                                        $type->scenario = 'update';
                break;
            case Type::TYPE_AGENT: $type = Agent::findOne($id);
                break;
        }
        if($update_id) {
            switch($model->type_id) {
                case Type::TYPE_PHARMACIST: $update = PharmacistUpdateRequest::findOne(['pharmacist_id' => $update_id]);
                    break;
                case Type::TYPE_AGENT: $update = AgentUpdateRequest::findOne(['agent_id' => $update_id]);
                    break;
            }
        }
        if (($model->load(Yii::$app->request->post()) && $type->load(Yii::$app->request->post()) &&
            ($model->validate() && $type->validate()))) {
            if($model->save(false) && $type->save(false))
                if($update_id) {
                    switch($model->type_id) {
                        case Type::TYPE_PHARMACIST: PharmacistUpdateRequest::deleteAll(['pharmacist_id' => $update_id]);
                            break;
                        case Type::TYPE_AGENT: AgentUpdateRequest::deleteAll(['agent_id' => $update_id]);
                            break;
                    }
                }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            switch($model->type_id) {
                case Type::TYPE_PHARMACIST:
                    return $this->render('pharmacists/update', [
                        'model' => $model,
                        'companies' => ArrayHelper::map(Company::find()->asArray()->all(), 'id','title'),
                        'regions' => ArrayHelper::map(Region::find()->asArray()->all(), 'id','name'),
                        'cities' => ArrayHelper::map(City::find()->asArray()->all(), 'id','name'),
                        'pharmacies' => ArrayHelper::map(Pharmacy::find()
                            ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
                            ->asArray()->all(), 'id','name'),
                        'education' => ArrayHelper::map(Education::find()->asArray()->all(), 'id','name'),
                        'positions' => ArrayHelper::map(Position::find()->asArray()->all(), 'id','name'),
                        'update' => $update,
                        'type' => $type
                ]);
                case Type::TYPE_AGENT:
                    return $this->render('agents/update', [
                        'model' => $model,
                        'factories' => ArrayHelper::map(Factory::find()->asArray()->all(), 'id', 'title'),
                        'update' => $update,
                        'type' => $type,
                ]);
            }
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        switch($model->type_id) {
            case Type::TYPE_PHARMACIST:
                $model->delete();
                return $this->redirect(['pharmacists']);
            case Type::TYPE_AGENT:
                $model->delete();
                return $this->redirect(['agents']);
        }
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Пользователь не существует.');
        }
    }

    public function actionAccept($id)
    {

        $model = $this->findModel($id);
        $model->verified();

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

        switch($model->type_id) {
            case 1:
                return $this->redirect(['pharmacists']);
            case 2:
                return $this->redirect(['agents']);
        }
    }

    public function actionBan($id)
    {
        $model = $this->findModel($id);
        $model->ban();

        switch($model->type_id) {
            case 1:
                return $this->redirect(['pharmacists']);
            case 2:
                return $this->redirect(['agents']);
        }
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

    public function actionPharmacyList() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $company_id = $parents[0];
                $city_id = $parents[1];
                $out = Pharmacy::getPharmacyList($company_id, $city_id);
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

}
