<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 30.10.2018
 * Time: 17:08
 */

namespace console\controllers;


use yii\console\Controller;
use Yii;
use yii\helpers\Console;

class LayoutMenuController extends Controller
{
        public function beforeAction($action)
    {
        if (Console::isRunningOnWindows()) {
            shell_exec('chcp 65001');
        }
        return parent::beforeAction($action);
    }

    public function actionItems($result = ''){
        $path = Yii::getAlias('@app').'\..\admin\views';
        $dirs = scandir($path);
        foreach ($dirs as $dir){
            if ($dir != '.' && $dir != '..' && $dir != 'layouts' && $dir != 'site'){
                    $result .= "['label' => '$dir', 'url' => ['/$dir']],\r\n";
            }
        }
        $result = " = [\r\n$result]";

        $file_name = $path.'\..\views\layouts\main.php';
        $file = file_get_contents($file_name);
        $config_pos_start = stripos($file,'$menuItems[] = [')+10;
        $config = substr($file,$config_pos_start);
        $config_pos_end = stripos($config,'];');
        $file = substr_replace($file, $result, $config_pos_start, $config_pos_end+1);
        if(file_put_contents($file_name, $file)){
            echo "Menu items had been configurated";
        };
//        var_dump($file);

    }
}