<?php

namespace backend\models\present;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Item;
use common\models\shop\Pharmacy;

class Search extends Item
{

    public function rules()
    {
        return [
            [['id', 'vendor_id','priority', 'status'], 'integer'],
            [['title'], 'string'],
            [['company_id', 'firm_id', 'pharmacy_id'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['firm_id', 'company_id', 'pharmacy_id']);
    }


    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Item::find();

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
            Item::tableName().'.id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'priority' => $this->priority,
            'status' => $this->status,
        ]);

        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('item_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('item_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Item::tableName().'.id', $companies])
            ->andFilterWhere(['in', Item::tableName().'.id', $pharmacies]);

        $query->groupBy(Item::tableName().'.id');

        return $dataProvider;
    }
}
