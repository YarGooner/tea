<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 28.11.2018
 * Time: 17:11
 */

namespace admin\components\parsers;

use admin\models\Region;
use admin\models\Upload;
use admin\models\City;
use admin\models\Tariff;

class PhoneCsvParser extends CSVParser
{
    public function insertData($model, $field){
        $codes = $this->getData($model, $field);
        if($codes){
            $id = Upload::createUpload('phone');
        }
        $this->parseData($codes, $id);
        $this->deleteCodesFile($model);
        return true;
    }

    public function parseData($data, $id){
        $tempDir = \Yii::getAlias('@public') . '/uploads/temp/';
        if (!is_dir($tempDir)){
            mkdir($tempDir);
        }
        $this->getTariffsFromUpload($data,$tempDir,$id);
        $this->getOptionsFromUpload($data,$tempDir,$id);
    }

    public function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        }
        else {
            exec($cmd . " > /dev/null &");
        }
    }

    private function getTariffsFromUpload($data, $tempDir, $uploadId){
        $dataPath = $tempDir.'data.csv';
        $dataHandle = fopen($dataPath,'w');
        foreach ($data as $key => $value){
            if($key != 0) {
                $values = $value;
            } else continue;
            $values[0] = Region::getRegionFromData($values[0]);
            $values[1] = City::getCityFromData($values[1],$values[0]);
            if(!$values[0]){
                continue;
            }
            $tariffData = [$values[1],$uploadId];
            $values[7] = $this->getFloatFromString($value[7]);
            $values[6] = $this->getFloatFromString($value[6]);
            foreach ($values as $valueKey => $item){
                if (1 < $valueKey && $valueKey < 11) {
                    $tariffData[] = $item;
                }
            }
            $this->arrayToCsv($tariffData,$dataHandle);
        }
        fclose($dataHandle);
        $this->insertCsvToDb($dataPath, 'tariff', [
            'city_id',
            'upload_id',
            'package_name',
            'minutes_amount',
            'gb_amount',
            'package_cost',
            'sms_cost',
            'minute_cost',
            'minutes_extra',
            'sms_unlim',
            'gb_extra',
        ]);
        unlink($dataPath);
    }

    private function getOptionsFromUpload($data, $tempDir, $uploadId){
        $headers = $data[0];
        $tariffs = Tariff::find()->where(['upload_id' => $uploadId])->all();
        $tariffsIds = [];
        $optionPath = $tempDir.'option.csv';
        $optionHandle = fopen($optionPath,'w');
        foreach ($tariffs as $item){
            $tariffsIds[] = $item->id;
        }
        $i = 0;
        foreach ($data as $key => $value) {
            if ($key != 0) {
                $values = $value;
            } else continue;
            foreach ($values as $valueKey => $item) {
                if ($valueKey > 10) {
                    $optionData = [$tariffsIds[$i]];
                    $optionData[] = $headers[$valueKey];
                    $optionData[] = $item;
                    $this->arrayToCsv($optionData, $optionHandle);
                }
            }
            $i++;
        }
        fclose($optionHandle);
        $this->insertCsvToDb($optionPath, 'option', ['tariff_id','header','value']);
        unlink($optionPath);
    }

}