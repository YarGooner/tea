<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 24.01.2019
 * Time: 12:53
 */

namespace admin\modules\rbac\modules\api\controllers;

use Yii;
use yii\web\Response;
use admin\modules\rbac\models\RoleModelPermission;

class ModelPermissionController extends RbacApiController
{
    public function actionAdd(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $field_id = $post['field_id'];
        $role_id = $post['role_id'];
        $type = $post['type'];
        $permission = RoleModelPermission::find()->where(['field_id' => $field_id, 'role_id' => $role_id, 'type' => $type])->one();
        if($permission){
            return ['error' => true, 'message' => 'This permission is already exist!'];
        } else {
            $permission = new RoleModelPermission();
            $permission->role_id = $role_id;
            $permission->field_id = $field_id;
            $permission->type = $type;
        }
        if(!$permission->save()){
            var_dump($permission->errors);
        }
        return ['success' => true, 'message' => 'Permission have been added successfully!'];
    }

    public function actionRemove(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $field_id = $post['field_id'];
        $role_id = $post['role_id'];
        $type = $post['type'];
        $permission = RoleModelPermission::find()->where(['field_id' => $field_id, 'role_id' => $role_id, 'type' => $type])->one();
        if(!$permission){
            return ['error' => true, 'message' => 'There is no such permission!'];
        }
        if(!$permission->delete()){
            var_dump($permission->errors);
        }
        return ['success' => true, 'message' => 'Permission have been deleted successfully!'];
    }
}