<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $model common\models\Tea */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tea-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'collection_id')->dropDownList(
        ArrayHelper::map(\common\models\Collection::find()->all(), 'id', 'title')
    ) ?>

    <?= $form->field($model, 'subtitle')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className(),[
        'editorOptions' => [
            'preset' => 'full',
        ],
    ]); ?>

    <?= $form->field($model, 'image_fon')->widget(InputFile::class) ?>

    <?= $form->field($model, 'image_pack')->widget( InputFile::class) ?>

    <?= $form->field($model, 'weight')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'temperature_brewing')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'time_brewing')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'buy_button_flag')->checkbox() ?>

    <?= $form->field($model, 'url')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'output_priority')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
