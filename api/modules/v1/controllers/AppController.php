<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 24.09.2018
 * Time: 14:44
 */

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\Response;
use api\components\devInfo\actions\DevInfoAction;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
use app\behaviors\ReturnStatusBehavior;

class AppController extends ActiveController
{
    public $errors;

    public $modelClass = 'api\modules\v1\models\AppModel';

    protected $filteredActions;

    public static function allowedDomains()
    {
        return [
            '*',                        // star allows all domains
        ];
    }

    public function behaviors()
    {
        $behaviors =array_merge(parent::behaviors(),[
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin'                           => static::allowedDomains(),
                    'Access-Control-Request-Method'    => ["*"],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Max-Age'           => 3600,
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'text/html' => Response::FORMAT_JSON,
                ]
            ],
            'authentificator' => [
                'class' => HttpBearerAuth::className(),
            ],
            [
                'class' => ReturnStatusBehavior::className(),
            ],
        ]);
        return $behaviors;
    }

    public function getPath()
    {
        $imgPath = Url::base(true);
        $pos = stripos($imgPath, '/api');
        $imgPath = substr($imgPath,0,$pos);
        return $imgPath;
    }

    public function getImagePath($data,$attribute){
        foreach ($data as $item){
            $item->$attribute = $this->getPath().Yii::getAlias('@images').'/'.$item->$attribute;
        }
        return $data;
    }

    public function checkIdentity(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->user->identity){
            $result = ['success' => true];
        }
        return $result;
    }

    public function getIdentity(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(Yii::$app->user->identity){
            $result = Yii::$app->user->identity;
        }
        return $result;
    }

    public function actions()
    {

    }

    public function verbs()
    {

    }

    public static function returnData($data, $header, $status){
        $response = [$status => true, $header => $data];
        return $response;
    }

    protected function getParameterFromRequest( $param_name ){
        $param = Yii::$app->request->post($param_name);
        if(!$param){
            $param = Yii::$app->request->get($param_name);
        }
        return $param;
    }

    protected function returnOpenerResponse(array $response){
        Yii::$app->response->format = Response::FORMAT_HTML;
        return "<script>window.opener.$(window.opener).trigger('oauth:complete', ".json_encode($response).");window.close();</script>";
    }
}