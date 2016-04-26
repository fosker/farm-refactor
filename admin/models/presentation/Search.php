<?php

namespace backend\models\presentation;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Presentation;
use common\models\presentation\City;
use common\models\presentation\Pharmacy;
use common\models\presentation\Education;

class Search extends Presentation
{

    public function rules()
    {
        return [
            [['id', 'status', 'home', 'home_priority'], 'integer'],
            [['title'], 'string'],
            [['city_id', 'firm_id', 'education_id'], 'safe']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['city_id', 'firm_id', 'education_id']);
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

        if($this->getAttribute('city_id'))
            $cities = City::find()->select('presentation_id')->where(['in', 'city_id', $this->getAttribute('city_id')]);
        if($this->getAttribute('firm_id'))
            $firms = Pharmacy::find()->select('presentation_id')->where(['in', 'firm_id', $this->getAttribute('firm_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('presentation_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['in', Presentation::tableName().'.id', $education])
            ->andFilterWhere(['in', Presentation::tableName().'.id', $cities])
            ->andFilterWhere(['in', Presentation::tableName().'.id', $firms]);

        return $dataProvider;
    }
}
