<?php

namespace backend\models\theme;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Theme;

class Search extends Theme
{

    public function rules()
    {
        return [
            [['title', 'email'], 'string'],
            [['id', 'company_id', 'form_id'], 'integer'],
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
        $query = Theme::find();

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
            'company_id' => $this->company_id,
            'form_id' => $this->form_id
        ]);

        $query->andFilterWhere(['like', Theme::tableName().'.title', $this->title]);

        return $dataProvider;
    }
}