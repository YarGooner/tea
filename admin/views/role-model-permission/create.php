<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model admin\modules\rbac\models\RoleModelPermission */

$this->title = Yii::t('app', 'Create Role Model Permission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Role Model Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-model-permission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
