<?php

namespace admin\models\search\employee;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use admin\models\Employee;

class Search extends Employee
{

    public function rules()
    {
        return [
            [['name', 'surname', 'phone'], 'string'],
            [['department_id', 'position_id'], 'integer']
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
        $query = Employee::find();

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
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
        ]);

        $query->andFilterWhere(['like', Employee::tableName().'.name', $this->name])
            ->andFilterWhere(['like', Employee::tableName().'.surname', $this->surname])
            ->andFilterWhere(['like', Employee::tableName().'.phone', $this->phone]);

        return $dataProvider;
    }
}
