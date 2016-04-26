<?php

namespace backend\models\news\comment;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\news\Comment;
use common\models\User;

class Search extends Comment
{

    public function rules()
    {
        return [
            [['id', 'news_id', 'user.id'], 'integer'],
            [['comment', 'user.name', 'date_add'], 'string'],
        ];
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['user.name', 'user.id']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Comment::find()->joinWith(['user','news']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_add'=>SORT_ASC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes['user.name'] = [
            'asc' => [User::tableName().'.name' => SORT_ASC],
            'desc' => [User::tableName().'.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'news_id' => $this->news_id,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')])
            ->andFilterWhere(['like', 'date_add', $this->date_add]);

        return $dataProvider;
    }
}
