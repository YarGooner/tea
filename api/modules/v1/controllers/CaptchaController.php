<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 12.11.2018
 * Time: 18:02
 */

namespace api\modules\v1\controllers;


use common\components\recaptcha\Recaptcha;
use yii\web\Controller;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class CaptchaController extends Controller
{
    public function behaviors()
    {
        $behaviors = [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'text/html' => Response::FORMAT_JSON,
                ]
            ],
        ];
        return $behaviors;
    }

    public function getMethodsInfo()
    {
        $methods =  parent::getMethodsInfo(); //

        $methods = array_merge( $methods, [

            "get" =>[
                'request' => [
                    'dev_token' => 'string :: optional :: dev',
                ],
                'response' => [
                    'public_key' => 'string',
                ]
            ]
        ]);

        return $methods;
    }

    public function actionGet(){
        return $this->returnSuccess([
            'public_key' => Recaptcha::$publickey,
        ]);
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

    private function returnSuccess( $answer = null ){
//        var_dump($answer);
        if( isset($answer) ) return array_merge( ['success' => true ], $answer );
        return ['success' => true ];
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