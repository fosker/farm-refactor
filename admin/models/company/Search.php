<?php

namespace backend\models\company;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Company;


class Search extends Company
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['title', 'description', 'image', 'logo'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Company::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'logo', $this->logo]);

        return $dataProvider;
    }
} 