<?php

namespace backend\models\form;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Form;


class Search extends Form
{

    public function rules()
    {
        return [
            [['title'], 'string'],
            [['id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [

        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Form::find();

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
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', Form::tableName().'.title', $this->title]);

        return $dataProvider;
    }
}