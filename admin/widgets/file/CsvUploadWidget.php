<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 21.01.2019
 * Time: 15:11
 */

namespace admin\widgets\file;

use yii\base\Widget;
use yii\widgets\ActiveForm;

class CsvUploadWidget extends Widget
{
    public $model;

    public $attribute = 'textFile';

    public function run(){

    }

    public function init()
    {
        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
        $content = "<div class=\"col-xs-3\">
                        <button onclick=\"showInput()\" class=\"btn btn-success\">Загрузить коды</button>
                        <div class=\"hide\"  id=\"upload\">" . $form .

                             $form->field($this->model, $this->attribute)->fileInput(['class' => 'codes-input']) .

                            "<button>Загрузить</button>" . ActiveForm::end() .
                        "</div>
                    </div>";

        $script = "<script>
                function showInput(){
                    var upload = document.getElementById('upload');
                    if (upload.className === 'hide') {
                        upload.className = 'show';
                    } else {
                        upload.className = 'hide';
                    }
                }
            </script>";
        return $content.$script;
    }
}