<?php

namespace backend\models\profile\agent;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\user\Agent;
use common\models\User;

class Search extends Agent
{

    public function rules()
    {
        return [
            [['user.status', 'id', 'factory_id'], 'integer'],
            [['user.name', 'user.email'], 'string'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['user.status', 'user.name', 'user.email']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Agent::find()->joinWith(['user']);

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

        $dataProvider->sort->attributes['user.email'] = [
            'asc' => [User::tableName() . '.email' => SORT_ASC],
            'desc' => [User::tableName() . '.email' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Agent::tableName().'.id' => $this->id,
            'factory_id' => $this->factory_id,
        ]);

        $query->andFilterWhere(['like', User::tableName() . '.name', $this->getAttribute('user.name')])
            ->andFilterWhere(['like', User::tableName() . '.email', $this->getAttribute('user.email')])
            ->andFilterWhere(['like', User::tableName() . '.status', $this->getAttribute('user.status')]);

        return $dataProvider;
    }
}
