<?php

namespace backend\models\pharmacy;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\agency\Pharmacy;

class Search extends Pharmacy
{

    public function rules()
    {
        return [
            [['id', 'city_id', 'firm_id'], 'integer'],
            [['address', 'name'], 'string']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Pharmacy::find();

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
            'firm_id' => $this->firm_id,
            'city_id' => $this->city_id,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
