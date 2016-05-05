<?php
namespace rest\versions\v1\controllers;


use Yii;
use yii\db\Expression;
use yii\rest\Controller;
use yii\data\ActiveDataProvider;

use common\models\User;
use common\models\Company;
use common\models\company\Pharmacy;
use common\models\location\City;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\profile\Position;
use common\models\profile\Type;


class ListController extends Controller
{

    public function actionRegions()
    {
        return Region::find()->asArray()->all();
    }

    public function actionCities($region,$search=null)
    {
        return City::find()
            ->select(['id','name'])
            ->where(['region_id'=>$region])
            ->andFilterWhere(['like', 'name', $search])
            ->orderBy("(CASE WHEN name LIKE '$search' THEN 1 WHEN name LIKE '$search%' THEN 2 ELSE 3 END)")
            ->asArray()
            ->all();
    }

    public function actionCompanies($search)
    {
        return new ActiveDataProvider([
            'query' => Company::find()
                ->andFilterWhere(['like','title',$search])
        ]);
    }

    public function actionPharmacies($city, $company, $search=null)
    {
        return Pharmacy::find()
            ->select(['id', new \yii\db\Expression("CONCAT(`name`, ' (', `address`,')') as name")])
            ->andFilterWhere(['city_id'=>$city])
            ->andFilterWhere(['company_id'=>$company])
            ->andFilterWhere(['or', ['like','name',$search],['like','address',$search]])
            ->asArray()
            ->all();
    }

    public function actionEducation()
    {
        return Education::find()->asArray()->all();
    }

    public function actionPositions()
    {
        return Position::find()->asArray()->all();
    }

    public function actionTypes()
    {
        return Type::find()->asArray()->all();
    }

    public function actionSex()
    {
        return [
            ['id' => User::SEX_MALE, 'name' => 'мужской'],
            ['id'=>User::SEX_FEMALE,'name' => 'женский']
        ];
    }

}