<?php

namespace backend\models\pharmacy;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\company\Pharmacy;

class Search extends Pharmacy
{

    public function rules()
    {
        return [
            [['id', 'city_id', 'company_id'], 'integer'],
            [['address', 'name', 'date_from', 'date_to'], 'string']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['date_from', 'date_to']);
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
            'company_id' => $this->company_id,
            'city_id' => $this->city_id,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['>=', 'date_visit', $this->getAttribute('date_from')])
            ->andFilterWhere(['<=', 'date_visit', $this->getAttribute('date_to')])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
