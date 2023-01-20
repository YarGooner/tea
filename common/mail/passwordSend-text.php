<?php

use common\components\UserUrlManager;
/* @var $this yii\web\View */
/* @var $user common\models\User */

$password = $this->params['data']['password'];

?>
Ваш новый пароль:

<?= $password ?>
