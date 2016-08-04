<?php

namespace backend\controllers;

use Yii;

use backend\models\Param;

use yii\base\Model;
use common\models\location\Region;
use common\models\Company;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

class MainController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user'=>'admin',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return $action->id == 'index' ? true : Yii::$app->admin->identity->can($action);
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $regions = new ActiveDataProvider([
            'query' => Region::find()->orderBy('name'),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $companies = new ActiveDataProvider([
            'query' => Company::find()->orderBy('title'),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'regions' => $regions,
            'companies' => $companies
        ]);
    }

}