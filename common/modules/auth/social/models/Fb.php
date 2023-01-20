<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 02.11.2018
 * Time: 12:28
 */

namespace common\modules\auth\social\models;

use common\modules\auth\Keys;
use common\modules\auth\social\SocInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;


class Fb extends SocBase implements SocInterface
{
    public $soc_name = 'fb';

    public $ver = 3.1;

    public $client_id = Keys::FB_CLIENT;

    public $client_secret = Keys::FB_SECRET;

    public $client_public = '';

    public $fields = 'id,name,age_range,first_name,last_name,email';

    public $scope = '';

    public function getLoginUrl(){
        $redirect_url = $this->getRedirectUri();
        $url = 'https://www.facebook.com/v'.$this->ver.'/dialog/oauth?client_id='.$this->client_id.'&display=popup&redirect_uri='.$redirect_url.'&response_type=code'.$this->scope;
        return $url;
    }

    public function getAccessTokenUrl(){
        $auth_url = 'https://graph.facebook.com/v'.$this->ver.'/oauth/access_token';
        return $auth_url;
    }

    public function getUserUrl(){
        $user_url = 'https://graph.facebook.com/v'.$this->ver.'/me';
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
            'v' => $this->ver,
        ];
        $args = [$auth_url, $client, $params];
        return $args;
    }

    public function getUserArgs($response){
        $user_url = $this->getUserUrl();
        $user_id = $this->getUserIdFromResponse($response);
        $access_token = $this->getAccessTokenFromResponse($response);
        $client = new Client();
        $params = [
            'user_id' => $user_id,
            'access_token' => $access_token,
            'fields' => $this->fields,
            'v' => $this->ver,
        ];
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
}