<?php

namespace backend\models\banner;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Banner;
use common\models\banner\Pharmacy;
use common\models\banner\Education;
use common\models\banner\Type;


class Search extends Banner
{

    public function rules()
    {
        return [
            [['id', 'position', 'status'], 'integer'],
            [['title', 'link'], 'string'],
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

        if($this->getAttribute('company_id'))
            $companies = Pharmacy::find()->select('banner_id')->where(['in', 'company_id', $this->getAttribute('company_id')])
                ->joinWith('pharmacy');
        if($this->getAttribute('education_id'))
            $education = Education::find()->select('banner_id')->andFilterWhere(['in', 'education_id', $this->getAttribute('education_id')]);
        if($this->getAttribute('type_id'))
            $types = Type::find()->select('banner_id')->andFilterWhere(['in', 'type_id', $this->getAttribute('type_id')]);
        if($this->getAttribute('pharmacy_id'))
            $pharmacies = Pharmacy::find()->select('banner_id')->andFilterWhere(['in', 'pharmacy_id', $this->getAttribute('pharmacy_id')]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['in', Banner::tableName().'.id', $education])
            ->andFilterWhere(['in', Banner::tableName().'.id', $companies])
            ->andFilterWhere(['in', Banner::tableName().'.id', $types])
            ->andFilterWhere(['in', Banner::tableName().'.id', $pharmacies]);

        return $dataProvider;
    }
}
