<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 06.11.2018
 * Time: 17:14
 */

namespace common\modules\auth\controllers;

use common\models\UserExt;
use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;


class AuthController
{
    public function createUser($user_data, $auth_code = null, $soc_id = null){

//        echo 'socAuthComplete<br/>';
//        print_r( $user_data );

        $data = $user_data;
        $data['auth_source'] = $soc_id;
        $user = User::createUser( $data, User::SCENARIO_SIGNUP_SOCIAL );

        if(isset($user['error'])) return null;
//        $user = new User();
//        $this->updateUserData($user, $user_data, $auth_code, $soc_id);
//        $this->updateUserExt($user, $user_data);
//        $this->updateEmail($user->id,$user_data);
        return $user;
    }

    public function updateUserData($user, $user_data, $auth_code = null, $soc_id = null)
    {
//        $user->username = $user_data['first_name'].' '.$user_data['last_name'];
//        if(!$user->password_hash) {
//            $user->setPassword(Yii::$app->security->generateRandomString());
//        }
//        if ($auth_code != null) {
//            $user->auth_key = $auth_code;
//        } else {
//            $user->auth_key = User::generateAuthKey();
//        }
//        $user->updated_at = date('U');
//        if(!$user->created_at) {
//            $user->created_at = date('U');
//        }
//        $user->auth_source = $soc_id;
//        if(!$user->save()){
//            return($user->errors);
//        }
        User::updateData( $user, $user_data );
    }

    public function updateEmail($id, $user_data){
        $user_data_email = ArrayHelper::getValue( $user_data, 'email' );
        if(!$user_data_email){
            return false;
        }
        $update_email_result = UserExt::updateEmail( $id, $user_data_email );
        if( isset($update_email_result['error'])) return false;
        return true;
        /*
        $email= Email::find()->where(['user_id' => $id])->one();
        if(!$email){
            $email = new Email();
            $email->user_id = $id;
            $email->value = $user_data_email;
            $email->confirm_token = Yii::$app->security->generateRandomString();
        }
        if($email->is_verified != 1 && $email->value == $user_data_email ){
            $email->is_verified = 1;
            $email->verified_at = time();
        }
        if(!$email->save()){
            var_dump($email->errors);
        };
        return true;
        */
    }


    public function updateUserExt($user, $user_data){
        $user_ext = UserExt::find()->where(['id' => $user->id])->one();
        if(!$user_ext){
            $user_ext = new UserExt;
            $user_ext->user_id = $user->id;
        }
        if (!$user_ext->first_name) {
            $user_ext->first_name = ArrayHelper::getValue( $user_data, 'first_name' );
        }
        if (!$user_ext->middle_name) {
            $user_ext->middle_name = ArrayHelper::getValue( $user_data, 'middle_name' );
        }
        if (!$user_ext->last_name) {
            $user_ext->last_name = ArrayHelper::getValue( $user_data, 'last_name' );
        }
        if (!$user_ext->phone) {
            $user_ext->phone = ArrayHelper::getValue( $user_data, 'phone' );
        }
        if(!$user_ext->save()){
            return($user_ext->errors);
        }
    }

    public function loginUser($user, $remember_me = null ){
        $app_params = Yii::$app->params;
        $remember_me_duration = ArrayHelper::getValue( $app_params, 'api.loginRememberMeDuration' );
        if(!Yii::$app->user->identity) {
            Yii::$app->user->login($user, $remember_me ? $remember_me_duration : 60 * 30);
        }
        return Yii::$app->user->identity;
    }

    public function returnSuccess($answer = null ){
        if( $answer != null ) {
            return "<script>window.opener.$(window.opener).trigger('oauth:complete', ".json_encode(array_merge(['success' => 'true'], ["access_token" => $answer])).");window.close();</script>";

//            return json_encode(array_merge(['success' => 'true'], ["access_token" => $answer]));
        }
        return ['success' => true ];
    }

    public function setAccessCookie($access_token){
        Yii::$app->response->cookies->add(new Cookie([
            'name' => 'access_token',
            'value' => $access_token,
            'expire' => time() + 30 * 60,
        ]));
    }
}