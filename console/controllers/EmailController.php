<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 20.11.2018
 * Time: 16:54
 */

namespace console\controllers;


use yii\console\Controller;
use admin\models\Email;
use common\modules\email\Emailer;

class EmailController extends Controller
{
    public function actionSend(){
        $emails = Email::find()->all();
        $message = [];
        $message['html_layout'] = 'newVacancies-html.php';
        $message['text_layout'] = 'newVacancies-text.php';
        foreach ($emails as $email){
            Emailer::sendMail($email->value,'Новые вакансии!',$message);
        };
    }
}