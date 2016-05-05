<?php

namespace backend\models\survey;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Survey;
use common\models\survey\Pharmacy;
use common\models\survey\Education;
use common\models\survey\Type;

class Search extends Survey
{

    public function rules()
    {
        return [
            [['id', 'status', 'points'], 'integer'],
            [['title'], 'string'],
            [['company_id', 'education_id', 'type_id', 'pharmacy_id'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['city_id', 'company_id', 'education_id', 'type_id', 'pharmacy_id']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Survey::find();

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
            Survey::tableName().'.id' => $this->id,
            'status' => $this->status,
            'points' => $this->points,
        ]);

        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('survey_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('survey_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);
        if($this->getAttribute('type_id'))
            $types = Type::find()->select('survey_id')->andFilterWhere(['in', 'type_id', $this->getAttribute('type_id')]);
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('survey_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Survey::tableName().'.id', $education])
            ->andFilterWhere(['in', Survey::tableName().'.id', $companies])
            ->andFilterWhere(['in', Survey::tableName().'.id', $types])
            ->andFilterWhere(['in', Survey::tableName().'.id', $pharmacies]);
        return $dataProvider;
    }
}
