<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 22.11.2018
 * Time: 18:01
 */

namespace api\components\devInfo\src;

use Yii;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

class GetInfo
{
    public $modelClass = 'api\modules\v1\models\\';

    public function getRoutes(){
        $result = [];
        $app = Yii::$app;
        $controller = $app->controller;
        $actionList = $this->getActionList($controller);
            foreach ($actionList as $item){
                $info = $this->getActionInfo($controller,$item);
                if($info != $item){
                    $item = $info;
                } else {
                    $item = [
                        $item => [
                            'description' => $this->getActionDescription(),
                            'methods' => [
                                '*' => [
                                    'access' => '*',
                                    'request' => $this->getMethodRequest(),
                                    'response' => $this->getMethodResponse(),
                                ]
                            ]
                        ]
                    ];
                }
//                var_dump($item);
                $result = array_merge($result, $item);
            }
//        $this->dump($result);

        return $result;
    }

    private function getActionList($controller){
        $actions = $this->getControllerActions($controller);
        $names = [];
        $actionList = [];
        foreach ($actions as &$action) {
            if($action->name != 'actions'){
                $name = Inflector::camel2id(substr($action->name,6));
                $actionList[] = $name;
            } else {
                $class = Yii::$app->controller;
                $inheritedActions = $class->actions();
                foreach ($inheritedActions as $key => $value){
                    $className = $key;
                    $names[] = $className;
                }
            };
        }
        $actionList = array_merge($actionList,$names);
        return $actionList;
    }

    private function getActionInfo($controller, $action){
        $rules = $this->getControllerRules($controller);
        if (!$rules) {
            return $action;
        }
        foreach ($rules as $key => $value) {
            if($key == $action){
                $action = [$key => ['description' => $this->getActionDescription(),'methods' => $value]];
            }
        }
        return $action;
    }

    private function getActionDescription(){
        return []; //TODO Придумать, как автоматизировать описание
    }

    public function getControllerRules($controller){
        $accessRules = $controller->behaviors['access']->rules;
        $rules = [];
        if (!$accessRules){
            return false;
        }
        foreach ($accessRules as $accessRule) {
            $actions = $accessRule->actions;
            $verbs = $accessRule->verbs;
            $roles_array = $accessRule->roles;
            $roles = implode(', ', $roles_array);
            if ($verbs) {
                $verbs = explode(', ', $verbs);
                foreach ($actions as $action) {
                    foreach ($verbs as $verb) {
                        $rules[$action][$verb] = [
                            'access' => $roles,
                        ];
                    }
                }
            } else {
                foreach ($actions as $action) {
                    $rules[$action]['*'] = [
                        'access' => $roles,
                    ];
                }
            }
            foreach ($rules[$action] as &$item) {
                $request = $this->getMethodRequest($roles_array);
                $response = $this->getMethodResponse($roles_array);
                $item = array_merge($item,[
                    'request' => $request,
                    'response' => $response,
                ]);
            }
        }
        return $rules;
    }

    public function getMethodRequest($roles = null){
        $request = [];
//        foreach ($roles as $role) {
//            $request[$role] = []; //TODO Придумать, как получать параметры запросов
//        }
        return $request;
    }

    public function getMethodResponse($roles = null){
        $response = [];
//        foreach ($roles as $role) {
//            $response[$role] = []; //TODO Придумать, как получать параметры ответов
//        }
        return $response;
    }

    public function getControllerActions($controller){
        $controllerRef = new \ReflectionClass($controller);
        $methods = $controllerRef->getMethods();
        $actions = [];
        foreach ($methods as $method) {
            if (stripos($method->name,'action') === 0){
                $actions[] = $method;
            }
        }
        return $actions;
    }

    public function dump($fun){
        VarDumper::dump($fun,10,1);
    }
}