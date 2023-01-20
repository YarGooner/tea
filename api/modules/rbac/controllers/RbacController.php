<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.01.2019
 * Time: 11:26
 */

namespace app\modules\rbac\controllers;

use yii\rest\Controller;
use Yii;
use app\behaviors\ReturnStatusBehavior;

class RbacController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return array_merge($behaviors,[
            [
                'class' => ReturnStatusBehavior::className(),
            ],
        ]);
    }

    public function getAuth(){
        return Yii::$app->authManager;
    }

    public static function returnData($data, $header, $status){
        $response = [$status => true, $header => $data];
        return $response;
    }
}