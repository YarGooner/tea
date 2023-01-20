<?php
$public = '/htdocs';

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@admin', dirname(dirname(__DIR__)) . '/admin');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@public', dirname(dirname(__DIR__)) . $public);
Yii::setAlias('@uploads', dirname(dirname(__DIR__)) . $public . \common\components\UserUrlManager::UPLOADS);
//Yii::setAlias('@scheduler', dirname(dirname(__DIR__)) . '/vendor/thamtech/yii2-scheduler/src');
Yii::setAlias('@root', dirname(dirname(__DIR__)));
