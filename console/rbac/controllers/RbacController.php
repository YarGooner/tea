<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.01.2019
 * Time: 17:39
 */

namespace console\rbac\controllers;

use common\modules\auth\models\User;
use yii\console\Controller;

class RbacController extends Controller
{
    public $initPath = '/../templates/init.json';

    public $rulePath = 'console\rbac\rules\\';

    public function actionInit(){
        $file = file_get_contents(__DIR__.$this->initPath);
        $data = json_decode($file);
        $roles = $data->roles;
        $this->createRoles($roles);
        $permissions = $data->permissions;
        $this->createPermission($permissions);
        $children = $data->children;
        $this->addChildren($children);
        $assignments = $data->assignments;
        $this->assign($assignments);
        return "RBAC initialization comleted!";
    }

    private function createRoles($roles){
        $auth = \Yii::$app->authManager;
        foreach ($roles as $role){
            $item = $auth->createRole($role->name);
            $item->description = $role->description;
            $auth->add($item);
            echo 'Role '.$item->name.' have been added.'.PHP_EOL;
        }
    }

    private function createPermission($permissions){
        $auth = \Yii::$app->authManager;
        foreach ($permissions as $permission){
            $item = $auth->createPermission($permission->name);
            $item->description = $permission->description;
            if(isset($permission->rule)){
                $rule = $this->getRule($permission->rule);
                $auth->add($rule);
                $item->ruleName = $rule->name;
            }
            $auth->add($item);
            echo 'Permission '.$item->name.' have been added.'.PHP_EOL;
        }
    }

    private function addChildren($children){
        $auth = \Yii::$app->authManager;
        foreach ($children as $item){
            $parent = $this->getAssignmentItemObject($item->parent);
            $child = $this->getAssignmentItemObject($item->child);
            $auth->addChild($parent, $child);
            echo $item->child .' have been added as a child of ' . $item->parent .PHP_EOL;
        }
    }

    private function assign($assignments){
        $auth = \Yii::$app->authManager;
        foreach ($assignments as $assignment){
            $role = $this->getAssignmentItemObject($assignment->parent);
            $userId = $this->getUserIdFromUsername($assignment->child);
            $auth->assign($role, $userId);
            echo $assignment->parent . ' and '. $assignment->child .' have been assigned.'.PHP_EOL;
        }
    }

    private function getUserIdFromUsername($username){
        $user = User::find()->where(['username' => $username])->one();
        return $user->id;
    }

    private function getAssignmentItemObject($item){
        $auth = \Yii::$app->authManager;
        $object = $auth->getRole($item);
        if(!$object){
            $object = $auth->getPermission($item);
        }
        return $object;
    }

    private function getRule($item){
        $ruleClass = $this->rulePath . $item;
        $rule = new $ruleClass;
        return $rule;
    }
}