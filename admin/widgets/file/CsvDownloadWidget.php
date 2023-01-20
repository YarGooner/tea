<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 21.01.2019
 * Time: 9:57
 */

namespace admin\widgets\file;

use yii\jui\Widget;
use yii\helpers\Html;

class CsvDownloadWidget extends Widget
{
    public $downloadLink;

    public $linkText;

    public $linkParams;

    public function init(){

    }

    public function run(){
        $innerCode = "<div class=\"col-xs-6\">" .
                    Html::beginForm('email/download','post') .
                    Html::a('Скачать список',
                        [$this->downloadLink], $this->linkParams) .
                    Html::endForm() .
                    "</div>";

        return $innerCode;
    }
}