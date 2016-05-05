<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\company\Pharmacy;
use yii\helpers\Json;


class ListController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'pharmacies' => ['POST'],
                ],
            ],

        ];
    }

    public function actionPharmacies()
    {

        if(Yii::$app->request->post('cities') && Yii::$app->request->post('companies')) {
            $cities = Yii::$app->request->post('cities');
            $companies = Yii::$app->request->post('companies');
            $pharmacies = Pharmacy::find()
                ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
                ->where(['in', 'city_id', $cities])
                ->andWhere(['in', 'company_id', $companies])
                ->asArray()
                ->all();
            echo Json::encode($pharmacies);
        }

    }
}
