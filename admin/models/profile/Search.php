<?php

namespace backend\models\profile;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\agency\Firm;
use common\models\location\City;

class Search extends User
{

    public function rules()
    {
        return [
            [['status', 'id'], 'integer'],
            [['name', 'sex', 'email'], 'string'],
            [['position_id', 'pharmacy_id', 'firm', 'city_id'], 'safe'],
        ];
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['firm', 'city_id']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find();
        $query->joinWith(['pharmacy.firm', 'pharmacy.city']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'status'=>SORT_ASC,
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $dataProvider->sort->attributes['firm'] = [
            'asc' => [Firm::tableName().'.name' => SORT_ASC],
            'desc' => [Firm::tableName().'.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['city_id'] = [
            'asc' => [City::tableName().'.name' => SORT_ASC],
            'desc' => [City::tableName().'.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            User::tableName().'.id' => $this->id,
            'position_id' => $this->position_id,
            'pharmacy_id' => $this->pharmacy_id,
            'status' => $this->status,
            City::tableName().'.id' => $this->city_id,

        ]);

        $query->andFilterWhere(['like',  User::tableName().'.name', $this->name])
            ->andFilterWhere(['like',  User::tableName().'.sex', $this->sex])
            ->andFilterWhere(['like',  User::tableName().'.email', $this->email])
            ->andFilterWhere(['like', Firm::tableName().'.id', $this->getAttribute('firm')]);

        return $dataProvider;
    }
}
