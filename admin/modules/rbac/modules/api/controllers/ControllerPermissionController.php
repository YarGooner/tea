<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 24.01.2019
 * Time: 12:52
 */

namespace admin\modules\rbac\modules\api\controllers;

use yii\web\Response;
use Yii;
use admin\modules\rbac\models\RolePermission;

class ControllerPermissionController extends RbacApiController
{
    public function actionAdd(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $action_id = $post['action_id'];
        $role_id = $post['role_id'];
        $permission = RolePermission::find()->where(['action_id' => $action_id, 'role_id' => $role_id])->one();
        if($permission){
            return ['error' => true, 'message' => 'This permission is already exist!'];
        } else {
            $permission = new RolePermission();
            $permission->role_id = $role_id;
            $permission->action_id = $action_id;
        }
        if(!$permission->save()){
            var_dump($permission->errors);
        }
        return ['success' => true, 'message' => 'Permission have been added successfully!'];
    }

    public function actionRemove(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $action_id = $post['action_id'];
        $role_id = $post['role_id'];
        $permission = RolePermission::find()->where(['action_id' => $action_id, 'role_id' => $role_id])->one();
        if(!$permission){
            return ['error' => true, 'message' => 'There is no such permission!'];
        }
        if(!$permission->delete()){
            var_dump($permission->errors);
        }
        return ['success' => true, 'message' => 'Permission have been deleted successfully!'];
    }
}