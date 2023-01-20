<?php

use admin\components\widgets\AdminWidgetHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model admin\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$username = $model->username ? $model->username : ( $model->userExt->email ? $model->userExt->email : $model->userExt->unconfirmed_email );
?>
<div class="user-view">

    <h1>#<?= Html::encode($this->title).'. '.$username ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
//            'password_hash',
            'auth_source',

            'created_at:datetime',
            'last_login_at:datetime',
//            'auth_key',
            //'password_reset_token',

            // ----------
            'userExt.first_name',
            'userExt.middle_name',
            'userExt.last_name',

            'userExt.phone',

            'userExt.unconfirmed_email',
            'userExt.email',
            'userExt.email_is_verified',
            'userExt.email_verified_at',

//            AdminWidgetHelper::getDictionaryItem( 'userExt.rules_accepted', 'yes-no'),
            'userExt.rules_accepted',

        ],
    ]) ?>

</div>
