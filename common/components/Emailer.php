<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 20.11.2018
 * Time: 11:48
 */
namespace common\components;

use Yii;
use common\models\Settings;

class Emailer
{
    /**
     * @param $to
     * @param $subject
     * @param $message
     * @param null $user
     * @param null $data
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function sendMail($to, $subject, $message = null, $user = null, $data = null, $from = null){

        if( !$message ) return ['error' => [ 'message' => 'An message template required.'] ];

        static $emailer_data;

        if( !$emailer_data ) {
            $settings = Settings::find();
            $emailer_data['host'] = $settings->where(['parameter' => 'email_server'])->one()->value;
            $emailer_data['username'] = $settings->where(['parameter' => 'email_username'])->one()->value;
            $emailer_data['password'] = $settings->where(['parameter' => 'email_password'])->one()->value;
            $emailer_data['port'] = $settings->where(['parameter' => 'email_port'])->one()->value;
            $emailer_data['from'] = $settings->where(['parameter' => 'email_from'])->one()->value;
        }

        if($from == null) $from = $emailer_data['from'];

        $mailer_params = [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'enableSwiftMailerLogging' => true,
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' =>  $emailer_data['host'],
                'username' => $emailer_data['username'],
                'password' => $emailer_data['password'],
                'port' => $emailer_data['port'],
                'encryption' => 'ssl',
//                'streamOptions' => [
//                    'ssl' => [
//                        'allow_self_signed' => true,
//                        'verify_peer' => false,
//                        'verify_peer_name' => false,
//                    ],
//                ]
            ],
        ];
        $mailer = Yii::createObject($mailer_params);

        if(!$data) $data = [];

        $mailer->getView()->params['data'] = $data;

        if( is_string($message) ){
            $message = [
                'html_layout' => $message.'-html.php',
                'text_layout' => $message.'-text.php',
            ];
        }

        $send = $mailer->compose(
            ['html' => $message['html_layout'], 'text' => $message['text_layout']],
            ['user' => $user ]
        );

        $status = $send->setTo($to)
            ->setFrom([$from])
            ->setSubject($subject.' ')
            ->send();
        $mailer->getView()->params['data'] = null;
        return $status;
    }
}