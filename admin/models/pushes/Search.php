<?php

namespace backend\models\pushes;


use Yii;
use yii\data\ActiveDataProvider;
use common\models\Push;
use common\models\pharmbonus\Users;


class Search extends Push
{

    public function rules()
    {
        return [
            [['id', 'views', 'device_count'], 'integer'],
            [['link', 'message', 'date_send'], 'string'],
        ];
    }


    public function search($params)
    {
        $query = Push::find()
            ->joinWith('pharmPushes')
            ->andWhere(['in', Push::tableName().'.id', Users::find()->select('push_id')])
            ->groupBy(Push::tableName().'.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_send' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'device_count' => $this->device_count,
            'views' => $this->views
        ]);

        $query->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'date_send', $this->date_send])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}