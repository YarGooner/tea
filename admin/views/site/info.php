<?php

/* @var $this yii\web\View */

use common\components\FileHelper;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ListView;

$this->title = 'My Yii Application';
?>


<?php
$root_prefix = dirname(__FILE__).'/../../../';
$frameworkPath =  $root_prefix.'vendor/yiisoft/yii2';


$api_config = ArrayHelper::merge(
//    require $root_prefix.'common/config/main.php',
//    require $root_prefix.'common/config/main-local.php',
//    require $root_prefix.'api/config/main.php',
//    require $root_prefix.'api/config/main-local.php'
    require $root_prefix.'api/config/params.php',
    require $root_prefix.'api/config/params-local.php'
);

$getParam = function ( $param, $target = null ) use( $api_config ) {
    if( !$target ) return ArrayHelper::getValue( Yii::$app->params, $param );
    if( $target == 'api' ) return ArrayHelper::getValue( $api_config, $param );
};

//var_dump( $api_config )
?>

<div class="site-index">
<div class="container">

    <div class="panel panel-default">
        <div class="panel-heading">
            <a class="h1 text-center" data-toggle="" href="#yii_body" >App Checkup</a>
        </div>
        <div class="panel-body">
        <?php

        $data = [
            [
                'key' => 'phpversion',
                'currentValue' => PHP_VERSION,
                'requiredValue' => $getParam('required-php-version' ),
                'description' => '',
                'check' => (function() use($getParam) {
                    $required = $getParam('required-php-version');
                    if (!$required) return true;
                    return version_compare(PHP_VERSION, $required);
                })()
            ],
            [
                'key'=>'upload_max_filesize',
                'currentValue'=> ini_get('upload_max_filesize'),
                'requiredValue' => FileHelper::bytesToString( $getParam('upload-max-size' ) ),
                'ApiRequiredValue' => FileHelper::bytesToString( $getParam('upload-max-size', 'api' ) ),
                'description' => 'Проверить не предполагает ли функционал загрузку файлов большего веса, чем указано тут. Править в php.ini',
                'check' => (function() use($getParam) {
                    $current = FileHelper::stringToBytes( ini_get('upload_max_filesize') );
                    $required = $getParam('upload-max-size', 'api' );
                    if (!$required) return true;
                    return $required <= $current;
                })()

            ],
        ];
        $gridViewDataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
    //        'sort' => [
    //            'attributes' => ['key','currentValue'],
    //        ],
            'pagination' => ['pageSize' => 10]
        ]);

        echo GridView::widget([
            'dataProvider' => $gridViewDataProvider,
            'columns' => [
                [
                    'attribute' => 'check',
                    'value' => function($data){
                        if( $data['check'] ) return "<div class='label label-success text-center' style='display:inline-block;width:100%;'>OK</div>";
                        return "<div class='label label-danger text-center' style='display:inline-block;width:100%;'>ERROR</div>";
                    },
                    'format' => 'raw',
                ],
                'key', 'currentValue', 'requiredValue', 'ApiRequiredValue', 'description',
            ]
        ]);
        ?>
        </div>
    </div>




<!--    -->


    <div class="panel panel-default">
        <div class="panel-heading">
            <a class="h1 text-center" data-toggle="collapse" href="#yii_body" >Yii2 Checkup</a>
        </div>
        <div id="yii_body" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="container __yii" style="width: 100%;position: relative">
    <?php
//

    // !!!
    //    function checkDir( $dir, $name = null ) {
    //        echo ($name ? $name : $dir) .': '. (is_dir($root_prefix.$dir) ? 'dir exists' : 'dir NOT exists');
    //        echo '['.$root_prefix.$dir.']'.'<br/>';
    //    }
    //
//    echo $frameworkPath;
//    echo '<br/>';
//    checkDir( 'vendor/yiisoft/yii2' );
//    checkDir( 'requirements' );
//    die();
    // !!!

    if (!is_dir($frameworkPath)) {
        echo '<h1>Error</h1>';
        echo '<p><strong>The path to yii framework seems to be incorrect.</strong></p>';
//        echo '<p>You need to install Yii framework via composer or adjust the framework path in file <abbr title="' . __FILE__ . '">' . basename(__FILE__) . '</abbr>.</p>';
//        echo '<p>Please refer to the <abbr title="' . dirname(__FILE__) . '/README.md">README</abbr> on how to install Yii.</p>';
    }

    require_once $root_prefix.'requirements/RequirementChecker.php';
    $requirementsChecker = new RequirementChecker();

    $gdMemo = $imagickMemo = 'Either GD PHP extension with FreeType support or ImageMagick PHP extension with PNG support is required for image CAPTCHA.';
    $gdOK = $imagickOK = false;

    if (extension_loaded('imagick')) {
        $imagick = new Imagick();
        $imagickFormats = $imagick->queryFormats('PNG');
        if (in_array('PNG', $imagickFormats)) {
            $imagickOK = true;
        } else {
            $imagickMemo = 'Imagick extension should be installed with PNG support in order to be used for image CAPTCHA.';
        }
    }

    if (extension_loaded('gd')) {
        $gdInfo = gd_info();
        if (!empty($gdInfo['FreeType Support'])) {
            $gdOK = true;
        } else {
            $gdMemo = 'GD extension should be installed with FreeType support in order to be used for image CAPTCHA.';
        }
    }

    /**
     * Adjust requirements according to your application specifics.
     */
    $requirements = array(
        // Database :
        array(
            'name' => 'PDO extension',
            'mandatory' => true,
            'condition' => extension_loaded('pdo'),
            'by' => 'All DB-related classes',
        ),
        array(
            'name' => 'PDO SQLite extension',
            'mandatory' => false,
            'condition' => extension_loaded('pdo_sqlite'),
            'by' => 'All DB-related classes',
            'memo' => 'Required for SQLite database.',
        ),
        array(
            'name' => 'PDO MySQL extension',
            'mandatory' => false,
            'condition' => extension_loaded('pdo_mysql'),
            'by' => 'All DB-related classes',
            'memo' => 'Required for MySQL database.',
        ),
        array(
            'name' => 'PDO PostgreSQL extension',
            'mandatory' => false,
            'condition' => extension_loaded('pdo_pgsql'),
            'by' => 'All DB-related classes',
            'memo' => 'Required for PostgreSQL database.',
        ),
        // Cache :
        array(
            'name' => 'Memcache extension',
            'mandatory' => false,
            'condition' => extension_loaded('memcache') || extension_loaded('memcached'),
            'by' => '<a href="http://www.yiiframework.com/doc-2.0/yii-caching-memcache.html">MemCache</a>',
            'memo' => extension_loaded('memcached') ? 'To use memcached set <a href="http://www.yiiframework.com/doc-2.0/yii-caching-memcache.html#$useMemcached-detail">MemCache::useMemcached</a> to <code>true</code>.' : ''
        ),
        array(
            'name' => 'APC extension',
            'mandatory' => false,
            'condition' => extension_loaded('apc'),
            'by' => '<a href="http://www.yiiframework.com/doc-2.0/yii-caching-apccache.html">ApcCache</a>',
        ),
        // CAPTCHA:
        array(
            'name' => 'GD PHP extension with FreeType support',
            'mandatory' => false,
            'condition' => $gdOK,
            'by' => '<a href="http://www.yiiframework.com/doc-2.0/yii-captcha-captcha.html">Captcha</a>',
            'memo' => $gdMemo,
        ),
        array(
            'name' => 'ImageMagick PHP extension with PNG support',
            'mandatory' => false,
            'condition' => $imagickOK,
            'by' => '<a href="http://www.yiiframework.com/doc-2.0/yii-captcha-captcha.html">Captcha</a>',
            'memo' => $imagickMemo,
        ),
        // PHP ini :
        'phpExposePhp' => array(
            'name' => 'Expose PHP',
            'mandatory' => false,
            'condition' => $requirementsChecker->checkPhpIniOff("expose_php"),
            'by' => 'Security reasons',
            'memo' => '"expose_php" should be disabled at php.ini',
        ),
        'phpAllowUrlInclude' => array(
            'name' => 'PHP allow url include',
            'mandatory' => false,
            'condition' => $requirementsChecker->checkPhpIniOff("allow_url_include"),
            'by' => 'Security reasons',
            'memo' => '"allow_url_include" should be disabled at php.ini',
        ),
        'phpSmtp' => array(
            'name' => 'PHP mail SMTP',
            'mandatory' => false,
            'condition' => strlen(ini_get('SMTP')) > 0,
            'by' => 'Email sending',
            'memo' => 'PHP mail SMTP server required',
        ),
    );
    $requirementsChecker->checkYii()->check($requirements)->render();
    ?>
                </div>
            </div>
        </div>
    </div>



    <div class="panel panel-default">
        <div class="panel-heading">
            <a class="h1 text-center" data-toggle="collapse" href="#phpini_body" >PHPinfo</a>
        </div>
        <div id="phpini_body" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="container __yii" style="width: 100%;position: relative">

                    <p><?= phpinfo() ?></p>

                </div>
            </div>
        </div>
    </div>

</div>
</div>




<style>
    .__yii>.container{
        width: 100%;
    }
    a, a:link{
        color: inherit !important;
        text-decoration:none;
        background: inherit;
    }

    a:hover {
        /*background-color:#0079C1;*/
        color: inherit;
    }

    a{
        cursor:pointer;
    }
</style>