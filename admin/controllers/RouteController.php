<?php
namespace admin\controllers;

use Exception;
use yii\helpers\Url;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

/**
 * Description of RuleController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class RouteController extends AdminController
{

    public function actionIndex()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [];
        $result = $this->getItems($result);
        return $result;
    }

    private function getItems($result){
        $path = Yii::getAlias('@app').'\views';
        $dirs = scandir($path);
        foreach ($dirs as $dir){
            if ($dir != '.' && $dir != '..' && $dir != 'layouts' && $dir != 'site'){

                if($devInfo = file_exists($path.'\\'.$dir.'\\dev-info.json')){
                $devInfo = file_get_contents($path.'\\'.$dir.'\\dev-info.json');
                $devInfo = json_decode($devInfo);
                $priority = $devInfo->priority;
                $translation = $devInfo->translation;
                if ($group = $devInfo->group){
//                    var_dump($group);
                    $result[] = ['name' => $dir, 'priority' => $priority, 'translation' => $translation, 'group' => $group];
                } else
                $result[] = ['name' => $dir, 'priority' => $priority, 'translation' => $translation];
                } else  $result[] = ['name' => $dir];
            }
        }
        usort($result, array($this,"cmp"));


        if(Yii::$app->user->isGuest){
            $result[] = ['name' => 'site/login', 'priority' => 0, 'translation' => 'Войти', 'group' => 'Войти'];
        } else {
            $result[] = ['name' => 'site/logout', 'priority' => 0, 'translation' => 'Выйти (' . Yii::$app->user->identity->username . ')', 'group' => 'Выйти'];
        }

        return $result;
    }

    function cmp($a, $b)
    {
        return -strnatcmp($a["priority"], $b["priority"]);
    }

}