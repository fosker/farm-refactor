<?php

namespace admin\models\search\position;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use admin\models\Position;

class Search extends Position
{

    public function rules()
    {
        return [
            ['name', 'string']
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
        $query = Position::find();

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

        $query->andFilterWhere(['like', Position::tableName().'.name', $this->name]);

        return $dataProvider;
    }
}
