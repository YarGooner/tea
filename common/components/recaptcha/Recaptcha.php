<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 09.08.2018
 * Time: 17:26
 */

namespace common\components\recaptcha;

use yii\helpers\Json;
use yii\httpclient\Client;
use Yii;

/*
 * HELP: https://developers.google.com/recaptcha/docs/display
 */

class Recaptcha
{

    public static $publickey = "";
    public static $privatekey = "";
    public static $errors;

    public static function getWidget(){
        return '<div onload="window.console.log(\'>>>\'+this);" class="g-recaptcha" data-sitekey="'.self::$publickey.'"></div>';
    }

    public static function validate( $recaptcha_token )
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://www.google.com/recaptcha/api/siteverify')
            ->setData([
                'secret' => self::$privatekey,
                'response' => $recaptcha_token,
            ])
            ->send();
        if ($response->isOk) {
            $result = Json::decode( $response->content );
            if( $result['success'] == true ) return true;
            self::$errors = $result;
            return self::$errors;
        }
    }

    public function returnErrorRecaptcha()
    {
        Yii::$app->response->statusCode = 403;
        $response = [ 'error_codes' => join( '; ', Recaptcha::$errors["error-codes"] ) ];
        if( $this->HAS_DEV_TOKEN == true ) $response = array_merge( $response, [
            'recaptcha_validation_error' => Recaptcha::$errors,
//            'private_key' => Recaptcha::$privatekey,
            'public_key' => Recaptcha::$publickey,
        ]);
        return $this->returnErrors( $response );
    }

    private function returnErrors( $messages = null ){

        if( $messages ){
            if($this->errors) {
                $this->errors = array_merge( $this->errors, $messages );
            } else $this->errors = $messages;
        }
        return [
            "error" => true,
            "error_messages" => $this->errors,
        ];
    }
}
