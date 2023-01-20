<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.01.2019
 * Time: 11:28
 */

namespace app\modules\rbac\controllers;


class PermissionController extends RbacController
{
    public function actionCreate(){
        $post = \Yii::$app->request->post();
        if(array_key_exists('name',$post) && array_key_exists('description',$post)){
            $success = $this->create($post);
        }
        $success ?
            $response = $this->returnData('Permission created','status','success') :
            $response = $this::returnData('There is an error while creating permission','errors','error');
        return $response;
    }

    public function actionAdd(){
        $post = \Yii::$app->request->post();
        if(array_key_exists('role',$post) && array_key_exists('permission',$post)){
            $success = $this->add($post);
        }
        $success ?
            $response = $this::returnData('Permission added','status','success') :
            $response = $this::returnData('There is an error while adding permission','errors','error');
        return $response;
    }

    public function actionAddChild(){
        $post = \Yii::$app->request->post();
        if(array_key_exists('parent',$post) && array_key_exists('child',$post)){
            $success = $this->addChild($post);
        }
        $success ?
            $response = $this::returnData('Permission added','status','success') :
            $response = $this::returnData('There is an error while adding permission','errors','error');
        return $response;
    }

    private function create($post){
        $name = $post['name'];
        $description = $post['description'];
        $auth = $this->getAuth();
        $permission = $auth->createPermission($name);
        $permission->description = $description;
        if(array_key_exists('rule', $post)){
            $permission->ruleName = $post['rule'];
        }
        return $auth->add($permission);
    }

    private function add($post){
        $auth = $this->getAuth();
        $role = $auth->getRole($post['role']);
        $permission = $auth->getPermission($post['permission']);
        return $auth->addChild($role,$permission);
    }

    private function addChild($post){
        $auth = $this->getAuth();
        $parent = $auth->getPermission($post['parent']);
        $child = $auth->getPermission($post['child']);
        return $auth->addChild($parent,$child);
    }
}