<?php

namespace backend\models\vacancy;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Vacancy;
use common\models\vacancy\Pharmacy;
use common\models\vacancy\City;

class Search extends Vacancy
{

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['title','email'], 'string'],
            [['city_id', 'firm_id'], 'safe']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes() {
        return array_merge(parent::attributes(),['city_id', 'firm_id']);
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

        if($this->getAttribute('city_id'))
            $cities = City::find()->select('vacancy_id')->where(['in', 'city_id', $this->getAttribute('city_id')]);
        if($this->getAttribute('firm_id'))
            $firms = Pharmacy::find()->select('vacancy_id')->where(['in', 'firm_id', $this->getAttribute('firm_id')])
                ->joinWith('pharmacy');

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['in', Vacancy::tableName().'.id', $cities])
            ->andFilterWhere(['in', Vacancy::tableName().'.id', $firms]);

        return $dataProvider;
    }
}
