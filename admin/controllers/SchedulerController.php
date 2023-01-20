<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 23.11.2018
 * Time: 15:10
 */

namespace admin\controllers;

use yii\web\Controller;
use thamtech\scheduler\models\SchedulerTask;
use thamtech\scheduler\models\SchedulerLog;
use Yii;


class SchedulerController extends Controller
{
//    public function actions()
//    {
//        return [
//            'index' => [
//                'class' => 'thamtech\scheduler\actions\IndexAction',
//                'view' => '@scheduler/views/index',
//            ],
//            'view' => [
//                'class' => 'thamtech\scheduler\actions\ViewAction',
//                'view' => '@scheduler/views/view',
//            ],
//            'view-log' => [
//                'class' => 'thamtech\scheduler\actions\ViewLogAction',
//                'view' => '@scheduler/views/view-log',
//            ],
//        ];
//    }

    public function actionIndex(){
        $model  = new SchedulerTask();
        $dataProvider = $model->search($_GET);

        $logModel = new SchedulerLog();
//        $logModel->scheduler_task_id = $model->id;
        $logDataProvider = $logModel->search($_GET);

        return $this->render('index' ?: $this->id, [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionView($id){
        $model = SchedulerTask::findOne($id);
        $request = Yii::$app->getRequest();

        if (!$model) {
            throw new \yii\web\HttpException(404, 'The requested page does not exist.');
        }

        if ($model->load($request->post())) {
            $model->save();
        }

        $logModel = new SchedulerLog();
        $logModel->scheduler_task_id = $model->id;
        $logDataProvider = $logModel->search($_GET);

        return $this->render('view' ?: $this->id, [
            'model' => $model,
            'logModel' => $logModel,
            'logDataProvider' => $logDataProvider,
        ]);
    }

    public function actionViewLog($id){
        $model = SchedulerLog::findOne($id);

        if (!$model) {
            throw new \yii\web\HttpException(404, 'The requested page does not exist.');
        }

        return $this->render('view-log' ?: $this->id, [
            'model' => $model,
        ]);
    }
}