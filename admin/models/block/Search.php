<?php

namespace backend\models\block;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Block;
use common\models\block\Comment;
use common\models\block\Mark;

class Search extends Block
{
    public function rules()
    {
        return [
            [['id', 'comment_count', 'mark_avg'], 'integer'],
            [['title', 'description'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['comment_count', 'mark_avg']);
    }

    public function search($params)
    {
        $query = Block::find();

        $commentQuery = Comment::find()
            ->select('block_id, count(id) as count')
            ->groupBy('block_id');
        $query->leftJoin(['commentCount' => $commentQuery], 'commentCount.block_id = id');

        $markQuery = Mark::find()
            ->select('block_id, round(avg(mark)) as mark')
            ->groupBy('block_id');
        $query->leftJoin(['avgMark' => $markQuery], 'avgMark.block_id = id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $dataProvider->sort->attributes['comment_count'] = [
            'asc' => ['commentCount.count' => SORT_ASC],
            'desc' => ['commentCount.count' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['mark_avg'] = [
            'asc' => ['avgMark.mark' => SORT_ASC],
            'desc' => ['avgMark.mark' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'commentCount.count' => $this->getAttribute('comment_count'),
            'avgMark.mark' => $this->getAttribute('mark_avg')
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
