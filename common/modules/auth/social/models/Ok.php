<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 02.11.2018
 * Time: 17:56
 */

namespace common\modules\auth\social\models;

use common\modules\auth\Keys;
use common\modules\auth\social\SocInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

class Ok extends SocBase implements SocInterface
{
    public $soc_name = 'ok';

    public $ver = 5.85;

    public $client_id = Keys::OK_CLIENT;

    public $client_secret = Keys::OK_SECRET;

    public $client_public = Keys::OK_PUBLIC;

    public $fields = '';

    public $scope = 'VALUABLE_ACCESS,GET_EMAIL,LONG_ACCESS_TOKEN,EMAIL';

    public function getLoginUrl(){
        $redirect_url = $this->getRedirectUri();
        $url = 'https://connect.ok.ru/oauth/authorize?client_id='.$this->client_id.'&scope='.$this->scope.'&response_type=code&redirect_uri='.$redirect_url;
        return $url;
    }

    public function getAccessTokenUrl(){
        $auth_url = 'https://api.ok.ru/oauth/token.do';
        return $auth_url;
    }

    public function getUserUrl(){
        $user_url = 'https://api.ok.ru/fb.do';
        return $user_url;
    }

    public function getAuthArgs(){
        $auth_url = $this->getAccessTokenUrl();
        $client = new Client();
        $get_params = Yii::$app->request->get();
        $code = ArrayHelper::getValue($get_params , 'code');
        $redirect_url = $this->getRedirectUri();
        $params = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $redirect_url,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $method = 'POST';
        $args = [$auth_url, $client, $params,$method];
        return $args;
    }

    public function getUserArgs($response){
        $user_url = $this->getUserUrl();
        $access_token = $this->getAccessTokenFromResponse($response);
        $client = new Client();
        $params = [
            'application_key' => $this->client_public,
            'format' => 'json',
            'method' => 'users.getCurrentUser',
        ];
        $secret = Keys::OK_SECRET;
        $sig = $this->getSig($params, $access_token, $secret);
        $params['sig'] = $sig;
        $params['access_token'] = $access_token;
        $args = [$user_url,$client, $params];
        return $args;
    }

    public function getUserIdFromResponse($response){
        $id = ArrayHelper::getValue( $response->data,'user_id' );
        return $id;
    }

    public function getUserDataFromResponse($response){
        $data = $response->data;
        return $data;
    }

    public function getAccessTokenFromResponse($response){
        $token = ArrayHelper::getValue( $response->data,'access_token' );
        return $token;
    }

    private function getSig($vars, $access_token,$secret){
        ksort($vars);
        $params = '';
        foreach ($vars as $key => $value) {
            $params .= "$key=$value";
        }
        return md5($params . md5($access_token.$secret));
    }
}