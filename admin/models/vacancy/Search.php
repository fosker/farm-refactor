<?php

namespace backend\models\vacancy;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Vacancy;
use common\models\vacancy\Pharmacy;

class Search extends Vacancy
{

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['title','email'], 'string'],
            [['company_id', 'firm_id', 'pharmacy_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['firm_id', 'company_id', 'pharmacy_id']);
    }

    public function search($params)
    {
        $query = Vacancy::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'status' => SORT_ASC,
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Vacancy::tableName().'.id' => $this->id,
            'status' => $this->status,
        ]);

        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('vacancy_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('vacancy_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['in', Vacancy::tableName().'.id', $companies])
            ->andFilterWhere(['in', Vacancy::tableName().'.id', $pharmacies]);

        return $dataProvider;
    }
}
