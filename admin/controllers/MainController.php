<?php

namespace backend\controllers;

use common\models\user\Pharmacist;
use common\models\user\Agent;
use Yii;

use common\models\location\Region;
use common\models\Company;
use common\models\location\City;
use common\models\company\Pharmacy;
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
        $regionQuery = Region::find();
        $regionCount = Pharmacist::find()
            ->select('count('.Pharmacist::tableName().'.id'.') as count, region_id')
            ->joinWith('pharmacy')
            ->join('LEFT JOIN', City::tableName(),
                Pharmacy::tableName().'.city_id = '.City::tableName().'.id')
            ->join('LEFT JOIN', Region::tableName(),
                Region::tableName().'.id = '.City::tableName().'.region_id')
            ->groupBy('region_id');
        $regionQuery->leftJoin(['regionCount' => $regionCount], 'regionCount.region_id = id')
            ->orderBy(['regionCount.count' => SORT_DESC]);
        $regions = new ActiveDataProvider([
            'query' => $regionQuery,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $companyQuery = Company::find();
        $companyCount = Pharmacist::find()
            ->select('count('.Pharmacist::tableName().'.id'.') as count, company_id')
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Pharmacist::tableName().'.pharmacy_id = '.Pharmacy::tableName().'.id')
            ->groupBy('company_id');
        $companyQuery->leftJoin(['companyCount' => $companyCount], 'companyCount.company_id = id')
            ->orderBy(['companyCount.count' => SORT_DESC]);
        $companies = new ActiveDataProvider([
            'query' => $companyQuery,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $pharmacists = Pharmacist::find()->joinWith('user')->where(['status' => 1])->count();
        $agents = Agent::find()->joinWith('user')->where(['status' => 1])->count();

        return $this->render('index', [
            'regions' => $regions,
            'companies' => $companies,
            'pharmacists' => $pharmacists,
            'agents' => $agents
        ]);
    }

}