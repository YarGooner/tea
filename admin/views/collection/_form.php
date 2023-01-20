<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\elfinder\InputFile;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model common\models\Collection */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="collection-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subtitle')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hovercolor')->widget(ColorInput::class) ?>

    <?php /*= $form->field($model, 'image')->textInput(['maxlength' => true]) */?>

    <?= $form->field($model, 'image')->widget(InputFile::class) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
