<?php

namespace admin\modules\rbac\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\modules\rbac\models\RoleAssign;

/**
 * RoleAssignSearch represents the model behind the search form of `admin\models\RoleAssign`.
 */
class RoleAssignSearch extends RoleAssign
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'role_id', 'user_id'], 'integer'],
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
        $query = RoleAssign::find();

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
            'role_id' => $this->role_id,
            'user_id' => $this->user_id,
        ]);

        return $dataProvider;
    }
}
