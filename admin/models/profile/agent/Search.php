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
            [['user.status', 'id', 'factory_id', 'user.inList', 'user.points', 'points_from', 'points_to'], 'integer'],
            [['user.name', 'user.login', 'user.email', 'user.comment'], 'string'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['user.status', 'user.name', 'user.email', 'user.points','user.inList', 'points_from', 'points_to', 'user.login', 'user.comment']);
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

        $dataProvider->sort->attributes['user.points'] = [
            'asc' => [User::tableName() . '.points' => SORT_ASC],
            'desc' => [User::tableName() . '.points' => SORT_DESC],
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
            Agent::tableName().'.id' => $this->id,
            'factory_id' => $this->factory_id,
            User::tableName().'.inList' => $this->getAttribute('user.inList'),
        ]);

        $query->andFilterWhere(['like', User::tableName() . '.name', $this->getAttribute('user.name')])
            ->andFilterWhere(['like', User::tableName() . '.email', $this->getAttribute('user.email')])
            ->andFilterWhere(['like', User::tableName() . '.login', $this->getAttribute('user.login')])
            ->andFilterWhere(['like', User::tableName() . '.comment', $this->getAttribute('user.comment')])
            ->andFilterWhere(['>=', 'points', $this->getAttribute('points_from')])
            ->andFilterWhere(['<=', 'points', $this->getAttribute('points_to')])
            ->andFilterWhere(['like', User::tableName() . '.status', $this->getAttribute('user.status')]);

        return $dataProvider;
    }
}
