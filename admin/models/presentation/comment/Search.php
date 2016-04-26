<?php

namespace backend\models\presentation\comment;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\presentation\Comment;
use common\models\User;

class Search extends Comment
{

    public function rules()
    {
        return [
            [['id', 'presentation_id', 'user.id'], 'integer'],
            [['comment','user.name', 'date_add'], 'string'],
        ];
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['user.name', 'user.id', 'date_add']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Comment::find()->joinWith('user');;

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
            'presentation_id' => $this->presentation_id,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'date_add', $this->date_add])
            ->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')]);

        return $dataProvider;
    }
}
