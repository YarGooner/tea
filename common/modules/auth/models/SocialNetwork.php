<?php

namespace common\modules\auth\models;

use common\models\User;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "social_network".
 *
 * @property int $id
 * @property int $user_id
 * @property string $social_network_id
 * @property string $user_auth_id
 * @property string $access_token
 * @property string $last_auth_date
 *
 * @property User $user
 */
class SocialNetwork extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'social_network';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'social_network_id', 'user_auth_id'], 'required'],
            [['user_id'], 'integer'],
            [['last_auth_date'], 'safe'],
            [['social_network_id', 'user_auth_id'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'social_network_id' => 'Social Network ID',
            'user_auth_id' => 'User Auth ID',
            'access_token' => 'Access Token',
            'last_auth_date' => 'Last Auth Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    static public function getUserBySocId($soc_id, $user_soc_id ){
        $user_id = SocialNetwork::findOne(['social_network_id' => $soc_id, 'user_auth_id' => $user_soc_id ]);
        if( $user_id == null ) return null;
        $user = User::getById( $user_id->user_id );
        return $user;
    }

//    static public function getUserByAccessToken( $auth_code ){
//        $user_id = SocialNetwork::findOne(['access_token' => $auth_code ]);
//        if( $user_id == null ) return null;
//        $user = User::findOne(['id' => $user_id->user_id] );
//        return $user;
//    }
}
