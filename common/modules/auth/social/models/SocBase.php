<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 26.11.2018
 * Time: 13:56
 */

namespace common\modules\auth\social\models;

use common\components\UserUrlManager;
use Yii;

class SocBase
{
    public $soc_name;

    public $ver;

    public $client_id;

    public $client_secret;

    public $client_public;

    public $fields;

    public $scope;

    public  static function getRedirectUri(){
        $url = UserUrlManager::getDomainUrl().'/api/'.Yii::$app->controller->route;
        return $url;
    }
}