<?php

namespace backend\models\survey;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Survey;
use common\models\survey\Pharmacy;
use common\models\survey\City;
use common\models\survey\Education;
use yii\db\Query;

class Search extends Survey
{

    public function rules()
    {
        return [
            [['id', 'status', 'points'], 'integer'],
            [['title'], 'string'],
            [['city_id', 'firm_id', 'education_id'], 'safe']
        ];
    }

    public function attributes() {
        return array_merge(parent::attributes(),['city_id', 'firm_id', 'education_id']);
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

        if($this->getAttribute('city_id'))
            $cities = City::find()->select('survey_id')->where(['in', 'city_id', $this->getAttribute('city_id')]);
        if($this->getAttribute('firm_id'))
            $firms = Pharmacy::find()->select('survey_id')->where(['in', 'firm_id', $this->getAttribute('firm_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('survey_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Survey::tableName().'.id', $education])
            ->andFilterWhere(['in', Survey::tableName().'.id', $cities])
            ->andFilterWhere(['in', Survey::tableName().'.id', $firms]);

        return $dataProvider;
    }
}
