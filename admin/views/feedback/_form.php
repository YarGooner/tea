<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Feedback */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="feedback-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->addAriaAttributes = false ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->addAriaAttributes = false ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6])->addAriaAttributes = false ?>

    <?= $form->field($model, 'moderation_status')->dropDownList(\common\components\Dictionary::getList('moderation-status',true)) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
