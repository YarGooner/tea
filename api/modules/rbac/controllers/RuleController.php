<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.01.2019
 * Time: 12:39
 */

namespace app\modules\rbac\controllers;


class RuleController extends RbacController
{
    private $rulePath ='app\modules\rbac\rules';

    public function actionAdd(){
        $post = \Yii::$app->request->post();
        if(array_key_exists('rule',$post)){
            $success = $this->add($post);
        }
        $success ?
            $response = $this::returnData('Rule added','status','success') :
            $response = $this::returnData('There is an error while adding rule','errors','error');
        return $response;
    }

    private function add($post){
        $auth = $this->getAuth();
        $ruleClass = $this->rulePath . '\\' . $post['rule'];
        $rule = new $ruleClass;
        $success = $auth->add($rule);
        return $success;
    }


}