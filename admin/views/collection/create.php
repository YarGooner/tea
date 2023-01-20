<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Collection */

$this->title = 'Создание Коллекции';
$this->params['breadcrumbs'][] = ['label' => 'Коллекции', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="collection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
