<?php

namespace backend\models\theme\answer;

use common\models\Theme;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\theme\Answer;
use common\models\User;


class Search extends Answer
{
    public function rules()
    {
        return [
            [['theme_id'], 'integer'],
            [['theme.title', 'user.login', 'user.name', 'date_added', 'user.email'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['theme.title', 'user.login', 'user.name', 'user.email']);
    }

    public function search($params)
    {
        $query = Answer::find()->joinWith(['user','theme']);;

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

        $dataProvider->sort->attributes['user.name'] = [
            'asc' => [User::tableName().'.name' => SORT_ASC],
            'desc' => [User::tableName().'.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['theme.title'] = [
            'asc' => [Theme::tableName().'.title' => SORT_ASC],
            'desc' => [Theme::tableName().'.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'theme_id' => $this->theme_id,
        ]);

        $query->andFilterWhere(['like', Answer::tableName().'.date_added', $this->date_added])
            ->andFilterWhere(['like', User::tableName().'.email', $this->getAttribute('user.email')])
            ->andFilterWhere(['like', Theme::tableName().'.title', $this->getAttribute('theme.title')])
            ->andFilterWhere(['like', User::tableName().'.login', $this->getAttribute('user.login')])
            ->andFilterWhere(['like', User::tableName().'.name', $this->getAttribute('user.name')]);

        return $dataProvider;
    }
}
