<?php

namespace backend\models\profile\update_request;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\profile\UpdateRequest;
use common\models\User;

class Search extends UpdateRequest
{

    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['name', 'date_add'], 'string'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = UpdateRequest::find();

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
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'date_add', $this->date_add]);


        return $dataProvider;
    }
}
