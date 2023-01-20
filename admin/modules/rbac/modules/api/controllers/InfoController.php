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
use admin\modules\rbac\models\RoleAssign;
use admin\modules\rbac\models\Role;
use admin\modules\rbac\models\RolePermission;
use admin\modules\rbac\models\Controller;
use admin\modules\rbac\models\Action;
use admin\modules\rbac\models\RoleModelPermission;
use admin\modules\rbac\models\Model;
use admin\modules\rbac\models\Field;

class InfoController extends RbacApiController
{
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $users = $this->getUsersList();
        $roles = $this->getRolesList();
        $controllers = $this->getControllersList();
        $models = $this->getModelsList();
        return [
            'success' => true,
            'users' => $users,
            'roles' => $roles,
            'controllers' => $controllers,
            'models' => $models
        ];
    }

    private function getUsersList(){
        $userModel = $userModel = Yii::$app->getModule('rbac')->userModel;
        $db_users = $userModel::find()->all();
        $db_roles_assign = RoleAssign::find()->all();
        $users = [];
        foreach ($db_users as $db_user){
            foreach ($db_roles_assign as $item){
                if($item->user_id == $db_user->id){
                    $role_id = $item->role_id;
                }
            }
            $users[] = ['id' => $db_user->id, 'username' => $db_user->username, 'role_id' => $role_id];
        }
        return $users;
    }

    private function getRolesList(){
        $db_roles = Role::find()->all();
        $roles = [];
        foreach ($db_roles as $db_role){
            $controllers_permissions = $this->getRoleControllersPermissions($db_role);
            $models_permissions = $this->getRoleModelsPermissions($db_role);
            $roles[] = [
                'id' => $db_role->id,
                'name' => $db_role->name,
                'description' => $db_role->description,
                'permissions' => [
                    'controllers' => $controllers_permissions,
                    'models' => $models_permissions,
                ]
            ];
        }
        return $roles;
    }

    private function getRoleControllersPermissions($db_role){
        $permissions = [];
        $db_permissions = RolePermission::find()->where(['role_id' => $db_role->id])->all();
        $db_controllers = Controller::find()->all();
        foreach ($db_controllers as $db_controller) {
            $db_actions = Action::find()->where(['controller_id' => $db_controller->id])->all();
            foreach ($db_actions as $db_action) {
                foreach ($db_permissions as $db_permission) {
                    if($db_permission->action_id == $db_action->id){
                        $permissions[$db_controller->name][] = $db_action->name;
                    }
                }
            }
        }
        return $permissions;
    }

    private function getRoleModelsPermissions($db_role){
        $permissions = [];
        $db_permissions = RoleModelPermission::find()->where(['role_id' => $db_role->id])->all();
        $db_models = Model::find()->all();
        foreach ($db_models as $db_model){
            $db_fields = Field::find()->where(['model_id' => $db_model->id])->all();
            foreach ($db_fields as $db_field){
                foreach ($db_permissions as $db_permission){
                    if ($db_field->id == $db_permission->field_id){
                        if ($db_permission->type == 0){
                            $permissions[$db_model->name]['read'][] = $db_field->name;
                        } else {
                            $permissions[$db_model->name]['write'][] = $db_field->name;
                        }
                    }
                }
            }
        }
        return $permissions;
    }

    private function getControllersList(){
        $controllers = [];
        $db_controllers = Controller::find()->all();
        foreach ($db_controllers as $db_controller){
            $db_actions = Action::find()->where(['controller_id' => $db_controller->id])->all();
            foreach ($db_actions as $db_action) {
                $controllers[$db_controller->name][] = [
                    'id' => $db_action->id,
                    'name' => $db_action->name,
                    'description' => $db_action->description,
                ];
            }
        }
        return $controllers;
    }

    private function getModelsList(){
        $models = [];
        $db_models = Model::find()->all();
        foreach ($db_models as $db_model) {
            $db_fields = Field::find()->where(['model_id' => $db_model->id])->all();
            foreach ($db_fields as $db_field) {
                $models[$db_model->name][] = [
                    'id' => $db_field->id,
                    'name' => $db_field->name,
                    'description' => $db_field->description,
                ];
            }
        }
        return $models;
    }
}