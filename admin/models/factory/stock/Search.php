<?php

namespace backend\models\factory\stock;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\factory\Stock;
use common\models\factory\City;
use common\models\factory\Pharmacy;
use common\models\factory\Education;

class Search extends Stock
{

    public function rules()
    {
        return [
            [['id', 'factory_id', 'status'], 'integer'],
            ['title', 'string'],
            [['city_id', 'firm_id', 'education_id'], 'safe'],
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
        $query = Stock::find();

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
            'id' => $this->id,
            'factory_id' => $this->factory_id,
            'status' => $this->status,
        ]);

        if($this->getAttribute('city_id'))
            $cities = City::find()->select('stock_id')->where(['in', 'city_id', $this->getAttribute('city_id')]);
        if($this->getAttribute('firm_id'))
            $firms = Pharmacy::find()->select('stock_id')->where(['in', 'firm_id', $this->getAttribute('firm_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('stock_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Stock::tableName().'.id', $education])
            ->andFilterWhere(['in', Stock::tableName().'.id', $cities])
            ->andFilterWhere(['in', Stock::tableName().'.id', $firms]);
        
        return $dataProvider;
    }
}
