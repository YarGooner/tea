<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 26.11.2018
 * Time: 10:06
 */

namespace common\modules\auth\controllers;

use common\modules\auth\models\User;

class LoginController
{
    public function loginUserByEmail($email, $password ){
        $user = User::getUserByEmail($email);
        if(!$user){
            return $this->returnData(["email:wrong" => "Wrong e-mail or password.", "password:wrong" => "Wrong e-mail or password."],'error_messages','error');
        }
        if($user->validatePassword($password)){
            $user->auth_source = 'login';
            if(!$user->auth_key){
                $user->generateAuthKey();
                $user->token_deadtime = time() + 30*60;
            }
            $user->save();
            Yii::$app->user->login($user);
            return $this->getProfile();
        } else {
            return $this->returnData(["email:wrong" => "Wrong e-mail or password.", "password:wrong" => "Wrong e-mail or password."],'error_messages','error');
        }
    }
}