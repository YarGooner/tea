<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.10.2018
 * Time: 16:21
 */

namespace console\controllers;


use yii\console\Controller;
use console\models\SignupForm;
use Yii;

class UserAdminController extends Controller
{


    public function actionCreate(){

        $model = new SignupForm();
        $model->username = $this->prompt('Введите имя пользователя:', ['required' => true]);
        $model->email = $this->prompt('Введите имя e-mail:', ['required' => true]);
        $model->password = $this->prompt('Введите пароль:', ['required' => true]);
            if ($user = $model->signup()) {
                return print_r('Пользователь '.$model->username.' добавлен');
            } else var_dump($model->errors);
    }

}