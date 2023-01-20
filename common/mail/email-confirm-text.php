<?php
use common\components\UserUrlManager;

$domain_name = Yii::$app->request->serverName;
$domain = UserUrlManager::getDomainUrl();
$user_name = null;
$confirmLink = null;
if( $user ) {
    $user_name = $user->username;
    $confirmLink = $domain . '/api/v1/user/email-confirm?token=' . $user->userExt->email_confirm_token;
}
?>

Здравствуйте, <?= $user_name ?>
поздравляем с успешной регистрацией на сайте $domain_name
Для завершения регистрации на сайте перейдите по ссылке:
<?= $confirmLink ?>