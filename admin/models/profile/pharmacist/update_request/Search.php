<?php

namespace backend\models\profile\pharmacist\update_request;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\profile\PharmacistUpdateRequest;
use common\models\User;

class Search extends PharmacistUpdateRequest
{

    public function rules()
    {
        return [
            [['pharmacist_id'], 'integer'],
            [['name', 'date_add', 'user.inList'], 'string'],
        ];
    }
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['user.inList']);
    }

    public function search($params)
    {
        $query = PharmacistUpdateRequest::find()->joinWith('user');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_add'=>SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);


        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pharmacist_id' => $this->pharmacist_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'date_add', $this->date_add])
            ->andFilterWhere(['like', User::tableName().'.inList', $this->getAttribute('user.inList')]);


        return $dataProvider;
    }
}