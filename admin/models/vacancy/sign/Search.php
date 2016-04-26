<?php

namespace backend\models\vacancy\sign;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\vacancy\Entry;
use common\models\User;
use common\models\Vacancy;

class Search extends Entry
{
    public function rules()
    {
        return [
            [['id', 'vacancy_id', 'user.id'], 'integer'],
            [['vacancy.title', 'user.login', 'date_add'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['vacancy.title', 'user.login', 'user.id']);
    }

    public function search($params)
    {
        $query = Entry::find()->joinWith(['user','vacancy']);

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

        $dataProvider->sort->attributes['vacancy.title'] = [
            'asc' => [Vacancy::tableName().'.title' => SORT_ASC],
            'desc' => [Vacancy::tableName().'.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'vacancy_id' => $this->vacancy_id,
        ]);

        $query->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')])
            ->andFilterWhere(['like', 'date_add', $this->date_add])
            ->andFilterWhere(['like', Vacancy::tableName().'.title', $this->getAttribute('vacancy.title')]);

        return $dataProvider;
    }
}
