<?php

namespace rest\versions\v1\controllers;

use common\models\Presentation;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use rest\components\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use common\models\Survey;

class ContentController extends Controller
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
            'query' => (new Query())
                ->select('*')
                ->from(['t' =>
                    Survey::getForCurrentUser()
                        ->select(['id', 'title', 'points', 'thumbnail'])
                        ->union(Presentation::getForCurrentUser()
                            ->select(['id', 'title', 'points', 'thumbnail'])
                        )
                ])
                ->orderBy(['id' => SORT_DESC]),
        ]);
    }
}