<?php

namespace backend\models\pharm_company;

use Yii;
use yii\data\ActiveDataProvider;

use common\models\PharmCompany;
use backend\models\Admin;

class Search extends PharmCompany
{

    public function rules()
    {
        return [
            [['id', 'admin_id'], 'integer'],
            [['name', 'type', 'location', 'size', 'rx_otc', 'first_visit', 'planned_visit', 'address'], 'safe'],
        ];
    }


    public function search($params)
    {

        $query = PharmCompany::find()
            ->joinWith('admin');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $dataProvider->sort->attributes['admin.name'] = [
            'asc' => [Admin::tableName().'.name' => SORT_ASC],
            'desc' => [Admin::tableName().'.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'admin_id' => $this->admin_id,
            'first_visit' => $this->first_visit,
            'planned_visit' => $this->planned_visit,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'location', $this->location])
            ->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'rx_otc', $this->rx_otc]);

        return $dataProvider;
    }
}