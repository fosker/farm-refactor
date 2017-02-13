<?php

namespace backend\models\app;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use common\models\app\Android as AndroidModel;

/**
 * Android represents the model behind the search form about `common\models\generated\app\Android`.
 */
class Android extends AndroidModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_forced'], 'integer'],
            [['version'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AndroidModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'is_forced' => $this->is_forced,
        ]);

        $query->andFilterWhere(['like', 'version', $this->version]);

        return $dataProvider;
    }
}
