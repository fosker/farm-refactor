<?php

namespace company\models\search\news;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\News;
use common\models\news\View;
use common\models\news\Pharmacy;
use common\models\news\Education;
use common\models\company\Pharmacy as Company_Pharmacy;


class Search extends News
{

    public function rules()
    {
        return [
            [['id', 'views'], 'integer'],
            [['title', 'text', 'date'], 'string'],
            [['education_id'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['education_id']);
    }

    public function search($params)
    {
        $query = News::find()
            ->joinWith('pharmacies')
            ->joinWith('types')
            ->join('LEFT JOIN', Company_Pharmacy::tableName(),
                Company_Pharmacy::tableName().'.id = '.Pharmacy::tableName().'.pharmacy_id')
            ->where(['company_id' => Yii::$app->user->identity->company_id])
            ->andWhere(['type_id' => 1])
            ->groupBy(News::tableName().'.id');

        $viewsQuery = View::find()
            ->select('count(DISTINCT user_id) as count, news_id')
            ->groupBy('news_id');
        $query->leftJoin(['viewsCount' => $viewsQuery], 'viewsCount.news_id = news.id');

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


        if($this->getAttribute('education_id'))
            $education = Education::find()->select('news_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['in', News::tableName().'.id', $education]);

        return $dataProvider;
    }
}
