<?php

namespace backend\models\video;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Video;


class Search extends Video
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['title', 'tags', 'link'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = Search::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Video::tableName().'.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'tags', $this->tags]);

        return $dataProvider;
    }
}
