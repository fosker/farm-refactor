<?php

namespace rest\versions\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Controller;

use common\models\Factory;
use common\models\Stock;
use common\models\stock\Reply;
use common\models\factory\Product;
use yii\web\UploadedFile;

class FactoryController extends Controller
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
            'query' => Factory::getForCurrentUser(),
        ]);
    }

    public function actionAll() {
        return new ActiveDataProvider([
            'query' => Factory::find(),
        ]);
    }

    public function actionView($id) {
        return Factory::findOne($id);
    }

    public function actionProducts($factory_id) {
        return Factory::findOne($factory_id)->products;
    }

    public function actionProduct($id) {
        return Product::findOne($id);
    }

    public function actionStocks($factory_id) {
        return new ActiveDataProvider([
            'query' => Stock::getForCurrentUser()->andWhere(['factory_id'=>$factory_id]),
        ]);
    }

    public function actionStock($id) {
        return Stock::getOneForCurrentUser($id);
    }

    public function actionReply() {

        $reply = new Reply();

        if($reply->load(Yii::$app->request->post(),'')) {
                $reply->image = UploadedFile::getInstance($reply, 'image');
                $reply->user_id = Yii::$app->user->id;
                if ($reply->validate()) {
                    $reply->saveImage();
                    $reply->save(false);
                    return ['success'=>true];
                }
        }
        return $reply;
    }

}