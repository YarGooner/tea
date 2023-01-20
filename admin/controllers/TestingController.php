<?php

namespace admin\controllers;

use common\models\Settings;
use common\modules\email\Emailer;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii2mod\editable\EditableAction;

class TestingController extends AdminController
{
    public function behaviors()
    {
        return array_merge( parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'send-email' => ['POST'],
                    ],
                ],
            ]
        );
    }


    //
    public function actionIndex(){

        return $this->render('index', [

        ]);
    }


    public function actionSendEmail(){

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = \Yii::$app->request->post();
        if( !$post ) return ['success' => false, 'message' => 'Data required' ];

        $to = $post['to'];
        if( !$to ) return ['success' => false, 'message' => 'Не указан email'];

        $subject = $post['subject'];
        $template = $post['template'];
        $send_result = Emailer::sendMail($to, $subject, $template, \Yii::$app->user );

        return [
            'success' => true,
            'data' => $send_result,
        ];
    }

}
