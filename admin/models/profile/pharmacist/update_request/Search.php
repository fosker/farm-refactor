<?php

namespace backend\models\profile\pharmacist\update_request;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\profile\PharmacistUpdateRequest;
use common\models\User;

class Search extends PharmacistUpdateRequest
{

    public function rules()
    {
        return [
            [['pharmacist_id'], 'integer'],
            [['name', 'date_add'], 'string'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = PharmacistUpdateRequest::find();

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
            'pharmacist_id' => $this->pharmacist_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'date_add', $this->date_add]);


        return $dataProvider;
    }
}