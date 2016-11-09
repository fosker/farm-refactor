<?php

namespace admin\models\search\criterion;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use admin\models\Criterion;

class Search extends Criterion
{

    public function rules()
    {
        return [
            [['name', 'abbr'], 'string'],
            ['type', 'integer']
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
        $query = Criterion::find();

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
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', Criterion::tableName().'.name', $this->name])
            ->andFilterWhere(['like', Criterion::tableName().'.abbr', $this->abbr]);

        return $dataProvider;
    }
}
