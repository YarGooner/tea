<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.09.2018
 * Time: 17:22
 */

namespace admin\models;


use common\models\LoginForm;

class LoginFormAdmin extends LoginForm
{
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильное имя пользователя или пароль.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'rememberMe' => 'Запомнить меня',
            'password' => 'Пароль',
        ];
    }
}