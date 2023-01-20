<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 02.11.2018
 * Time: 10:21
 */

namespace common\modules\auth\controllers;

use common\models\UserExt;
use common\models\User;
use common\modules\auth\social\SocInterface;
use Yii;
use common\modules\auth\models\SocialNetwork;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\Cookie;


class SocAuthController extends AuthController
{
    public function auth(SocInterface $soc, $type){
        //сначала авторизуемся в соц.сети
        $authArgs = $soc->getAuthArgs();
        $auth_response = $this->sendRequest(...$authArgs);

        //парсим данные пользователя
        $userArgs = $soc->getUserArgs($auth_response);
        $user_response = $this->sendRequest(...$userArgs);
        $user_data = $soc->getUserDataFromResponse($user_response);

        //получаем почту из vk
        if(!ArrayHelper::getValue( $user_data, 'email') ) {
            $user_data['email'] = ArrayHelper::getValue( $auth_response->data, 'email' );
        }

        $access_token = $soc->getAccessTokenFromResponse($auth_response);
        $user_data['auth_code'] = $access_token;

        $user = null;

        if($type !== 'signup') {
            $user_id = Yii::$app->user->id;
            $user = User::findIdentity($user_id);
            if ($user == null) {
                $cookie = Yii::$app->request->cookies->get('access_token');
                $access_token = $cookie ? $cookie->value : null;
                Yii::$app->response->cookies->remove('access_token');
                if( $access_token ) {
                    $user = User::find()->where(['auth_key' => $access_token])->one();
                }
            }
        }

        return $this->socAuthComplete( $soc->soc_name, $user_data, $user, $type);

    }

    private function socAuthComplete($soc_id, $user_data, $user, $type)
    {

        $success = false;

        if($soc_id == 'ok'){
            $soc_user_id = strval($user_data['uid']);
        } else {
            $soc_user_id = strval($user_data['id']);
        }

        $user_data['soc_user_id'] = $soc_user_id;
        $auth_code = $user_data['auth_code'];

        if($type == 'login' && !SocialNetwork::find()->where(['social_network_id' => $soc_id, 'user_auth_id' => $soc_user_id])->one()){
            $user = $this->createUser($user_data, $auth_code, $soc_id);
//            echo('socAuthComplete: LOGIN: ');
//            print_r( $user );
//            die();
        }

        if($user == null) {
           $user = SocialNetwork::getUserBySocId($soc_id, $soc_user_id);

            // Brand new User
            if ($user == null) {

                $user = $this->createUser($user_data, $auth_code, $soc_id, $soc_user_id);
//                print_r( $user );
//                die();
                /*
                $used_email_check = User::getUserByEmail($user_data['email']);
                $used_unconfirmed_email_check = User::getUserByUnconfirmedEmail($user_data['email']);;
                if($used_email_check || $used_unconfirmed_email_check){
                    unset($user_data['email']);
                }
                $user = $this->createUser($user_data, $auth_code, $soc_id, $soc_user_id);

                $id = $user->id;
                $auth = $this->getAuth($id,$soc_id,$soc_user_id,$auth_code);
                */
            } else {
//                echo('socAuthComplete: USER EXSISTS ');

                $this->updateUserData($user, $user_data, $auth_code, $soc_id);
                $auth = SocialNetwork::getUserBySocId($soc_id, $soc_user_id);
            }
            $success = true;
            $user->auth_key = $auth_code;
        } else {
            // User exists
            $success = true;
            $id = $user->id;
            $auth = $this->getAuth($id,$soc_id,$soc_user_id,$auth_code);
        }

        return $this->createUserSession($user, $success, $user_data, $auth, $soc_id);

    }

    private function createUserSession($user, $success, $user_data, $auth, $soc_id){

        if( $user && $success ){
            $this->loginUser($user);
            $response = $user->auth_key;
        }else{
            $response = [
                'success' => $success,
                'user' => $user,
                'user_data' => $user_data,
                'user_errors' => $user != null ? $user->errors : null,
                'auth' => $auth,
                'auth_errors' => $auth != null ? $auth->errors : null,
            ];
            return $response;
        }

        $user->auth_source = $soc_id;
        $user->save( false ); // TODO: Валидацию данных пользователя настроить
        \Yii::$app->response->format = Response::FORMAT_HTML;
        return $response;
    }

    private function getAuth($id,$soc_id,$soc_user_id,$auth_code){
        $user = User::find()->where(['id' => $id])->one();
        $soc_net = SocialNetwork::find()->where(['user_id' => $id, 'social_network_id' => $soc_id, 'user_auth_id' => $soc_user_id])->one();
        if(!$soc_net){
            $soc_net = new SocialNetwork();
            $soc_net->user_id = $user->id;
            $soc_net->social_network_id = $soc_id;
            $soc_net->user_auth_id = $soc_user_id;
            $soc_net->access_token = $auth_code;
            $soc_net->last_auth_date = time();
            if(!$soc_net->save()){
                return $soc_net->errors;
            }
        }
        return $user;
    }

//    private function getSocResponse($user, $success, $user_data, $auth, $soc_name){
//
//        if( $user && $success ){
////            $response = $this->loginUser( $user );
////            $response = array_merge( [ 'oauth_client' => $soc_name ], $response );
////            var_dump($response);
//        }else{
//            $response = [
//                'success' => $success,
//                'user' => $user,
//                'user_data' => $user_data,
//                'user_errors' => $user != null ? $user->errors : null,
//                'auth' => $auth,
//                'auth_errors' => $auth != null ? $auth->errors : null,
//            ];
//            return $response;
//        }
//        if(!Yii::$app->request->cookies->getValue('access_token')) {
//            $social = SocialNetwork::find()->where(['user_id' => $user->id, 'social_network_id' => $soc_name])->one();
//            $social->last_auth_date = date('U');
//            $social->save();
//            $this->setAccessCookie($user->auth_key);
//        }
//        $user->auth_source = $soc_name;
//        $user->save();
//        \Yii::$app->response->format = Response::FORMAT_HTML;
//        return $this->returnSuccess($user->auth_key);
//    }

    private function sendRequest($url, $client, $params, $method = 'GET'){
        $response = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setData($params)
            ->send();
//        var_dump($response);
        return $response;
    }


}
