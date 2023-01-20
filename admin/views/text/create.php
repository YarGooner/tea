<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model admin\models\Text */

$this->title = Yii::t('app', 'Create Text');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Texts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
