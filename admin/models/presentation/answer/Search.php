<?php

namespace backend\models\presentation\answer;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Presentation;
use common\models\User;
use common\models\presentation\View;

class Search extends View
{

    public function rules()
    {
        return [
            [['presentation.id', 'user.id'], 'integer'],
            [['presentation.title', 'user.login', 'added'], 'string'],
        ];
    }

    public function attributes() {
        return array_merge(parent::attributes(), ['presentation.title', 'user.login', 'user.id',
            'presentation.id']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = View::find()->joinWith(['presentation', 'user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'added' => SORT_DESC
                ],
            ],
        ]);

        $dataProvider->sort->attributes['presentation.title'] = [
            'asc' => [Presentation::tableName().'.title' => SORT_ASC],
            'desc' => [Presentation::tableName().'.title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['presentation.id'] = [
            'asc' => [Presentation::tableName().'.id' => SORT_ASC],
            'desc' => [Presentation::tableName().'.id' => SORT_DESC],
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
            Presentation::tableName().'.id' => $this->getAttribute('presentation.id'),
        ]);

        $query->andFilterWhere(['like', User::tableName().'.id', $this->getAttribute('user.id')])
            ->andFilterWhere(['like', View::tableName().'.added', $this->added])
            ->andFilterWhere(['like', Presentation::tableName().'.title', $this->getAttribute('presentation.title')]);

        return $dataProvider;
    }
}
