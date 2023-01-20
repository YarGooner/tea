<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Tea */

$this->title = 'Создание Чая';
$this->params['breadcrumbs'][] = ['label' => 'Чай', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tea-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
