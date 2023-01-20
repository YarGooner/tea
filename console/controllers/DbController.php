<?php

namespace console\controllers;

use yii\helpers\Console;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use ZipArchive;
use SplFileInfo;

class DbController extends Controller
{
    public $dumpPath = '@app/data';

    public function actionImport($path = null)
    {

        $this->unZip();
        $this->importDump();
    }

    public function actionExport($path = null)
    {
                $this->createArchive();
    }

    private function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    private function createArchive(){
        $pathdir='@app/../public_html/uploads/global/'; // путь к папке, файлы которой будем архивировать
        $pathdir = FileHelper::normalizePath(Yii::getAlias($pathdir));
        $savePath = FileHelper::normalizePath(Yii::getAlias($this->dumpPath));
        $nameArchive = date('Y-m-d H-m-s').'_content.zip'; //название архива
        $zip = new ZipArchive(); // класс для работы с архивами
        if ($zip -> open($savePath.'\\'.$nameArchive, ZipArchive::CREATE) === TRUE){ // создаем архив, если все прошло удачно продолжаем
            $dir = opendir($pathdir); // открываем папку с файлами
            while( false !==($file = readdir($dir))){ // перебираем все файлы из нашей папки
                    if ($file != '.' && $file != '..' && !is_dir($pathdir.'\\'.$file)) { // проверяем файл ли мы взяли из папки
                        $zip->addFile($pathdir . '\\' . $file, $file); // и архивируем
                    }
            }

            $dumpName = $this->getDump();
            $dumpDir = opendir($savePath);
            if ($dump = readdir($dumpDir)){
                $zip->addFile($savePath.'\\'.$dumpName, $dumpName);
//                unlink($savePath.'\\'.$dumpName);

            };


            if($zip -> close()) { // закрываем архив.
                echo 'Архив успешно создан';
                unlink($savePath.'\\'.$dumpName);
            }
        }else{
            die ('Произошла ошибка при создании архива');
        }
    }

    private function getDump(){
        $path = $this->dumpPath;
        $path = FileHelper::normalizePath(Yii::getAlias($path));
        if (file_exists($path)) {
            if (is_dir($path)) {
                if (!is_writable($path)) {
                    echo 'Directory not writable' . PHP_EOL;
                    exit;
                }
                $fileName = 'dump-' . date('Y-m-d-H-i-s') . '.sql';
                $fileName = Console::prompt('Enter filename:', ['default' => $fileName]);
                $filePath = $path . DIRECTORY_SEPARATOR . $fileName;

                $db = Yii::$app->getDb();
                if (!$db) {
                    echo 'DB component not configured' . PHP_EOL;
                    exit;
                }
                exec('mysqldump --host=' . $this->getDsnAttribute('host', $db->dsn) . ' --user=' . $db->username . ' --password=' . $db->password . ' ' . $this->getDsnAttribute('dbname', $db->dsn) . ' --skip-add-locks  > ' . $filePath);
                return $fileName;

            } else {
                echo 'Path must be a directory' . PHP_EOL;
            }
        } else {
            echo 'Path does not exist' . PHP_EOL;
        }
    }

    private function unZip(){
        $path = $this->dumpPath;
        $path = FileHelper::normalizePath(Yii::getAlias($path));
        $destinationPath = FileHelper::normalizePath(Yii::getAlias('@app/../public_html/uploads/global'));
        $dir = opendir($path);
        while(false !==($file = readdir($dir))){
            $ext = new SplFileInfo($file);
            $ext = $ext->getExtension();
            if($ext == 'zip'){
                $zip = new ZipArchive();
                $zip->open($path.'\\'.$file);
                for ($i = 0; $i < $zip->numFiles; $i++) {

                    $item = $zip->getNameIndex($i);
                    $zipExt = new SplFileInfo($item);
                    $zipExt = $zipExt->getExtension();
                    if($zipExt != 'sql'){
                        $zip->extractTo($destinationPath,$item);
                    } else {
                        $zip->extractTo($path,$item);
                    }
                }
                $zip->close();
            }
        }
    }

    public function importDump() {
        $path = $this->dumpPath;
        $path = FileHelper::normalizePath(Yii::getAlias($path));
        if (file_exists($path)) {
            if (is_dir($path)) {
                $files = FileHelper::findFiles($path, ['only' => ['*.sql']]);
                if (!$files) {
                    echo 'Path does not contain any SQL files' . PHP_EOL;
                    exit;
                }
                $select = Console::select('Select SQL file', $files);
                if (Console::confirm('Confirm selected file [' . $files[$select] . ']')) {
                    $path = $files[$select];
                } else {
                    exit;
                }
            }
            $db = Yii::$app->getDb();
            if (!$db) {
                echo 'DB component not configured' . PHP_EOL;
                exit;
            }
            exec('mysql --host=' . $this->getDsnAttribute('host', $db->dsn) . ' --user=' . $db->username . ' --password=' . $db->password . ' ' . $this->getDsnAttribute('dbname', $db->dsn) . ' < ' . $path);
            echo 'Dump file [' . $path . '] was imported' . PHP_EOL;
            $this->deleteDump($path);
        } else {
            echo 'Path does not exist' . PHP_EOL;
        }


    }

    public function deleteDump($path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}