<?php

namespace backend\models\factory\answer;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\factory\Reply;
use common\models\factory\Stock;
use common\models\User;

/**
 * Search represents the model behind the search form about `common\models\factory\Reply`.
 */
class Search extends Reply
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stock_id', 'user.id'], 'integer'],
            [['stock.title', 'user.login', 'date_add'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['stock.title', 'user.login', 'user.id']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Reply::find()->joinWith(['user','stock']);;


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_add'=>SORT_DESC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes['user.login'] = [
            'asc' => [User::tableName().'.login' => SORT_ASC],
            'desc' => [User::tableName().'.login' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['stock.title'] = [
            'asc' => [Stock::tableName().'.title' => SORT_ASC],
            'desc' => [Stock::tableName().'.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'stock_id' => $this->stock_id,
        ]);

        $query->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')])
            ->andFilterWhere(['like', Reply::tableName().'.date_add', $this->date_add])
            ->andFilterWhere(['like', Stock::tableName().'.title', $this->getAttribute('stock.title')]);

        return $dataProvider;
    }
}
