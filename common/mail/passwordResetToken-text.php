<?php
use common\components\UserUrlManager;

$domain_name = Yii::$app->request->serverName;
$domain = UserUrlManager::getDomainUrl();
if( $user ) {
    $user_name = $user->username ;
    $resetLink = $domain . '/?reset_password_token=' . $user->password_reset_token;
}
?>
Здравствуйте, <?= $user_name ?>!

Вы получили это письмо, так как нам поступил запрос на 
восстановление Вашего пароля на сайте <?= $domain_name ?>.

Для обновления пароля, пожалуйста, перейдите по 
ссылке: <?= $resetLink ?>

Если Вы не обращались к процедуре восстановления
пароля, просто проигнорируйте данное письмо. 
Ваш пароль не будет изменен.
