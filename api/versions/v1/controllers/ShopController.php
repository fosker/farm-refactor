<?php

namespace rest\versions\v1\controllers;

use Yii;

use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use common\models\Item;
use common\models\shop\Desire;
use common\models\shop\Present;
use yii\web\BadRequestHttpException;

class ShopController extends Controller
{

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBearerAuth::className(),
                    QueryParamAuth::className(),
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

    public function actionIndex() {
        return new ActiveDataProvider([
            'query' => Item::getForCurrentUser(),
        ]);
    }

    public function actionView($id) {
        return Item::getOneForCurrentUser($id);
    }

    public function actionPresents() {
        return new ActiveDataProvider([
            'query' => Present::getForCurrentUser(),
        ]);
    }

    public function actionPresent($id) {
        return Present::findOne($id);
    }

    public function actionAddPresent() {
        $present = new Present();

        if($present->load(Yii::$app->request->post(), '') && $present->validate(['count','item_id'])) {
            $present->user_id = Yii::$app->user->id;
            $present->promo = Yii::$app->security->generateRandomString(8);
            Yii::$app->user->identity->pay($present->item->points*$present->count);
            $present->save(false);
            $item = Item::findOne($present->item_id);
            $item->count -= 1;
            $item->save(false);
            return ['success'=>true];
        } else return $present;
    }

    public function actionDesires() {
        return new ActiveDataProvider([
            'query' => Desire::getForCurrentUser(),
        ]);
    }

    public function actionDesire($id) {
        return Desire::findOne($id);
    }

    public function actionAddDesire() {
        $desire = new Desire();

        $desire->user_id = Yii::$app->user->id;

        if($desire->load(Yii::$app->request->post(), '') && $desire->save()) {
            return ['success'=>true];
        } else return $desire;
    }

    public function actionDeleteDesire($id) {
        if ($desire = Desire::findOne(['id'=>$id,'user_id'=>Yii::$app->user->id])) {
            $desire->delete();
            return ['success'=>true];
        }
        else {
            throw new BadRequestHttpException;
        }
    }
}