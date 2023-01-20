<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 20.11.2018
 * Time: 16:54
 */

namespace console\components\cron\controllers;


use yii\console\Controller;
use admin\models\Email;
use common\modules\email\Emailer;
use thamtech\scheduler\Task;

class EmailController extends Task
{
    public $description = 'Sends an email to all subscribers';

    public function run(){
        $emails = Email::find()->all();
        $message = [];
        $message['html_layout'] = 'newVacancies-html.php';
        $message['text_layout'] = 'newVacancies-text.php';
        foreach ($emails as $email){
            Emailer::sendMail($email->value,'Новые вакансии!',$message);
        };
    }
}