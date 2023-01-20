<?php

use yii\helpers\Html;
use yii\grid\GridView;
use admin\components\arrayViewHelper\ArrayViewHelper;

/* @var $this yii\web\View */
/* @var $searchModel admin\models\UserAdminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'User Admins');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-admin-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<!---->
<!--    <p>-->
<!--        --><?//= Html::a(Yii::t('app', 'Create User Admin'), ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
//            'auth_key',
//            'password_hash',
//            'password_reset_token',
            'email:email',
            [
                'attribute' => 'status',
                'value' => function($data){
                    return ArrayViewHelper::returnValueArray('user-admin','status',$data->status);
                },
                'filter' => ArrayViewHelper::returnFilterArray('user-admin', 'status'),
            ],
            [
                'attribute' => 'created_at',
                'value' => function($data){
                    return date('Y-m-d H:i:s', $data->created_at);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($data){
                    return date('Y-m-d H:i:s', $data->updated_at);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{delete}'
            ],
        ],
    ]); ?>
</div>
