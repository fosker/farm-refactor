<?php

namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

use common\models\location\Region;
use common\models\Company;
use common\models\location\City;
use common\models\company\Pharmacy;
use common\models\user\Pharmacist;
use common\models\user\Agent;
use common\models\User;


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

        $dateQuery = User::find()->select('date_reg')->orderBy('date_reg DESC');

        $dates = new ActiveDataProvider([
            'query' => $dateQuery,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $date_reg_array = ArrayHelper::map(User::find()->select('id, date_reg')
            ->orderBy('date_reg DESC')
            ->asArray()
            ->all(), 'id', 'date_reg');

        $months = [1 => 'январь', 2 => 'февраль', 3 => 'март', 4 => 'апрель', 5 => 'май', 6 => 'июнь',
            7 => 'июль', 8 => 'август', 9 => 'сентябрь', 10 => 'октябрь', 11 => 'ноябрь', 12 => 'декабрь'
        ];

        foreach($date_reg_array as $date_reg) {
            $year = substr($date_reg, 0, 4);
            if($year != '2015')
                $years[] = $year;
        }

        $years = array_unique($years);

        $count_in_month = Pharmacist::find()->select('count('.Pharmacist::tableName().'.id'.') as count,
        month(date_reg) as month, year(date_reg) as year')
            ->joinWith('user')
            ->where('year(date_reg) > 2015')
            ->groupBy(['month', 'year'])
            ->orderBy('month')
            ->asArray()
            ->all();

        $calendar = [];

        foreach($years as $year) {
            foreach($count_in_month as $month) {
                if($month['year'] == $year) {
                    $calendar[$year][$month['month']] = $month['count'];
                }
            }
        }
        $region_month = Pharmacist::find()
            ->select('count('.Pharmacist::tableName().'.id'.') as count, '.Region::tableName().'.name,
            month(date_reg) as month, year(date_reg) as year')
            ->joinWith('user')
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Pharmacy::tableName().'.id = '.Pharmacist::tableName().'.pharmacy_id')
            ->join('LEFT JOIN', City::tableName(),
                Pharmacy::tableName().'.city_id = '.City::tableName().'.id')
            ->join('LEFT JOIN', Region::tableName(),
                Region::tableName().'.id = '.City::tableName().'.region_id')
            ->where('year(date_reg) > 2015')
            ->groupBy([Region::tableName().'.id', 'month', 'year'])
            ->asArray()
            ->all();

        $user_region_month = Pharmacist::find()
            ->select(User::tableName().'.name as name,'.Region::tableName().'.name as region, month(date_reg) as month,
            year(date_reg) as year, date_reg')
            ->joinWith('user')
            ->join('LEFT JOIN', Pharmacy::tableName(),
                Pharmacy::tableName().'.id = '.Pharmacist::tableName().'.pharmacy_id')
            ->join('LEFT JOIN', City::tableName(),
                Pharmacy::tableName().'.city_id = '.City::tableName().'.id')
            ->join('LEFT JOIN', Region::tableName(),
                Region::tableName().'.id = '.City::tableName().'.region_id')
            ->where('year(date_reg) > 2015')
            ->orderBy('date_reg DESC')
            ->asArray()
            ->all();

        $pharmacists = Pharmacist::find()->count();
        $agents = Agent::find()->count();

        return $this->render('index', [
            'regions' => $regions,
            'companies' => $companies,
            'pharmacists' => $pharmacists,
            'agents' => $agents,
            'months' => $months,
            'years' => $years,
            'calendar' => $calendar,
            'region_month' => $region_month,
            'user_region_month' => $user_region_month
        ]);
    }

}