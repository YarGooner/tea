<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 22.11.2018
 * Time: 18:35
 */

namespace app\modules\components;

use Yii;
use common\components\recaptcha\Recaptcha;


class ReturnValueFormatter
{
    public $HAS_DEV_TOKEN;

    public function returnData($data, $header, $status){
        $response = [$status => true, $header => $data];
        return $response;
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

    public function returnSuccess( $answer = null ){
        if( isset($answer) ) return array_merge( ['success' => true ], $answer );
        return ['success' => true ];
    }

    public function returnErrors( $messages = null ){

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