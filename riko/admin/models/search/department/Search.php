<?php

namespace admin\models\search\department;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use admin\models\Department;

class Search extends Department
{

    public function rules()
    {
        return [
            ['name', 'string'],
            ['company_id', 'integer']
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
        $query = Department::find();

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
        ]);

        $query->andFilterWhere(['like', Department::tableName().'.name', $this->name]);

        return $dataProvider;
    }
}
