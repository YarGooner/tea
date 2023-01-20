<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model admin\models\EmailSettings */

$this->title = Yii::t('app', 'Create Email Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Email Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-settings-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
