<?php

namespace backend\models\present;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Item;
use common\models\shop\City;
use common\models\shop\Pharmacy;

class Search extends Item
{

    public function rules()
    {
        return [
            [['id', 'vendor_id','priority', 'status'], 'integer'],
            [['title'], 'string'],
            [['city_id', 'firm_id'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['city_id', 'firm_id']);
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

        $cities = City::find()->select('item_id')->andFilterWhere(['in', 'city_id', $this->getAttribute('city_id')]);
        $firms = Pharmacy::find()->select('item_id')->andFilterWhere(['in', 'firm_id', $this->getAttribute('firm_id')])
            ->joinWith('pharmacy');

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Item::tableName().'.id', $cities])
            ->andFilterWhere(['in', Item::tableName().'.id', $firms]);

        $query->groupBy(Item::tableName().'.id');

        return $dataProvider;
    }
}
