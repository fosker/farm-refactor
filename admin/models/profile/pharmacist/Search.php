<?php

namespace backend\models\profile\pharmacist;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\user\Pharmacist;
use common\models\User;
use common\models\Company;
use common\models\company\Pharmacy;
use common\models\profile\Device;

class Search extends Pharmacist
{
    public function rules()
    {
        return [
            [['user.status', 'id', 'user.points', 'education_id', 'pharmacy_id', 'position_id',
                'pharmacy.city.id', 'pharmacy.company.id', 'user.inList', 'points_from', 'points_to'], 'integer'],
            [['user.name', 'user.login', 'user.email', 'user.comment', 'user.phone', 'date_reg_from', 'date_reg_to',
                'date_birth_from', 'date_birth_to', 'device.type', 'device.version_from', 'device.version_to', 'sex'], 'string'],
        ];
    }
    public function attributes()
    {
        return array_merge(parent::attributes(), ['user.status', 'user.name', 'user.email', 'user.points',
            'pharmacy.city.id', 'pharmacy.company.id', 'user.inList', 'points_from', 'points_to', 'user.login',
            'user.comment', 'user.phone', 'date_reg_from', 'date_reg_to', 'date_birth_from', 'date_birth_to',
            'device.type', 'device.version_from', 'device.version_to']);
    }
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Pharmacist::find()->joinWith(['user', 'pharmacy', 'user.devices']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
            ],
            'pagination' => [
                'pageSizeLimit' => [0, 10000],
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
            Pharmacy::tableName().'.company_id' => $this->getAttribute('pharmacy.company.id'),
            Device::tableName().'.type' => $this->getAttribute('device.type')
        ]);
        $query->andFilterWhere(['like', User::tableName() . '.name', $this->getAttribute('user.name')])
            ->andFilterWhere(['>=', 'points', $this->getAttribute('points_from')])
            ->andFilterWhere(['<=', 'points', $this->getAttribute('points_to')])
            ->andFilterWhere(['>=', 'date_reg', $this->getAttribute('date_reg_from')])
            ->andFilterWhere(['<=', 'date_reg', $this->getAttribute('date_reg_to')])
            ->andFilterWhere(['>=', 'date_birth', $this->getAttribute('date_birth_from')])
            ->andFilterWhere(['<=', 'date_birth', $this->getAttribute('date_birth_to')])
            ->andFilterWhere(['like', User::tableName() . '.login', $this->getAttribute('user.login')])
            ->andFilterWhere(['like', User::tableName() . '.phone', $this->getAttribute('user.phone')])
            ->andFilterWhere(['like', User::tableName() . '.comment', $this->getAttribute('user.comment')])
            ->andFilterWhere(['like', User::tableName() . '.status', $this->getAttribute('user.status')])
            ->andFilterWhere(['like', User::tableName() . '.email', $this->getAttribute('user.email')])
            ->andFilterWhere(['sex' => $this->sex])
            ->andFilterWhere(['>=', 'version', $this->getAttribute('device.version_from')])
            ->andFilterWhere(['<=', 'version', $this->getAttribute('device.version_to')])
            ->groupBy(User::tableName().'.id');
        return $dataProvider;
    }
}