<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 20.11.2018
 * Time: 16:54
 */

namespace console\cron\controllers;

use admin\models\Email;
use common\modules\email\Emailer;
use thamtech\scheduler\Task;

class EmailController extends Task
{
    public $description = 'Обеспечивает отправку писем подписчикам';

    public function run(){
        $emails = Email::find()->where(['subscribed' => 1])->all();
        $message = [];
        $message['html_layout'] = 'newVacancies-html.php';
        $message['text_layout'] = 'newVacancies-text.php';
        foreach ($emails as $email){
            Emailer::sendMail($email->value,'Новые вакансии!',$message);
        };
    }
}