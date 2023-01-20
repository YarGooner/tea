<?php

use admin\components\widgets\AdminWidgetHelper;
use common\models\Collection;
use yii\helpers\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel common\models\TeaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Чай';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tea-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать Чай', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title',
            AdminWidgetHelper::getDropdownColumn(
                'collection_id',
                Collection::find()->select(['title', 'id'])->indexBy('id')->column(),
                false
            ),
            //'subtitle:ntext',
            'description:ntext',
            //'image_fon',
            //'image_pack',
            //'weight:ntext',
            //'temperature_brewing:ntext',
            //'time_brewing:ntext',
            //'buy_button_flag',
            //'url:ntext',
            //'output_priority',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
