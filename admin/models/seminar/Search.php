<?php

namespace backend\models\seminar;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Seminar;
use common\models\seminar\Pharmacy;
use common\models\seminar\Education;
use common\models\seminar\Type;

class Search extends Seminar
{

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['title','email'], 'string'],
            [['company_id', 'education_id', 'type_id', 'pharmacy_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['city_id', 'company_id', 'education_id', 'type_id', 'pharmacy_id']);
    }

    public function search($params)
    {
        $query = Seminar::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'status' => SORT_ASC,
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            Seminar::tableName().'.id' => $this->id,
            'status' => $this->status,
        ]);

        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('seminar_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('seminar_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);
        if($this->getAttribute('type_id'))
            $types = Type::find()->select('seminar_id')->andFilterWhere(['in', 'type_id', $this->getAttribute('type_id')]);
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('seminar_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['in', Seminar::tableName().'.id', $education])
            ->andFilterWhere(['in', Seminar::tableName().'.id', $companies])
            ->andFilterWhere(['in', Seminar::tableName().'.id', $types])
            ->andFilterWhere(['in', Seminar::tableName().'.id', $pharmacies]);

        return $dataProvider;
    }
}
