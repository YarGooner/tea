<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.09.2018
 * Time: 11:30
 */

namespace api\components\devInfo\controllers;


use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\Response;
use api\components\devInfo\actions\DevInfoAction;



class DevInfoController extends Controller
{
    public $token;

    public $modelClass = 'common\models\User';

    public $enableCsrfValidation;

    public $controllersPath;

    public $defaultControllers;

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
        ]);
        return $behaviors;
    }
    /**
     * Lists all Route models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $check = Yii::$app->request->post('dev_token') != $this->token && Yii::$app->request->get('dev_token') != $this->token;
        if ($check) {
            return [
                "error"=> true,
                "error_messages" => [
                    "dev_token:wrong" => "Wrong dev_token."
                ],
            ];
        }
        $controllers = $this->getControllerNames();
        $return = ['success' => true,'controllers' => $controllers];
        return $return;
    }

    private function getControllerNames(){
        $path = $this->controllersPath;
        $files = scandir($path);
        $controllers = [];
        foreach ($files as $file){
            if(stripos($file,'Controller')) {
                $controller = Inflector::camel2id(substr($file, 0, -14));
                foreach ($this->defaultControllers as $item) {
                    if($controller == $item) {
                        $controllerIsDefault = true;
                    }
                }
                if (!$controllerIsDefault){
                    $controllers[] = $controller;
                }
                unset($controllerIsDefault);
            }
        }
        return $controllers;
    }

    public function verbs()
    {
    }

    public function actions()
    {
        return [
            'dev-info' => DevInfoAction::className(),
        ];
    }

    public function beforeAction($action)
    {
        // ...set `$this->enableCsrfValidation` here based on some conditions...
        // call parent method that will check CSRF if such property is true.
        if ($action->id === 'ik') {
            # code...
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
}