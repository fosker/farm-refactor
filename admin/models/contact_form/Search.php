<?php

namespace backend\models\contact_form;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ContactForm;
use common\models\User;

class Search extends ContactForm
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['user.name', 'date', 'message', 'subject'] , 'string']
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributes() {
        return array_merge(parent::attributes(),['user.name']);
    }

    public function search($params)
    {
        $query = ContactForm::find()->joinWith(['user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder'=>[
                    'date'=>SORT_DESC,
                ],
            ],
        ]);

        $dataProvider->sort->attributes['user.name'] = [
            'asc' => [User::tableName().'.name' => SORT_ASC],
            'desc' => [User::tableName().'.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            ContactForm::tableName().'.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', User::tableName().'.name',$this->getAttribute('user.name')])
            ->andFilterWhere(['like', 'date', $this->date]);

        return $dataProvider;
    }
}
