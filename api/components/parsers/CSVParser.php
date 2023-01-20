<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 28.11.2018
 * Time: 14:53
 */

namespace admin\components\parsers;

use Yii;


class CSVParser
{
    public $tempDir;

    public function getData($model,$field){
        if (!is_dir($this->tempDir)){
            mkdir($this->tempDir);
        }
        $model->path = $this->tempDir . $model->$field->baseName . '.' . $model->$field->extension;
        $model->$field->saveAs($model->path);
        $codes = $this->csvToArray($model->path);
        return $codes;
    }

    public function csvToArray($path){
        $handle = fopen($path,'r+');
        while (($stringArray = fgetcsv($handle,0,',','"'))!== false){
            $values[]= $stringArray;
        };
        return $values;
    }


    public function arrayToCsv($data, $handle){
        fputcsv($handle,$data,',','"');
    }

    public function getFloatFromString($number){
        $number = explode(',', $number);
        $float = $number[0] + 0.1 * $number[1];
        return $float;
    }

    public function insertCsvToDb($name, $tableName, array $fields){
        $inFile = str_replace('\\', '/', $name);
        $lineEnd = '\n';
        foreach ($fields as &$field){
            $field = '`'.$field.'`';
        }
        $fields = implode(', ', $fields);
        $sql = "
              LOAD DATA LOCAL 
              INFILE '".$inFile."' 
              INTO TABLE `".$tableName."`
              FIELDS TERMINATED BY ',' 
              ENCLOSED BY '\"' 
              LINES TERMINATED BY '".$lineEnd."'
              IGNORE 0 LINES
              (".$fields.") 
        ";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function deleteCodesFile($model){
        unlink($model->path);
    }
}