<?php

namespace admin\modules\rbac\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use admin\modules\rbac\models\RoleModelPermission;

/**
 * RoleModelPermissionSearch represents the model behind the search form of `admin\modules\rbac\models\RoleModelPermission`.
 */
class RoleModelPermissionSearch extends RoleModelPermission
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'role_id', 'field_id', 'type'], 'integer'],
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
        $query = RoleModelPermission::find();

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
            'field_id' => $this->field_id,
            'type' => $this->type,
        ]);

        return $dataProvider;
    }
}
