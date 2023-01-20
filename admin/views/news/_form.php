<?php

use kartik\datecontrol\DateControl;
use kartik\datecontrol\Module;
use mihaildev\elfinder\InputFile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'output_priority')->textInput() ?>

    <?= $form->field($model, 'date')->widget(DateControl::class) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 8]) ?>

    <?= $form->field($model, 'image')->widget(InputFile::class) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\components\Dictionary::getList('news-status', true)) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
