<?php

namespace backend\models\city;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\location\City;

class Search extends City
{

    public function rules()
    {
        return [
            ['name', 'string'],
            [['id', 'region_id'], 'integer'],
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
        $query = City::find();

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
            'region_id' => $this->region_id,
        ]);

        $query->andFilterWhere(['like', City::tableName().'.name', $this->name]);

        return $dataProvider;
    }
}
