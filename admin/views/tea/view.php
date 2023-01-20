<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use admin\components\widgets\AdminWidgetHelper;
use common\models\Collection;


/* @var $this yii\web\View */
/* @var $model common\models\Tea */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Чай', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tea-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            AdminWidgetHelper::getDropdownColumn(
                'collection_id',
                Collection::find()->select(['title', 'id'])->indexBy('id')->column(),
                false
            ),
            'subtitle:ntext',
            'description:ntext',
            'image_fon',
            'image_pack',
            'weight:ntext',
            'temperature_brewing:ntext',
            'time_brewing:ntext',
            'buy_button_flag',
            'url:ntext',
            'output_priority',
        ],
    ]) ?>

</div>
