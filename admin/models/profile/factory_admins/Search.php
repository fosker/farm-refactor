<?php

namespace backend\models\profile\factory_admins;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\factory\Admin;

class Search extends Admin
{

    public function rules()
    {
        return [
            [['status', 'id', 'factory_id'], 'integer'],
            [['email', 'login', 'name'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Admin::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
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
            'factory_id' => $this->factory_id,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', Admin::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', Admin::tableName() . '.login', $this->login])
            ->andFilterWhere(['like', Admin::tableName() . '.email', $this->email]);

        return $dataProvider;
    }
}
