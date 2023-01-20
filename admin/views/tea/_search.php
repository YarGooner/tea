<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tea-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'collection_id') ?>

    <?= $form->field($model, 'subtitle') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'image_fon') ?>

    <?php // echo $form->field($model, 'image_pack') ?>

    <?php // echo $form->field($model, 'weight') ?>

    <?php // echo $form->field($model, 'temperature_brewing') ?>

    <?php // echo $form->field($model, 'time_brewing') ?>

    <?php // echo $form->field($model, 'buy_button_flag') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'output_priority') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
