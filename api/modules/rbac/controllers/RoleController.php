<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.01.2019
 * Time: 11:29
 */

namespace app\modules\rbac\controllers;


class RoleController extends RbacController
{
    public function actionCreate(){
        $post = \Yii::$app->request->post();
        if(array_key_exists('name',$post) && array_key_exists('description',$post)){
            $success = $this->create($post);
        }
        $success ?
            $response = $this::returnData('Role created','status','success') :
            $response = $this::returnData('There is an error while creating role','errors','error');
        return $response;
    }

    private function create($post){
        $name = $post['name'];
        $auth = $this->getAuth();
        $role = $auth->createRole($name);
        if (array_key_exists('description',$post)){
            $description = $post['description'];
            $role->description = $description;
        }
        $success = $auth->add($role);
        if (array_key_exists('children',$post)){
            foreach ($post['children'] as $item){
                $child = $auth->getPermission($item);
                if(!$child){
                    $child = $auth->getRole($item);
                }
                $auth->addChild($role,$child);
            }
        }
        return $success;
    }
}