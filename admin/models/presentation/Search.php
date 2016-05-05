<?php

namespace backend\models\presentation;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Presentation;
use common\models\presentation\Pharmacy;
use common\models\presentation\Education;
use common\models\presentation\Type;

class Search extends Presentation
{

    public function rules()
    {
        return [
            [['id', 'status', 'home', 'home_priority'], 'integer'],
            [['title'], 'string'],
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
        $query = Presentation::find();

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
            Presentation::tableName().'.id' => $this->id,
            'status' => $this->status,
            'home' => $this->home,
            'home_priority' => $this->home_priority
        ]);

        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('presentation_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('presentation_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);
        if($this->getAttribute('type_id'))
            $types = Type::find()->select('presentation_id')->andFilterWhere(['in', 'type_id', $this->getAttribute('type_id')]);
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('presentation_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Presentation::tableName().'.id', $education])
            ->andFilterWhere(['in', Presentation::tableName().'.id', $companies])
            ->andFilterWhere(['in', Presentation::tableName().'.id', $types])
            ->andFilterWhere(['in', Presentation::tableName().'.id', $pharmacies]);

        return $dataProvider;
    }
}
