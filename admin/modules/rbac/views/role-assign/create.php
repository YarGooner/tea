<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model admin\models\RoleAssign */

$this->title = Yii::t('app', 'Create Role Assign');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Role Assigns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-assign-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
