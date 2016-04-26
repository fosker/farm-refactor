<?php

namespace backend\models\seminar\sign;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\seminar\Entry;
use common\models\User;
use common\models\Seminar;

class Search extends Entry
{
    public function rules()
    {
        return [
            [['id', 'seminar_id', 'user.id'], 'integer'],
            [['seminar.title', 'user.login', 'date_add'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['seminar.title', 'user.login', 'user.id']);
    }

    public function search($params)
    {
        $query = Entry::find()->joinWith(['user','seminar']);

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

        $dataProvider->sort->attributes['seminar.title'] = [
            'asc' => [Seminar::tableName().'.title' => SORT_ASC],
            'desc' => [Seminar::tableName().'.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'seminar_id' => $this->seminar_id,
        ]);

        $query->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')])
            ->andFilterWhere(['like', 'date_add', $this->date_add])
            ->andFilterWhere(['like', Seminar::tableName().'.title', $this->getAttribute('seminar.title')]);

        return $dataProvider;
    }
}
