<?php

namespace backend\models\present\vendor;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\shop\Vendor;

class Search extends Vendor
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'email'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Vendor::find();

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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
