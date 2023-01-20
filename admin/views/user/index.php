<?php

use admin\components\widgets\AdminWidgetHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use admin\components\arrayViewHelper\ArrayViewHelper;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel admin\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'username',
        'auth_source',

        'userExt.first_name',
        'userExt.middle_name',
        'userExt.last_name',

        'userExt.email',
        'userExt.email_is_verified',
        'userExt.email_verified_at',

        'last_login_at:datetime',
        'created_at:datetime',
//        ['class' => 'yii\grid\ActionColumn'],
    ];

    // Renders a export dropdown menu
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns
    ]);

    /*   // You can choose to render your own GridView separately
       echo \kartik\grid\GridView::widget([
           'dataProvider' => $dataProvider,
           'filterModel' => $searchModel,
           'columns' => $gridColumns
       ]);*/
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            AdminWidgetHelper::getFixedWidthColumn(),
            'username',
//            'password_hash',
            'auth_source',
//            'auth_key',
            //'password_reset_token',
//            'userExt.first_name',
//            'userExt.middle_name',
            'userExt.last_name',
//            'userExt.unconfirmed_email',
//            'userExt.email',
//            'userExt.unconfirmed_email',
            [
                'label' => 'Email',
                'value' => function($data){
                    return $data->userExt->email ? '<span style="color:green" title="'.Yii
                            ::t('app', 'Email is confirmed').'">'.$data->userExt->email.'</span>' : ('<span style="color:red" title="'.Yii
::t('app', 'Email is not confirmed').'">'.$data->userExt->unconfirmed_email.'</span>');
                },
                'format' => 'raw',
            ],
//            'userExt.email_is_verified',
//            'userExt.email_verified_at',

            /*
            [
                'attribute' => 'last_login_at',
                'value' => 'last_login_at',
                'format' => 'datetime',
//                'value' => function($data){
//                    if($data->last_login_at > 0) {
//                        return date('Y-m-d H:i:s', +$data->last_login_at);
//                    }
//                    return '';
//                },
                'filter'=>DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute' => 'last_login_at',
                        'pluginOptions'=> [
                            'timePicker'=>true,
//                            'locale'=>['format' => 'Y-MM-DD HH:mm:ss'],
//                              "opens"=>"left",
                        ],
                    ]). HTML::error($searchModel,'last_login_at') // TRICK
            ],
            */
            AdminWidgetHelper::getDataRangeItem( 'last_login_at', $searchModel ),
            AdminWidgetHelper::getDataRangeItem( 'created_at', $searchModel ),
//            'updated_at',
            //'status',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{delete}'
            ],
        ],
    ]); ?>
</div>
