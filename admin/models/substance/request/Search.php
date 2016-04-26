<?php

namespace backend\models\substance\request;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\substance\Request;
use common\models\User;
use common\models\Substance;

class Search extends Request
{

    public function rules()
    {
        return [
            [['id', 'user.position_id'], 'integer'],
            [['user.name','substance.cyrillic', 'date_request'], 'string'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes() {
        return array_merge(parent::attributes(),['user.position_id','user.name','substance.cyrillic']);
    }

    public function search($params)
    {
        $query = Request::find()->joinWith(['user','substance']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date_request'=>SORT_DESC,
                ],
            ],
        ]);
        $dataProvider->sort->attributes['user.name'] = [
            'asc' => [User::tableName().'.name' => SORT_ASC],
            'desc' => [User::tableName().'.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['user.position_id'] = [
            'asc' => [User::tableName().'.position_id' => SORT_ASC],
            'desc' => [User::tableName().'.position_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['substance.cyrillic'] = [
            'asc' => [Substance::tableName().'.name' => SORT_ASC],
            'desc' => [Substance::tableName().'.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Request::tableName().'.id' => $this->id,
            User::tableName().'.position_id' => $this->getAttribute('user.position_id'),
        ]);
        $query->andFilterWhere(['or',['like', Substance::tableName().'.cyrillic',$this->getAttribute('substance.cyrillic')],
            ['like',Substance::tableName().'.name',$this->getAttribute('substance.cyrillic')]])
            ->andFilterWhere(['like', Request::tableName().'.date_request', $this->date_request])
            ->andFilterWhere(['like', User::tableName().'.name',$this->getAttribute('user.name')]);

        return $dataProvider;
    }
}
