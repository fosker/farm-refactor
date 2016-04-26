<?php

namespace rest\versions\v1\controllers;

use Yii;

use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\rest\Controller;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use common\models\Substance;
use common\models\substance\Request;

class SubstanceController extends Controller
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

    public function actionIndex($part = null) {
        return new ActiveDataProvider([
            'query' => Substance::findByPart($part),
        ]);
    }

    public function actionView($id) {
        Request::add($id);
        return Substance::findOne($id);
    }
}