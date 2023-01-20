<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 22.11.2018
 * Time: 18:42
 */

namespace api\components\devInfo;

use Yii;
use common\components\recaptcha\Recaptcha;


class ErrorController
{
    public $errors = array();

    public function addError( $key, $message ){
//        array_push( $this->errors, [ "{$key}" => $message] );
//        array_push( $this->errors, [ $key => $message] );
        $this->errors[$key] = $message;
    }

    public function addErrors( $errors ){
        $this->errors = array_merge( $this->errors, $errors );
    }

    public function returnErrors( $messages = null ){

//        if( $messages ) $this->errors = Utils::merge_associative_arrays( $this->errors, $messages );
        if( $messages ) $this->errors = array_merge( $this->errors, $messages );

//        if( count( $this->errors ) ){
        return [
            "error" => true,
            "error_messages" => $this->errors,
        ];
//        }else{
//            return $this->returnSuccess([ 'error' => false ]);
//        }
    }

    //
    public function returnErrorBadRequest(){
        Yii::$app->response->statusCode = 400;
        return $this->returnErrors( $this->getLocalText( 'request.bad', true ) );
    }

    //
    public function returnActionError(){
        Yii::$app->response->statusCode = 404;
        return $this->returnErrors( ['action:error'=>''] );
    }

    //
    public function returnErrorUserNotFound()
    {
        Yii::$app->response->statusCode = 401;
        $locals = $this->getLocals();
        return $this->returnErrors([self::USER_NOT_FOUND => $locals["user:not_found"] ]);
    }

    public function returnErrorUserIsNotLoggedIn()
    {
        Yii::$app->response->statusCode = 401;
        $locals = $this->getLocals();
        return $this->returnErrors(['user_not_logged_in' => $locals['user:not_logged_in'] ]);
    }

    //
    public function returnErrorDevTokenRequired()
    {
        Yii::$app->response->statusCode = 403;
        $locals = $this->getLocals();
        return $this->returnErrors(['dev_token:required' => $locals['dev_token:required'] ]);
    }

    //
    public function returnErrorEmailConfirmRequired()
    {
        Yii::$app->response->statusCode = 401;
        $locals = $this->getLocals();
        return $this->returnErrors(['email_confirm:required' => $locals['email_confirm:required'] ]);
    }

    //
    public function getDBError( $error ){
        Yii::$app->response->statusCode = 500;
        $app_params = Yii::$app->params;
        $locals = $this->getLocals();
        return ['db_error' => ($app_params['api.sendDetailsOnDBError'] ? $error : $locals['db:error'] ) ];
    }

    //
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
}