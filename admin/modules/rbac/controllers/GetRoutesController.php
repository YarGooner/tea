<?php
/**
 * Created by PhpStorm.
 * User: ГерманАл
 * Date: 12.01.2019
 * Time: 18:25
 */

namespace admin\modules\rbac\controllers;

use admin\modules\rbac\models\Action;
use admin\modules\rbac\models\Controller;
use yii\helpers\FileHelper;
use Yii;
use yii\web\Response;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

class GetRoutesController extends RbacController
{

    public function actionIndex(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $path = __DIR__ . Yii::$app->getModule('rbac')->rootPath . Yii::$app->getModule('rbac')->userControllerFolder;
        $files = FileHelper::findFiles($path, [
            'only' => [
                '*Controller.php'
            ]
        ]);
        $this->saveRoutesToDb($files);

        return ['success' => true, 'message' => 'Controllers have been successfully added to database'];
    }

    public function saveRoutesToDb($files){
        foreach ($files as $file){
            $controllerId = $this->saveControllerToDb($file);
            if($controllerId){
                $this->saveActionsToDb($file, $controllerId);
            }
        }
    }

    public function saveControllerToDb($controllerFile){
        $fileInfo = pathinfo($controllerFile);
        $file = $fileInfo['basename'];
        $controllerName = strtolower(substr($file,0,-14));
        $controller = Controller::find()->where(['name' => $controllerName])->one();
        if($controller == false) {
            $controller = new Controller();
            $controller->name = $controllerName;
            $controller->save();
        }
        return $controller->id;
    }

    public function saveActionsToDb($controllerFile, $controllerId){
        $fileInfo = pathinfo($controllerFile);
        $file = $fileInfo['filename'];
        $controllerPath = Yii::$app->getModule('rbac')->userControllerNamespace . '\\' . $file;
        $id = $fileInfo['basename'];
        $module = Yii::$app->getModule('rbac')->moduleId;
        $result = [];
        $controller = Yii::createObject($controllerPath, [$id, $module]);
        foreach ($controller->actions() as $id => $value) {
            $result[] = $id;
        }
        $class = new \ReflectionClass($controllerPath);
        foreach ($class->getMethods() as $method) {
            $name = $method->getName();
            if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                $result[] = Inflector::camel2id(substr($name, 6));
            }
        }
        foreach ($result as $item) {
            $this->saveActionToDb($item, $controllerId);
        }
    }

    public function saveActionToDb($actionName, $controllerId){
        if(Action::find()->where(['controller_id' => $controllerId, 'name' =>$actionName])->one()){
            return false;
        }
        $action = new Action();
        $action->name = $actionName;
        $action->controller_id = $controllerId;
        $action->save();
    }

}