<?php

namespace backend\models\news;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\News;
use common\models\news\View;
use common\models\news\Pharmacy;
use common\models\news\Education;
use common\models\news\Type;


class Search extends News
{

    public function rules()
    {
        return [
            [['id', 'views', 'priority'], 'integer'],
            [['title', 'text', 'date'], 'string'],
            [['company_id', 'education_id', 'type_id', 'pharmacy_id'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['city_id', 'company_id', 'education_id', 'type_id', 'pharmacy_id']);
    }


    public function search($params)
    {
        $query = News::find();

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
            'priority' => $this->priority
        ]);

        if($this->views) {
            $query->andFilterWhere(['viewsCount.count' => $this->views]);
            $query->orWhere(['(views_added + viewsCount.count)' => $this->views]);
            $query->orWhere(['views_added' => $this->views]);
        }


        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('news_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('news_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);
        if($this->getAttribute('type_id'))
            $types = Type::find()->select('news_id')->andFilterWhere(['in', 'type_id', $this->getAttribute('type_id')]);
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('news_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['in', News::tableName().'.id', $education])
            ->andFilterWhere(['in', News::tableName().'.id', $companies])
            ->andFilterWhere(['in', News::tableName().'.id', $types])
            ->andFilterWhere(['in', News::tableName().'.id', $pharmacies]);

        return $dataProvider;
    }
}
