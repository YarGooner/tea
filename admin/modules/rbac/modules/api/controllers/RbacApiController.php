<?php

namespace admin\modules\rbac\modules\api\controllers;

use yii\web\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class RbacApiController extends Controller
{
    public function behaviors()
    {
        $behaviors =array_merge(parent::behaviors(),[
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'text/html' => Response::FORMAT_JSON,
                ]
            ],
            'authentificator' => [
                'class' => HttpBearerAuth::className(),
            ],
        ]);
        return $behaviors;
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
