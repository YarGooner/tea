<?php
//\yii\base\Event::on(
//    \thamtech\scheduler\console\SchedulerController::className(),
//    \thamtech\scheduler\events\SchedulerEvent::EVENT_AFTER_RUN,
//    function ($event) {
//        if (!$event->success) {
//            foreach($event->exceptions as $exception) {
//                throw $exception;
//            }
//        }
//    }
//);

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
        'admin' => [
            'class' => 'console\controllers\UserAdminController',
        ],
//        'db-console' => [
//            'class' => 'dizews\dbConsole\DbController',
//        ],
        'menu' => [
            'class' => 'console\controllers\LayoutMenuController',
        ],
        'rbac' => [
            'class' => 'console\rbac\controllers\RbacController'
        ]
    ],
    'modules' => [
//        'scheduler' => [
//            'class' => 'thamtech\scheduler\Module',
//            'tasks' => [
//                'email' => [
//                    'class' => 'console\cron\controllers\EmailController',
//                    'displayName' => 'email-send',
////                    'schedule' => '* * * * *',
//                ],
//            ],
//        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'class' => '\common\components\UserUrlManager',
            'baseUrl' => '/api/v1',
            'hostInfo' => 'Invitro.loc',
            'root' => '/htdocs',
            'hideRoot' => true,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
//                '' => 'site/index',
            ],
        ],
    ],
    'params' => $params,
];
