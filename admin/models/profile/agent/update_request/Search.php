<?php

namespace backend\models\profile\agent\update_request;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\profile\AgentUpdateRequest;
use common\models\User;

class Search extends AgentUpdateRequest
{

    public function rules()
    {
        return [
            [['agent_id'], 'integer'],
            [['name', 'date_add', 'user.inList'], 'string'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['user.inList']);
    }

    public function search($params)
    {
        $query = AgentUpdateRequest::find()->joinWith('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_add'=>SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);


        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'agent_id' => $this->agent_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'date_add', $this->date_add])
            ->andFilterWhere(['like', User::tableName().'.inList', $this->getAttribute('user.inList')]);


        return $dataProvider;
    }
}