<?php

namespace backend\models\education;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\profile\Education;

class Search extends Education
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'string'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Education::find();

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

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
