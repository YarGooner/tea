<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить Новости', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title',
            //'output_priority',
            [
                    'attribute'=>'date',
                    'format'=>['date', 'php:Y-m-d']
                    ],
            'description:ntext',
            //'text:ntext',
            //'image',
            \admin\components\widgets\AdminWidgetHelper::getDictionaryItem('status', 'news-status'),

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
