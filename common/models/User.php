<?php

namespace common\models;

use common\components\Emailer;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 *
 * @property string $username
 *
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_source
 * @property string $auth_key
 *
 * @property int $last_login_at
 * @property int $created_at
 * @property int $updated_at
 *
 * @property int $status
 *
 * @property UserExt $userExt
 */
class User extends ActiveRecord implements IdentityInterface
{

    const SCENARIO_SIGNUP = 1;
    const SCENARIO_SIGNUP_SOCIAL = 2;
    const SCENARIO_LOGIN = 3;
    const SCENARIO_UPDATE = 4;

    public $email;
    public $password;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['created_at', 'updated_at'], 'required'],

            [['email', 'password'], 'required', 'on' => self::SCENARIO_SIGNUP],
            [['email', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],

            [['email'], 'email'],
            [['password'], 'string', 'min' => 6, 'max' => 20],

            [['last_login_at', 'created_at', 'updated_at', 'status'], 'integer'],
            [['username', 'auth_source', 'auth_key', 'password_reset_token'], 'string', 'max' => 255],
            [['password_hash'], 'string', 'max' => 60],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_SIGNUP => ['email', 'password'],
            self::SCENARIO_SIGNUP_SOCIAL => ['email', 'password', 'username'],
            self::SCENARIO_LOGIN => ['email', 'password'],
            self::SCENARIO_UPDATE => ['email', 'password', 'username'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_source' => Yii::t('app', 'Auth Source'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'last_login_at' => Yii::t('app', 'Last Login At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserExt()
    {
        return $this->hasOne(UserExt::className(), ['user_id' => 'id']);
    }

    // ==========================================
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    //
    public static function findIdentity( $id, $with_ext = false )
    {
//        if( $with_ext ){
        return self::find()->where(['id' => $id])->with('userExt')->one();
//        }
//        return self::findOne(['id' => $id]);

    }

    public static function findUserByAccessToken($token)
    {
        return self::findOne(['auth_key' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if($token) {
            $user = self::find()->where(['auth_key' => $token])->one();
            return $user;
        }
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    //
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    // -----------------------

    //
    static public function getUserByEmail($email)
    {
        if( !$email ) return null;
        $userExt = UserExt::getByEmail($email);
        if( !$userExt ) return null;
        $user = self::getById( $userExt->user_id );
        return $user;
    }

    //
    static public function getUserByUsername( $username )
    {
        if( !$username ) return;
        $user = User::find()->where(['username' => $username])->one();
        return $user;
    }

    //
    static public function getUserByUnconfirmedEmail($email)
    {
        if( !$email ) return null;
        $emailUser = Email::find()->where(['value' => $email, 'is_verified' => '0'])->one();
        if( !$emailUser ) return null;
        $user_id = $emailUser->user_id;
        $user = User::find()->where(['id' => $user_id])->one();
        return $user;
    }

    //
    static public function getById( $user_id = null ){
        if( $user_id === true ) $user_id = Yii::$app->user->id;
        return self::find()->where(['id'=>$user_id])->with('userExt')->one();
    }


    // Создание нового пользователя
    static public function createUser(  $params = null, $scenario = null ){

        if( !$params ) return ['error' => ['createUser' => Yii::t('app', 'Data required' )]];

        $user = new self();
        $user->scenario = $scenario ? $scenario : self::SCENARIO_SIGNUP;

//        $user->username = explode( '@', $email )[0];
//        return ['error' => ['createUser' => $params ]]; // !!!
        $user->load( $params, '' );
        if( isset($params['password']) ) $user->setPassword($params['password']);
        $user->created_at = $user->updated_at = time();
        $user->auth_key = \Yii::$app->security->generateRandomString();
        self::checkEmailIsChanged( $user, $params );
        $user->last_login_at = time();

//        return $this->returnError($user); // !!!

        if($user->save()){

            $user->refresh();

            $userExt = new UserExt();
            $params['user_id'] = $user->id;
            $userExt->load( $params, '' );

            if( !$userExt->save() ){
                $user->delete();
                return ['error' => ['createUser' => $userExt->errors ]];
            }

            // Send a Letter
            self::sendConfirmationEmail( $userExt->unconfirmed_email, 'Letter Subject: Signup', ArrayHelper::getValue( Yii::$app->params, 'email_on.signup'), $user );
            /*
            $template = ArrayHelper::getValue( Yii::$app->params, 'email_on.signup');
            if( $template ) {
                Emailer::sendMail( $userExt->unconfirmed_email, Yii::t('app', 'Letter Subject: Signup'), $template, $user );
            }
            */

            return $user;

        } else {
            return ['error' => ['createUser' => $user->errors ]];
        }

    }


    //
    public static function updateData( $user, $params ){
        $user->scenario = self::SCENARIO_UPDATE;

//        $user->username = explode( '@', $email )[0];
//        return ['error' => ['createUser' => $params ]]; // !!!
        $user->load( $params, '' );
        self::checkEmailIsChanged( $user, $params );

        if($user->save()){

            $user->refresh();

            $userExt = $user->userExt;
            $userExt->load( $params, '' );

            if( !$userExt->save() ){
                $user->delete();
                return ['error' => ['updateData' => $userExt->errors ]];
            }

            return $user;

        } else {
            return ['error' => ['updateData' => $user->errors ]];
        }
    }


    //
    public static function checkEmailIsChanged( &$user, &$params )
    {
        if( isset($params['email']) ) {
            $user->auth_source = 'e-mail';
            $params['unconfirmed_email'] = $params['email'];
            unset( $params['email']);
        }
    }


    //
    static public function sendConfirmationEmail( $email = null, $subject = null, $template_name = null, $user = null ){
        if( !$email || !$subject || !$user || !$template_name ) return;
        $user->userExt->email_confirm_token = \Yii::$app->security->generateRandomString();;
        if( $user->userExt->save() ) {
            if( Emailer::sendMail( $email, Yii::t('app', $subject), $template_name, $user) ){
                return true;
            }
        }
        return false;

    }

}
