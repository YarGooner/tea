<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 27.09.2018
 * Time: 11:58
 */

namespace admin\controllers;
use admin\components\AuthHandler;


class AuthController extends AdminController
{

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

}