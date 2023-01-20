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
        $this->getRouteRecursive(Yii::$app,$result);
        return $result;
    }

    public function getRouteRecursive($module,&$result) {
        try {
            foreach ($module->getModules() as $id => $child) {
                if (($child = $module->getModule($id)) !== null) {

                    $this->getRouteRecursive($child, $result);
                }
            }
            foreach ($module->controllerMap as $id => $type) {

                $this->getControllerActions($type, $id, $module, $result);
            }
            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '', $result);
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    private function getControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = @Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file)) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $id = Inflector::camel2id(substr(basename($file), 0, -14));
                    $className = $namespace . Inflector::id2camel($id) . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')|| is_subclass_of($className, 'yii\rest\ActiveController')) {
                        $this->getControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    private function getControllerActions($type, $id, $module, &$result)
    {
        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller, $result);
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    private function getActionRoutes($controller, &$result)
    {
        $class = new \ReflectionClass($controller);
        $actions = [];
        $inheritedActions = $this->getInheritedActions($class->getName());
        foreach ($inheritedActions as $key => $value){
            $actions[] = [
                'name' => $key,
                'class' => $value,
            ];
        }
        foreach ($actions as $action){

            $this->getInheritedActionRoute($action,$result);
        }
        $this->dump($result);

//        $this->dump($class->getMethods());
        foreach ($class->getMethods() as $method) {
            $this->getActionRoute($class,$method,$result);
        }
    }

    private function getInheritedActionRoute($method, &$result){
        $methodName = $method['name'];
        $className = $method['class'];
        $request = $this->returnRequestParams($className,'run');
        if($request == null) {
            $request = [];
        }
        $result[$methodName]['request'] = $request;
    }

    private function getActionRoute($class, $method, &$result){
        $name = $method->getName();
//        $this->dump($name);
        if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
            $pos = strripos($class->name,'\\');
            $className = Inflector::camel2id(substr($class->name, $pos));
            $className = str_replace(['controller','-','\\'],'',$className);
            if ($className == Yii::$app->controller->id){
                $this->getMethodRequestParams(Yii::$app->controller->className(),$name,$result);
                $this->getMethodResponseParams($class, $method, $result);
                if($result['dev-info']) {
                    $result['dev-info'] = [
                        'dev_only' => true,
                        'description' => "Dev only action. Returns all actions of the controller",
                        'response' => ["methods" => "array :: dev // dev only"],
                    ];
                }
            }
        }
    }

    private function getMethodRequestParams($className, $name, &$result){
        if(stripos($name,'action') !== false) {
            $methodName = Inflector::camel2id(substr($name, 6));
        } else {
            $methodName = $name;
        }
        $request = $this->returnRequestParams($className,$name);
        if($request == null) {
            $request = [];
        }
        $result[$methodName]['request'] = $request;
    }

    private function getMethodResponseParams($class, $method, &$result){
        $name = $method->getName();
        $methodName = Inflector::camel2id(substr($name, 6));
        $response = [];
//        $modelClass = $this->$modelClass.substr(basename($class->getName()),0,-10);
        $className = $class->getName();
        $className = str_replace('\\','/',$className);
        $modelClass = substr(basename($className),0,-10);
        if($modelClass != 'Test' && $modelClass != 'User') {
            $model = new $modelClass();
            $columns = $model->attributeLabels();
            if ($name == 'actionIndex' || $name == 'actionView') {
                $response = [
                    Inflector::camel2id(substr($name, 6)) => array_keys($columns),
                ];
            }
        }
        $result[$methodName]['response'] = $response;
    }

    private function returnRequestParams($className,$actionName)
    {
        $comments = new \ReflectionMethod($className,$actionName);
        $comments = $comments->getDocComment();
        $result = $this->getRequestParamsFromDoc($comments);
        return $result;
    }

    private function getRequestParamsFromDoc($comments){
        $comment_array = explode('* @',$comments);
        $result = [];
        foreach ($comment_array as $item) {
            $item = trim($item);
//            $pos = stripos($item,'return');
//            if($pos !== false){
//                $end_pos = stripos($item,' ')-1;
//                $result['return'] = substr($item,$pos+7,$end_pos);
//            }
            $check_param = stripos($item, 'param');
            if ($check_param !== false) {
                $start_pos = stripos($item, '$');
                $param_name = substr($item, $start_pos + 1);
                $param_type_start_pos = stripos($item, ' ');
                $param_type = substr($item, $param_type_start_pos, $start_pos - $param_type_start_pos);
                $param_type = trim($param_type);
                Formatter::formatRequestParams($param_name, $param_type, $result);
            }
        }
        return $result;
    }

    private function getInheritedActions(){
        $class = Yii::$app->controller;
        return $class->actions();
    }

    public function dump($fun){
        VarDumper::dump($fun,10,1);
    }
}