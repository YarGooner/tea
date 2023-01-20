<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 21.01.2019
 * Time: 17:40
 */

namespace admin\modules\rbac\filters;

use Yii;
use yii\web\Response;
use admin\modules\rbac\models\RoleAssign;
use admin\modules\rbac\models\Role;
use admin\modules\rbac\models\Controller;
use admin\modules\rbac\models\Action;
use admin\modules\rbac\models\RolePermission;
use yii\web\UnauthorizedHttpException;

class RbacActionFilter
{

    public function checkAccess(){
        if($this->checkUnauthorized()){
            return true;
        }
        if($this->checkPermission()) {
            return true;
        }
    }

    private function checkUnauthorized(){
        foreach (Yii::$app->getModule('rbac')->unauthorizedActions as $controller => $actions){
            if(Yii::$app->controller->id == $controller || $controller == '*'){
                foreach ($actions as $action){
                    if(Yii::$app->controller->action->id == $action || $action == '*'){
                        return true;
                    }
                }
            }
        }
        if(Yii::$app->user && (Yii::$app->controller->id == 'rbac' || Yii::$app->controller->module->id == 'rbac-api')){
            return true;
        }
        return false;
    }

    private function checkPermission(){
        $userModel = Yii::$app->getModule('rbac')->userModel;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $token = substr(Yii::$app->request->getHeaders()['authorization'],7);
        $user = $userModel::findIdentityByAccessToken($token);
        $role = RoleAssign::find()->where(['user_id' => $user->id])->one();
        if(!$role){
            throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
        }
        $role_id = Role::find()->where(['id' => $role->role_id])->one()->id;
        $controller = Controller::find()->where(['name' => Yii::$app->controller->id])->one();
        $db_action = Action::find()->where(['controller_id' => $controller->id, 'name' => Yii::$app->controller->action->id])->one();
        $permission = RolePermission::find()->where(['role_id' => $role_id, 'action_id' => $db_action->id])->one();
        if(!$permission){
            throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
        }
        return true;
    }
}