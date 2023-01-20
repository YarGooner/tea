<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 27.03.2019
 * Time: 19:33
 */

use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Testing');
$this->params['breadcrumbs'][] = $this->title;

$domain = \common\components\UserUrlManager::getDomainUrl();

// ------------------------------
// >>> EMAIL >>>
// get email templates
$files = FileHelper::findFiles(Yii::getAlias('@common').'\mail');
$templates = [];
foreach( $files as $file){
    $filename = basename($file);
    if( strpos($filename , '-html.php') !== false ) $templates[] = explode( '-html.php', $filename )[0];
//    echo $filename.' : '.strpos( $filename,'-html.php' ).'</br>';
}
//print_r( $templates );

$this->registerJs('
$(function(){
    var $form = $(".__email-test");
    var $output = $(".__output", $form);
    $form.submit(function(e){
        e.preventDefault();
        
//        console.log("send: ", $form.serialize());
        $output.text("Pending...");
        $.post( $form.attr("action"), $form.serialize(),
        function(data){
            console.log("send: ", data);
            $output.text(JSON.stringify(data));
        }
        ).fail( function(data){
            console.log("send: ", data);
            $output.text(JSON.stringify(data.responseJSON));
        });
    });
});', View::POS_READY );
// ------------------------------
?>
<div class="testing-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="well">
        <form class="form __email-test" action="<?= Url::to( '/admin/testing/send-email') ?>" method="post" >

            <h2>Отправка почты</h2>

            <div class="row">

                <div class="form-group col-md-6">
                    <label for="to">Кому:</label>
                    <input type="email" name="to" class="form-control" value="<?= $to ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="subject">Тема:</label>
                    <input type="text" name="subject" class="form-control" value="Тестовое письмо c сайта <?= $domain ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="template">Шаблон письма</label>
                    <select name="template" class="form-control" >
                        <?php foreach( $templates as $template): ?>
                        <option value="<?= $template ?>"><?= $template ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>

            </div>

            <div class="form-group">
                <input type="submit" value="Отправить" class="btn btn-primary" >
            </div>

            <label for="to">Результат:</label>
            <div class="well __output" ></div>

        </form>
    </div>

</div>
