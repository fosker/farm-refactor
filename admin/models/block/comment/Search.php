<?php

namespace backend\models\block\comment;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\block\Comment;
use common\models\User;

class Search extends Comment
{

    public function rules()
    {
        return [
            [['id', 'block_id', 'user.id'], 'integer'],
            [['comment', 'date_add', 'user.name'], 'string'],
        ];
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['user.id', 'user.name']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Comment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_add'=>SORT_ASC,
                ],
            ],
        ]);

        $query->joinWith('user');

        $dataProvider->sort->attributes['user.name'] = [
            'asc' => [User::tableName().'.name' => SORT_ASC],
            'desc' => [User::tableName().'.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'block_id' => $this->block_id,
        ]);

        $query->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')])
            ->andFilterWhere(['like', Comment::tableName().'.date_add', $this->date_add])
            ->andFilterWhere(['like', Comment::tableName().'.comment', $this->comment]);

        return $dataProvider;
    }
}
