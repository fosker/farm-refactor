<?php

namespace backend\models\banner;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Banner;
use common\models\banner\City;
use common\models\banner\Pharmacy;
use common\models\banner\Education;


class Search extends Banner
{

    public function rules()
    {
        return [
            [['id', 'position', 'status'], 'integer'],
            [['title', 'link'], 'string'],
            [['city_id', 'firm_id', 'education_id'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(),['city_id', 'firm_id', 'education_id']);
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {

        $query = Banner::find();

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
            Banner::tableName().'.id' => $this->id,
            'position' => $this->position,
            'status' => $this->status,
        ]);

        if($this->getAttribute('city_id'))
            $cities = City::find()->select('banner_id')->where(['in', 'city_id', $this->getAttribute('city_id')]);
        if($this->getAttribute('firm_id'))
            $firms = Pharmacy::find()->select('banner_id')->where(['in', 'firm_id', $this->getAttribute('firm_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('banner_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['in', Banner::tableName().'.id', $education])
            ->andFilterWhere(['in', Banner::tableName().'.id', $cities])
            ->andFilterWhere(['in', Banner::tableName().'.id', $firms]);

        return $dataProvider;
    }
}
