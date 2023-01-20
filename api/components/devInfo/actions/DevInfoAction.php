<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 16.11.2018
 * Time: 10:23
 */
namespace api\components\devInfo\actions;

use Yii;
use yii\base\Action;
use api\components\devInfo\src\GetInfo;
use api\components\devInfo\src\Formatter;

class DevInfoAction extends Action
{
    public $token = '';

    /**
     * @param null $dev_token
     * @return array
     */

    public function run($dev_token=null){
        if(!$this->checkDevToken()){
            return Formatter::returnData(["dev_token:wrong" => "Wrong dev_token."],'error_messages','error');
        }
//        $result = [];
        $info = new GetInfo();
        $result = $info->getRoutes();
//        var_dump($result);
//        var_dump($info->getRouteRecursive(Yii::$app, $result));
        if($result!=[]){
            return Formatter::returnData($result,'actions','success');
        }
        else $result = ['success' => false];
        return $result;
    }


    public function checkDevToken($dev_token=null){
        $dev_token = Yii::$app->request->post('dev_token');
        if ($dev_token == null){
            return [
                "error"=> true,
                "error_messages" => [
                    "dev_token:required" => "#The developer token is required."
                ],
            ];
        }
        if ($dev_token != $this->token)     {
            return [
                "error"=> true,
                "error_messages" => [
                    "dev_token:wrong" => "Wrong dev_token."
                ],
            ];
        }
        $result = [];
        $this->getRouteRecrusive(Yii::$app, $result);
        if($result!=[]){
            $result = ['success'=> true, 'methods'=> $result];
        }

        else $result = ['success' => false];
        return $result;
    }

    public function verbs()
        {
        }
}