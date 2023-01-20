<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii2mod\editable\EditableColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'parameter',
            [
                'class' => EditableColumn::class,
                'attribute' => 'value',
                'url' => ['change-value'],
            ],
            [
                'class' => EditableColumn::class,
                'attribute' => 'description',
                'url' => ['change-description'],
            ],
        ],
    ]); ?>
</div>
