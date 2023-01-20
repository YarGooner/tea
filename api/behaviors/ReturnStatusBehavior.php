<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.01.2019
 * Time: 15:11
 */

namespace app\behaviors;

use Yii;
use yii\base\Behavior;

class ReturnStatusBehavior extends Behavior
{

    //
    public function getFormatedResponse( $success = true, $data = null, $data_description = null ){

        if( $success ){
//            $response = ['success' => true ];
            if( $data ){
                if( is_string($data) ){
                    $response = [ $data => $data_description ];
                }else{
                    $response = $data;
                }
            }
        }else{
//            $response = ['error' => true ];
            if( $data ){
                if( is_string($data) ){
//                    $response = [ $data => $data_description ];
                    $response['name'] = $data;
                    $response['message'] = is_string($data_description) ? ['-'=>$data_description] : $data_description;
                }else{
                    $response['name'] = 'Unknown error';
                    $response['message'] = $data;
                }
            }
        }

        return $response;
    }

    //
    private function _returnError( $error_id, $error_description, $statusCode = null){
        if( $statusCode ) Yii::$app->response->statusCode = $statusCode;
        $response = self::getFormatedResponse( false, $error_id, $error_description );
        Yii::error( "ERROR: ".$error_id, 'api');
        return $response;
    }



    //
    public function returnSuccess($data, $header = 'data', $links = null){
        $response = [$header => $data];
        if($links != null){
            $response['links'] = $links;
        }

        Yii::info( 'SUCCESS', 'api');

        return $response;
    }

    //
    public function returnError( $error = null, $error_description = null, $statusCode = 500 ){
        Yii::$app->response->statusCode = $statusCode;
        $response = self::getFormatedResponse( false, $error, $error_description );
        Yii::error( "ERROR: ", 'api');

        return $response;
    }

    //
    public function returnErrorBadRequest(){
        return self::_returnError( 'request:bad','Некорректный запрос', 400 );
    }

    //
    public function returnErrorUserIsNotLoggedIn()
    {
        return self::_returnError( 'user:not_logged_in','Пользователь не авторизован', 401 );
    }

    //
    public function returnUserNotFoundError(){
        return self::_returnError( 'user:not_found','Пользователь не найден', 401 );
    }

    //
    public function returnActionError(){
        return self::_returnError( 'action:error','Пользователь не найден', 404 );
    }

    //
    public function getDBError( $error ){
        return self::_returnError( 'db:error','Ошщибка обращения к БД', 500 );
    }
}