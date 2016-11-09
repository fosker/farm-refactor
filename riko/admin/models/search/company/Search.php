<?php

namespace admin\models\search\company;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use admin\models\Company;

class Search extends Company
{

    public function rules()
    {
        return [
            ['name', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [

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

        $query->andFilterWhere(['like', Company::tableName().'.name', $this->name]);

        return $dataProvider;
    }
}
