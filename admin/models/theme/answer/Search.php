<?php

namespace backend\models\theme\answer;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\theme\Reply;
use common\models\User;
use common\models\Theme;

class Search extends Reply
{
    public function rules()
    {
        return [
            [['id', 'theme_id', 'user_id'], 'integer'],
            ['date_added', 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Reply::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_add'=>SORT_DESC,
                ],
            ],
        ]);


        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'theme_id' => $this->theme_id,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'date_added', $this->date_added]);

        return $dataProvider;
    }
}
