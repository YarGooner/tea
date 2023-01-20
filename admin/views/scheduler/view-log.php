<?php
/**
 * Log Entry view
 *
 * @var yii\web\View $this
 * @var thamtech\scheduler\models\SchedulerLog $model
 */

use yii\helpers\Html;
use thamtech\scheduler\models\SchedulerTask;


$this->title = Yii::$app->getFormatter()->asDatetime($model->started_at, "php:d-m-Y H:i:s");
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Scheduler Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->schedulerTask->__toString(), 'url' => ['view', 'id' => $model->scheduler_task_id]];
$this->params['breadcrumbs'][] = Yii::$app->getFormatter()->asDatetime($model->started_at, "php:d-m-Y H:i:s");
?>

<div class="">

    <h1><?=$this->title ?></h1>

    <div class="well">
        <dl class="dl-horizontal">
            <dt>Описание</dt>
            <dd><?= Html::encode($model->schedulerTask->description) ?></dd>

            <dt><?= $model->getAttributeLabel('started_at') ?></dt>
            <dd><?= Yii::$app->formatter->asDatetime($model->started_at) ?></dd>

            <dt><?= $model->getAttributeLabel('ended_at') ?></dt>
            <dd><?= Yii::$app->formatter->asDatetime($model->ended_at) ?></dd>

            <dt>Продолжительность</dt>
            <dd><?= $model->getDuration() ?></dd>

            <dt>Результат</dt>
            <dd>
                <?php if ($model->error): ?>
                    <span class="text-danger glyphicon glyphicon-remove-circle"></span> Error
                <?php else: ?>
                    <span class="text-success glyphicon glyphicon-ok-circle"></span> Success
                <?php endif ?>
            </dd>
        </dl>

        <h3>Вывод</h3>
        <textarea class="form-control" rows="7"><?= $model->output ?></textarea>
    </div>
</div>
