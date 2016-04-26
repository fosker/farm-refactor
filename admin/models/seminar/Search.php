<?php

namespace backend\models\seminar;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Seminar;
use common\models\seminar\Pharmacy;
use common\models\seminar\City;
use common\models\seminar\Education;

class Search extends Seminar
{

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['title','email'], 'string'],
            [['city_id', 'firm_id', 'education_id'], 'safe']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes() {
        return array_merge(parent::attributes(),['city_id', 'firm_id', 'education_id']);
    }

    public function search($params)
    {
        $query = Seminar::find();

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
            Seminar::tableName().'.id' => $this->id,
            'status' => $this->status,
        ]);

        if($this->getAttribute('city_id'))
            $cities = City::find()->select('seminar_id')->where(['in', 'city_id', $this->getAttribute('city_id')]);
        if($this->getAttribute('firm_id'))
            $firms = Pharmacy::find()->select('seminar_id')->where(['in', 'firm_id', $this->getAttribute('firm_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('seminar_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['in', Seminar::tableName().'.id', $education])
            ->andFilterWhere(['in', Seminar::tableName().'.id', $cities])
            ->andFilterWhere(['in', Seminar::tableName().'.id', $firms]);

        return $dataProvider;
    }
}
