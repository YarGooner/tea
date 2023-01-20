<?php

use common\components\Utils;
use yii\web\Request;
use admin\modules\rbac\filters\RbacActionFilter;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

//Utils::$ROOT_DOMAIN = $params['rootDomain'];
//$baseUrl = Utils::getBaseUrl();
$current_url = (new Request)->absoluteUrl;
$htdocs_pos = strpos($current_url,'/htdocs');
if(!$htdocs_pos){
    $htdocs_pos = stripos($current_url,'/admin');
}
$baseUrl = substr($current_url,0,$htdocs_pos);
$module = '/api';

return [
    'id' => 'app-api',
    'homeUrl' => $baseUrl.$module,
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'controllerNamespace' => 'app\controllers',
//    'on beforeAction' => function($event){
//        $filter = new RbacActionFilter();
//        return $filter->checkAccess();
//    },
    'aliases' => [
        '@images' => '/uploads/global/',
    ],
//    'defaultRoute' => 'site/index',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
            'controllerMap' => [
                'dev-info' => [
                    'class' => 'api\components\devInfo\controllers\DevInfoController',
                    'enableCsrfValidation' => false,
                    'token' => 'b5RWkA>Kx$Kt)R+ZsYe_',
                    'controllersPath' => '../../api/modules/v1/controllers',
                    'defaultControllers' => [
                        'app',
                        'default',
                        'dev-info',
                        'site',
                        'captcha'
                    ],
                ],
//                'rbac' => [
//                    'class' => 'admin\modules\rbac\controllers\ApiController'
//                ]
            ],
        ],
//        'auth' => [
//            'class' => 'common\modules\auth\Module',
//        ],
//        'rbac' => [
//            'class' => 'admin\modules\rbac\Rbac',
//            'userModel' => 'common\modules\auth\models\User',
//            'unauthorizedActions' => [
//                'site' => ['*'],
//                'user' => ['login']
//            ],
//        ],
//        'rbac-api' => [
//            'class' => 'admin\modules\rbac\modules\api\Module',
//        ],
    ],
    'defaultRoute' => '/api',

    'components' => [

        'request' => [
            'csrfParam' => '_csrf-api',
            'baseUrl' => '/api',
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],

        'user' => [
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true, 'path' => '/'],
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

        'session' => [
            'name' => 'advanced-api',
        ],

        'response' => [
            // ...
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
            'on beforeSend' => function ($event) {

                $response = $event->sender;

                if( !is_string($response->data) ) {

                    $response_data = [
                        'success' => $response->isSuccessful,
                    ];

                    // translate messages
                    if( isset( $response->data['message'] ) ) {
                        $message = $response->data['message'];
                        $response->data['message'] = is_string($message) ? Yii::t('app', $response->data['message']) : $message;
                    }

                    // send original response data
                    /*if ($response->data) {
                        $response_data['data'] = isset( $response->data['data'] ) ? $response->data['data'] : $response->data;
                        $response_data['data']['status'] = Yii::$app->response->statusCode;
                    }

                    $response->data = $response_data;*/

                }

                // Suppress OK status
                if( $response->isSuccessful ) $response->statusCode = 200;

//                $response->data = gettype( $response->data );

                /*
                if ($response->data !== null && Yii::$app->request->get('suppress_response_code')) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    $response->statusCode = 200;
                }
                */
            },
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
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
            'errorAction' => 'v1/site/error',
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

//                '<_m:[\w-]+>/error' => '<_m>/site/error',                // v1/dev-info
//                '<_m:[\w-]+>/dev-info' => '<_m>/site/dev-info',                // v1/dev-info
//                '<_m:[\w-]+>/<_c:[\w-]+>/<id:\d+>' => '<_m>/<_c>/index',                // v1/user/1
//                '<_m:[\w-]+>/<_c:[\w-]+>' => '<_m>/<_c>/index',                         // v1/user
//                '<_m:[\w-]+>/<_c:[\w-]+>/<_a:[\w-]+>' => '<_m>/<_c>/<_a>',              // v1/user/login
//                '<_m:[\w-]+>/<_c:[\w-]+>/<id:\d+>/<_a:[\w-]+>' => '<_m>/<_c>/<_a>',     // v1/user/1/delete

//               ['class' => 'yii\rest\UrlRule',
//                   'controller' => [
//                       'v1/user',
//                       'v1/test',
//                       'v1/post',
//                       'v1/rbac'
//                    ]
//               ],
            ],
        ],

    ],
    'params' => $params,
];
