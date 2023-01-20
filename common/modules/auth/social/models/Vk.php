<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 02.11.2018
 * Time: 10:23
 */

namespace common\modules\auth\social\models;

use common\modules\auth\Keys;
use common\modules\auth\social\SocInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;


class Vk extends SocBase implements SocInterface
{
    public $soc_name = 'vk';

    public $ver = 5.87;

    public $client_id = Keys::VK_CLIENT;

    public $client_secret = Keys::VK_SECRET;

    public $client_public = '';

    public $fields = 'uid,first_name,last_name,photo_big,sex,about,bdate';

    public $scope = '&scope=friends,email,phone';

    public function getLoginUrl(){
        $redirect_url = $this->getRedirectUri();
        $url = 'https://oauth.vk.com/authorize?client_id=' . $this->client_id . '&display=popup&redirect_uri=' . $redirect_url . '&response_type=code&v=' . $this->ver . $this->scope;
        return $url;
    }

    public function getAccessTokenUrl(){
        $auth_url = 'https://oauth.vk.com/access_token';
        return $auth_url;
    }

    public function getUserUrl(){
        $user_url = 'https://api.vk.com/method/users.get';
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
        $data = $response->data['response'][0];
        return $data;
    }

    public function getAccessTokenFromResponse($response){
        $token = ArrayHelper::getValue( $response->data,'access_token' );
        return $token;
    }
}