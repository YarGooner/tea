<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 22.01.2019
 * Time: 16:43
 */

namespace admin\modules\rbac\assets;

use yii\web\AssetBundle;

class RbacAssets extends AssetBundle
{
    public $sourcePath = '@app/modules/rbac/assets';
    public $css = [
        'css/styles.css',
    ];
    public $js = [
        'js/permission.js',
        'js/model-permission.js',
        'js/role-assign.js',
        'js/role-delete.js',
        'js/info-update.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}