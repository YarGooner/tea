<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 31.10.2018
 * Time: 11:04
 */

namespace admin\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $phoneFile;

    public $modemFile;

    public $path;

    public function rules()
    {
        return [
            [['phoneFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'txt, csv'],
            [['modemFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'txt, csv'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'phoneFile' => 'Загрузите текстовый файл',
            'modemFile' => 'Загрузите текстовый файл',
        ];
    }
}