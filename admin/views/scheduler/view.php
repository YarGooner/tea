<?php
/**
 * Task View
 *
 * @var yii\web\View $this
 * @var thamtech\scheduler\models\SchedulerTask $model
 */

use yii\helpers\Html;
use thamtech\scheduler\models\SchedulerTask;
use yii\bootstrap\Tabs;
use yii\grid\GridView;
use yii\widgets\DetailView;

Yii::$app->formatter->locale = 'ru-RU';

$this->title = $model->__toString();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Scheduler Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->__toString();
?>
<div class="task-view">

    <h1><?=$this->title ?></h1>

    <?php $this->beginBlock('main'); ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'display_name',
            'description',
            'schedule',
            [
                'attribute' => 'status',
                'label' => 'Статус',
            ],
            [
                'attribute' => 'started_at',
                'format' => 'raw',
                'value' => $model->status_id == SchedulerTask::STATUS_RUNNING ? $model->started_at : '',
            ],
            'last_run',
            'next_run',
        ],
    ]) ?>
    <?php $this->endBlock(); ?>



    <?php $this->beginBlock('logs'); ?>
    <div class="table-responsive">
        <?php \yii\widgets\Pjax::begin(['id' => 'logs']); ?>
        <?= GridView::widget([
            'layout' => '{summary}{pager}{items}{pager}',
            'dataProvider' => $logDataProvider,
            'pager' => [
                'class' => yii\widgets\LinkPager::className(),
                'firstPageLabel' => Yii::t('app', 'First'),
                'lastPageLabel' => Yii::t('app', 'Last'),
            ],
            'columns' => [
                [
                    'attribute' => 'started_at',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return Html::a(Yii::$app->getFormatter()->asDatetime($m->started_at, "php:d-m-Y H:i:s"), ['view-log', 'id' => $m->id]);
                    }
                ],
                [
                    'attribute' => 'ended_at',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return Yii::$app->getFormatter()->asDatetime($m->ended_at, "php:d-m-Y H:i:s");
                    }
                ],
                [
                    'label' => 'Длительность',
                    'value' => function ($m) {
                        return $m->getDuration();
                    }
                ],
                [
                    'label' => 'Результат',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function ($m) {
                        return Html::tag('span', '', [
                            'class' => $m->error == 0 ? 'text-success glyphicon glyphicon-ok-circle' : 'text-danger glyphicon glyphicon-remove-circle'
                        ]);
                    }
                ],
            ],
        ]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
    <?php $this->endBlock(); ?>

    <?= Tabs::widget([
        'encodeLabels' => false,
        'id' => 'customer',
        'items' => [
            'overview' => [
                'label'   => Yii::t('app', 'Overview'),
                'content' => $this->blocks['main'],
                'active'  => true,
            ],
            'logs' => [
                'label' => 'Логи',
                'content' => $this->blocks['logs'],
            ],
        ]
    ]);?>
</div>
