<?php

namespace backend\models\stock;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Stock;
use common\models\stock\Pharmacy;
use common\models\stock\Education;
use common\models\stock\Type;

class Search extends Stock
{

    public function rules()
    {
        return [
            [['id', 'factory_id', 'status'], 'integer'],
            ['title', 'string'],
            [['company_id', 'education_id', 'type_id', 'pharmacy_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['city_id', 'company_id', 'education_id', 'type_id', 'pharmacy_id']);
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

        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('stock_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('stock_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);
        if($this->getAttribute('type_id'))
            $types = Type::find()->select('stock_id')->andFilterWhere(['in', 'type_id', $this->getAttribute('type_id')]);
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('stock_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Stock::tableName().'.id', $education])
            ->andFilterWhere(['in', Stock::tableName().'.id', $companies])
            ->andFilterWhere(['in', Stock::tableName().'.id', $types])
            ->andFilterWhere(['in', Stock::tableName().'.id', $pharmacies]);
        
        return $dataProvider;
    }
}
