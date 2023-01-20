<?php
use yii\helpers\Html;
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

<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table id="Table_02" width="640" border="0" cellpadding="0" cellspacing="0" style="margin: auto; display: block;">
    <tr width="640" height="15" style="display: block; background-color: #e4e4e4;">
        <td colspan="3"></td>
    </tr>
    <tr>
        <td width="15" height="133" style="display: inline-block; background-color: #e4e4e4;"></td>
        <td width="610" height="133" style="display: inline-block; background-color: #fffff;">
            <!-- <img src="<?= $domain ?>/images/mail/doll.jpg" width="610" height="133" style="display: block;"> -->
        </td>
        <td width="15" height="133" style="display: inline-block; background-color: #e4e4e4;"></td>
    </tr>
    <tr>
        <td width="15" height="366" style="display: inline-block; background-color: #e4e4e4;"></td>
        <td width="510" height="366" style="display: inline-block; background-color: #fffff; padding: 0px 40px 0 60px">
            <p style="  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: left; font-size: 16px; line-height: 22px; color: #5c646e; margin: 30px 0 28px; letter-spacing: -0.2px;">
                Здравствуйте, <?= $user_name ?>
            </p>
            <p style="  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: left; font-size: 16px; line-height: 22px; color: #5c646e; margin: 30px 0 28px; letter-spacing: -0.2px;">
                Поздравляем Вас с успешной регистрацией на сайте <?= $domain_name ?>.
            </p>
            <p style="  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; text-align: left; font-size: 16px; line-height: 22px; color: #5c646e; margin: 0px 0px 12px; letter-spacing: -0.2px;">
                Для завершения регистрации перейдите по ссылке <a target="_blank" href="<?= $confirmLink ?>" style=""><?= $confirmLink ?></a>
            </p>

        </td>
        <td width="15" height="366" style="display: inline-block; background-color: #e4e4e4;"></td>
    </tr>
    <tr width="640" height="15" style="display: block; background-color: #e4e4e4;">
        <td colspan="3"></td>
    </tr>
</table>
</body>
</html>