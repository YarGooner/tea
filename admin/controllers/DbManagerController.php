<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 13.12.2018
 * Time: 9:58
 */

namespace admin\controllers;

use admin\models\DbManager;
use Yii;
use yii\web\Controller;

class DbManagerController extends Controller
{
    public function actionIndex(){
        $model = new DbManager();

        if ($model->load(Yii::$app->request->post())) {

            Yii::$app->consoleRunner->run('controller/action param1 param2 ...');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }
}