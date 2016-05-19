<?php

namespace factory\models\search\news;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\News;
use common\models\news\View;


class Search extends News
{

    public function rules()
    {
        return [
            [['id', 'views'], 'integer'],
            [['title', 'text', 'date'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = News::find()
            ->where(['factory_id' => Yii::$app->user->identity->factory_id])
            ->joinWith('types')
            ->andWhere(['type_id' => 2]);

        $viewsQuery = View::find()
            ->select('count(DISTINCT user_id) as count, news_id')
            ->groupBy('news_id');
        $query->leftJoin(['viewsCount' => $viewsQuery], 'viewsCount.news_id = id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date' => SORT_DESC
                ],
            ],
        ]);

        $dataProvider->sort->attributes['views'] = [
            'asc' => ['viewsCount.count' => SORT_ASC],
            'desc' => ['viewsCount.count' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if($this->views) {
            $query->andFilterWhere(['viewsCount.count' => $this->views]);
            $query->orWhere(['(views_added + viewsCount.count)' => $this->views]);
            $query->orWhere(['views_added' => $this->views]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'date', $this->date]);

        return $dataProvider;
    }
}
