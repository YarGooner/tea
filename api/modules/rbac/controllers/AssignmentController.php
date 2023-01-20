<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.01.2019
 * Time: 11:29
 */

namespace app\modules\rbac\controllers;

use common\models\User;

class AssignmentController extends RbacController
{
    public function actionAdd(){
        $post = \Yii::$app->request->post();
        if(array_key_exists('role',$post) && array_key_exists('user',$post)){
            $success = $this->add($post);
        }
        $success ?
            $response = $this::returnData('Role assigned','status','success') :
            $response = $this::returnData('There is an error while assigning role','errors','error');
        return $response;
    }

    private function add($post){
        $auth = $this->getAuth();
        $role = $auth->getRole($post['role']);
        $user = User::find()->where(['username' => $post['user']])->one();
        $userId = $user->getId();
        $success = $auth->assign($role,$userId);

        return $success;
    }
}