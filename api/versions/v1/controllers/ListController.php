<?php
namespace rest\versions\v1\controllers;


use Yii;
use yii\db\Expression;
use yii\rest\Controller;

use common\models\User;
use common\models\agency\Firm;
use common\models\agency\Pharmacy;
use common\models\location\City;
use common\models\location\Region;
use common\models\profile\Education;
use common\models\profile\Position;

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

    public function actionFirms($search)
    {
        return Firm::find()
            ->andFilterWhere(['like','name',$search])
            ->asArray()
            ->all();
    }

    public function actionPharmacies($city, $firm, $search=null)
    {
        return Pharmacy::find()
            ->select(['id',new Expression("CONCAT(`name`, ', ', `address`) as name")])
            ->andFilterWhere(['city_id'=>$city])
            ->andFilterWhere(['firm_id'=>$firm])
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

    public function actionSex()
    {
        return [
            ['id' => User::SEX_MALE, 'name' => 'мужской'],
            ['id'=>User::SEX_FEMALE,'name' => 'женский']
        ];
    }

}