<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 24.01.2019
 * Time: 12:52
 */

namespace admin\modules\rbac\modules\api\controllers;

use Yii;
use yii\web\Response;
use admin\modules\rbac\models\Role;
use admin\modules\rbac\models\RoleAssign;

class RoleController extends RbacApiController
{
    public function actionCreate(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $model = new Role();
        $model->name = $post['name'];
        $model->description = $post['description'];
        if (!$model->save()) {
            var_dump($model->errors);
        }
        return ['success' => true, 'role' => $model];
    }

    public function actionDelete(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $role = Role::find()->where(['id' => $post['id']])->one();
        if(!$role){
            return ['error' => true, 'message' => 'There is no role with these id!'];
        }
        if(!$role->delete()){
            var_dump($role->errors);
        }
        return ['success' => true, 'message' => 'Role have been deleted successfully!'];
    }

    public function actionAssign(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $db_role_assign = RoleAssign::find()->where(['user_id' => $post['user_id']])->one();
        if(!$db_role_assign){
            $db_role_assign = new RoleAssign();
            $db_role_assign->user_id = $post['user_id'];
        }
        $db_role_assign->role_id = $post['role_id'];
        if(!$db_role_assign->save()){
            var_dump($db_role_assign->errors);
        }
        return ['success' => true, 'message' => 'Role assigned successfully!'];
    }
}