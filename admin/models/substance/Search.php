<?php

namespace backend\models\substance;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Substance;

class Search extends Substance
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['cyrillic', 'name', 'description'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Substance::find();

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

        $query->andFilterWhere(['like', 'cyrillic', $this->cyrillic])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
