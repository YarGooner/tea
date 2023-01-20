<?php

use yii\helpers\Url;
use kartik\datecontrol\Module;
use yii\web\Request;
use common\components\UserUrlManager;

$baseUrl = str_replace('/admin', '', UserUrlManager::getDomainUrl('@admin'));
$module = '/admin';

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-admin',
    'name' => 'PROJECT NAME',
    'homeUrl' => $baseUrl.$module,
    'basePath' => dirname(__DIR__),
	//    'defaultRoute' => '/site/index',
    'sourceLanguage' => 'en-US',
    'language' => 'ru-RU',
	
    'controllerNamespace' => 'admin\controllers',
    
	'aliases' => [
        '@images' => UserUrlManager::UPLOADS,
    ],
	
	'bootstrap' => ['log'],
	  
	'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\PathController',
            'access' => ['@'],
            'root' => [
                'baseUrl'=> '',
                'basePath'=> '@uploads',
                'path' => '',
                'name' => 'Global'
            ],
        ],
        'assignment' => [
            'class' => 'yii2mod\rbac\controllers\AssignmentController',
            'userIdentityClass' => 'common\modules\auth\models\User',
        ],
    ],
	
    'modules' => [
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module',
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd-MM-yyyy',
                Module::FORMAT_TIME => 'hh:mm:ss a',
                Module::FORMAT_DATETIME => 'php:Y-m-d H-m-s',
            ],
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
                Module::FORMAT_TIME => 'php:U',
                Module::FORMAT_DATETIME => 'php:Y-m-d H-m-s',
            ],
            // set your display timezone
            'displayTimezone' => 'Europe/Moscow',

            // set your timezone for date saved to db
            'saveTimezone' => 'Europe/Moscow',
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module',
            // other module settings
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*']
        ],
//        'rbac' => [
//            'class' => 'admin\modules\rbac\Rbac',
//            'userModel' => 'common\modules\auth\models\User'
//        ],
    ],

    'components' => [

        'request' => [
            'csrfParam' => '_csrf-admin',
            'enableCsrfValidation' => false,
//            'baseUrl' => '/admin',
            'class' => 'common\components\Request',
            'web'=> $module,
//            'web'=> Utils::$ROOT_DOMAIN.$module,

            'adminUrl' => $module,
//            'enableCsrfValidation' => !true,
//            'enableCookieValidation' => !true,
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => $baseUrl,
            ],
//            'identityCookie' => [
//                'path' => $baseUrl,
//            ]
        ],

        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app'       => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'yii2mod.rbac' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/rbac/messages',
                ],
            ],
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ],

        // 'consoleRunner' => [
            // 'class' => 'admin\components\consoleRunner\ConsoleRunner',
            // 'file' => '@root/yii', // or an absolute path to console file
            // 'phpBinaryPath' => PHP_BINDIR . '/php',
        // ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],

        'user' => [
            'identityClass' => 'admin\models\AdminUser',
            'enableSession' => true,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-admin', 'httpOnly' => true],
        ],

        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
        ],

        'session' => [
            // this is the name of the session cookie used for login on the admin
            'name' => 'advanced-admin',
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'RUB',
            'dateFormat' => 'php: d/m/Y',
            'datetimeFormat' => 'php: d/m/Y H:i',
        ],

        'urlManager' => [
            'class' => '\common\components\UserUrlManager',
            'root' => '/htdocs',
            'hideRoot' => true,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
//                '/' => 'site/index',
            ],
        ],
    ],
    'params' => $params,
];
