<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use admin\modules\rbac\models\Role;
use common\modules\auth\models\User;

/* @var $this yii\web\View */
/* @var $model admin\models\RoleAssign */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="role-assign-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'role_id')->dropDownList(ArrayHelper::map(Role::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(),'id','username')) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
