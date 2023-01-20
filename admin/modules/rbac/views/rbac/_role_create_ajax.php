<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model admin\models\Role */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Pjax::begin(['enablePushState' => false]); ?>

<div class="role-form">

    <?php $form = ActiveForm::begin([
            'action' => 'rbac/save-role',
            'id' => 'role-form',
            'options' => [
                'data-pjax' => true,
            ],
    ]); ?>
<div class="row">
    <div class="col-xs-3">
    <?= $form->field($roleModel, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-3">
    <?= $form->field($roleModel, 'description')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-3">
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success role-ajax']) ?>
        </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>

</div>

<?php Pjax::end(); ?>