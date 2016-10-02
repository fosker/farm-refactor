<?php

namespace backend\models\profile\pharmacist;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\user\Pharmacist;
use common\models\User;
use common\models\Company;
use common\models\company\Pharmacy;


class Search extends Pharmacist
{

    public function rules()
    {
        return [
            [['user.status', 'id', 'user.points', 'education_id', 'pharmacy_id', 'position_id',
                'pharmacy.city.id', 'pharmacy.company.id', 'user.inList', 'points_from', 'points_to'], 'integer'],
            [['user.name', 'user.login', 'user.email'], 'string'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['user.status', 'user.name', 'user.email', 'user.points',
            'pharmacy.city.id', 'pharmacy.company.id', 'user.inList', 'points_from', 'points_to', 'user.login']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Pharmacist::find()->joinWith(['user', 'pharmacy']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $dataProvider->sort->attributes['user.status'] = [
            'asc' => [User::tableName() . '.status' => SORT_ASC],
            'desc' => [User::tableName() . '.status' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['user.name'] = [
            'asc' => [User::tableName() . '.name' => SORT_ASC],
            'desc' => [User::tableName() . '.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['user.points'] = [
            'asc' => [User::tableName() . '.points' => SORT_ASC],
            'desc' => [User::tableName() . '.points' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['pharmacy.city.id'] = [
            'asc' => [Pharmacy::tableName().'.city_id' => SORT_ASC],
            'desc' => [Pharmacy::tableName().'.city_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['pharmacy.company.id'] = [
            'asc' => [Pharmacy::tableName().'.company_id' => SORT_ASC],
            'desc' => [Pharmacy::tableName().'.company_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['user.inList'] = [
            'asc' => [User::tableName().'.inList' => SORT_ASC],
            'desc' => [User::tableName().'.inList' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['user.login'] = [
            'asc' => [User::tableName().'.login' => SORT_ASC],
            'desc' => [User::tableName().'.login' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            Pharmacist::tableName().'.id' => $this->id,
            'education_id' => $this->education_id,
            'pharmacy_id' => $this->pharmacy_id,
            'position_id' => $this->position_id,
            User::tableName().'.inList' => $this->getAttribute('user.inList'),
            Pharmacy::tableName().'.city_id' => $this->getAttribute('pharmacy.city.id'),
            Pharmacy::tableName().'.company_id' => $this->getAttribute('pharmacy.company.id')
        ]);

        $query->andFilterWhere(['like', User::tableName() . '.name', $this->getAttribute('user.name')])
            ->andFilterWhere(['>=', 'points', $this->getAttribute('points_from')])
            ->andFilterWhere(['<=', 'points', $this->getAttribute('points_to')])
            ->andFilterWhere(['like', User::tableName() . '.login', $this->getAttribute('user.login')])
            ->andFilterWhere(['like', User::tableName() . '.status', $this->getAttribute('user.status')])
            ->andFilterWhere(['like', User::tableName() . '.email', $this->getAttribute('user.email')]);

        return $dataProvider;
    }
}