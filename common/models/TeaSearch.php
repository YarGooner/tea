<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Tea;

/**
 * TeaSearch represents the model behind the search form of `common\models\Tea`.
 */
class TeaSearch extends Tea
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'collection_id', 'buy_button_flag', 'output_priority'], 'integer'],
            [['title', 'subtitle', 'description', 'image_fon', 'image_pack', 'weight', 'temperature_brewing', 'time_brewing', 'url'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = Tea::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'collection_id' => $this->collection_id,
            'buy_button_flag' => $this->buy_button_flag,
            'output_priority' => $this->output_priority,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'subtitle', $this->subtitle])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'image_fon', $this->image_fon])
            ->andFilterWhere(['like', 'image_pack', $this->image_pack])
            ->andFilterWhere(['like', 'weight', $this->weight])
            ->andFilterWhere(['like', 'temperature_brewing', $this->temperature_brewing])
            ->andFilterWhere(['like', 'time_brewing', $this->time_brewing])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
