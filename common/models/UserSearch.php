<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form of `admin\models\User`.
 */
class UserSearch extends User
{
    public $first_name;
    public $last_name;
    public $middle_name;
    public $email_value;
    public $email_verified;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id',  'status'], 'integer'],
            [['created_at', 'updated_at', 'last_login_at'], 'safe'],
            [['username', 'password_hash', 'auth_source', 'auth_key', 'password_reset_token' ], 'safe'],
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
        $query = User::find()->joinWith('userExt');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

//        $dataProvider->setSort([
//            'attributes' => [
//                'id',
//                'username',
//                'first_name' => [
//                    'asc' => ['user_ext.first_name' => SORT_ASC],
//                    'desc' => ['user_ext.first_name' => SORT_DESC],
//                    'label' => 'First Name',
//                    'default' => SORT_ASC
//                ],
//                'middle_name' => [
//                    'asc' => ['user_ext.middle_name' => SORT_ASC],
//                    'desc' => ['user_ext.middle_name' => SORT_DESC],
//                    'label' => 'Middle Name',
//                    'default' => SORT_ASC
//                ],
//                'last_name' => [
//                    'asc' => ['user_ext.last_name' => SORT_ASC],
//                    'desc' => ['user_ext.last_name' => SORT_DESC],
//                    'label' => 'Last Name',
//                    'default' => SORT_ASC
//                ],
//                'email_value' => [
//                    'asc' => ['email.value' => SORT_ASC],
//                    'desc' => ['email.value' => SORT_DESC],
//                    'label' => 'E-mail',
//                    'default' => SORT_ASC
//                ],
//                'email_verified' => [
//                    'asc' => ['email.verified_at' => SORT_ASC],
//                    'desc' => ['email.verified_at' => SORT_DESC],
//                    'label' => 'Email Verified',
//                    'default' => SORT_ASC
//                ],
//                'auth_source',
//                'created_at',
//                'updated_at',
//                'last_login_at',
//                'email',
//                'status',
//            ]
//        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id' => $this->id,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'auth_source', $this->auth_source])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token]);

//        $query->andFilterWhere(['like', 'user_ext.first_name', $this->first_name])
//            ->andFilterWhere(['like', 'user_ext.middle_name', $this->middle_name])
//            ->andFilterWhere(['like', 'user_ext.last_name', $this->last_name])
//            ->andFilterWhere(['like', 'email.value', $this->email_value])
//            ->andFilterWhere(['like', 'email.is_verified', $this->email_verified]);

        /*
        $divider = ' - ';
        $div_pos_last_login_at = strpos($this->last_login_at,$divider);
        if(!$div_pos_last_login_at){
            $query->andFilterWhere([
                'last_login_at' => $this->last_login_at,
            ]);
        } else {
            $last_login_from = substr($this->last_login_at, 0, $div_pos_last_login_at);
            $last_login_from = strtotime($last_login_from);
            $last_login_to = substr($this->last_login_at, $div_pos_last_login_at + 3);
            $last_login_to = strtotime($last_login_to);

            $query->andFilterWhere([">=", "last_login_at", $last_login_from]);
            $query->andFilterWhere(["<=", "last_login_at", $last_login_to]);
        }

        $div_pos_created_at = strpos($this->created_at,$divider);
        if(!$div_pos_created_at){
            $query->andFilterWhere([
                'created_at' => $this->created_at,
            ]);
        } else {
            $created_from = substr($this->created_at, 0, $div_pos_created_at);
            $created_from = strtotime($created_from);
            $created_to = substr($this->created_at, $div_pos_created_at + 3);
            $created_to = strtotime($created_to);

            $query->andFilterWhere([">=", "created_at", $created_from]);
            $query->andFilterWhere(["<=", "created_at", $created_to]);
        }
        */

        return $dataProvider;
    }
}
