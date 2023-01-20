<?php

/* @var $this \yii\web\View */
/* @var $content string */

use admin\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\widgets\Menu;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->name ?> | Панель администратора</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default navbar-fixed-top',
//            'id' => 'main-menu',
        ],
    ]);

    $menuItems = [];

    if (!Yii::$app->user->isGuest) {
        $menuItems = [
           // ['label' => 'Пользователи', 'url' => ['/user']],
            //['label' => 'Тексты', 'url' => ['/text']],
            ['label' => 'Коллекции', 'url' => ['/collection']],
            ['label' => 'Чай', 'url' => ['/tea']],
            ['label' => 'Новости', 'url' => ['/news']],
            ['label' => 'Обратная связь', 'url' => ['/feedback']],
            [ 'label' => 'Управление', 'items' => [
                ['label' => 'Настройки', 'url' => ['/settings']],
                ['label' => 'Администраторы', 'url' => ['/user-admin']],
                ['label' => 'Тестирование', 'url' => ['/testing']],
                ['label' => 'Информация о хостинге', 'url' => ['/site/info']],
            ]],
            '<li class="divider-vertical"></li>',
        ];
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Выйти (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    } else {
        $menuItems = [
            ['label' => 'Войти', 'url' => ['/site/login']],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();

    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>



<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

