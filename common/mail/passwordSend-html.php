<?php
use yii\helpers\Html;
use common\components\UserUrlManager;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$password = $this->params['data']['password'];

?>
<div class="password-reset">

    <p>Ваш новый пароль:</p>
    <p><?= $password ?></p>
</div>
