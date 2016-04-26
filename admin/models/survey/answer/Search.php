<?php

namespace backend\models\survey\answer;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Survey;
use common\models\User;
use common\models\survey\View;

class Search extends View
{
    public function rules()
    {
        return [
            [['survey.id', 'user.id'], 'integer'],
            [['survey.title', 'user.login', 'added'], 'string']
        ];
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['survey.title', 'survey.id', 'user.login', 'user.id']);
    }
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = View::find()->joinWith(['survey', 'user']);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'added' => SORT_DESC
                ],
            ],
        ]);

        $dataProvider->sort->attributes['survey.title'] = [
            'asc' => [Survey::tableName().'.title' => SORT_ASC],
            'desc' => [Survey::tableName().'.title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['survey.id'] = [
            'asc' => [Survey::tableName().'.id' => SORT_ASC],
            'desc' => [Survey::tableName().'.id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['user.login'] = [
            'asc' => [User::tableName().'.login' => SORT_ASC],
            'desc' => [User::tableName().'.login' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['added'] = [
            'asc' => [View::tableName().'.added' => SORT_ASC],
            'desc' => [View::tableName().'.added' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Survey::tableName().'.id'=>$this->getAttribute('survey.id'),
        ]);

        $query->andFilterWhere(['like', Survey::tableName().'.title', $this->getAttribute('survey.title')])
            ->andFilterWhere(['like', View::tableName().'.added', $this->added])
            ->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')]);

        return $dataProvider;
    }
}
