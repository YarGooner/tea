<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 28.11.2018
 * Time: 17:27
 */

namespace admin\components\parsers;

use admin\models\Region;
use admin\models\Upload;
use yii\helpers\VarDumper;

class ModemCsvParser extends CSVParser
{
    public function insertData($model, $field){
        $codes = $this->getData($model, $field);
        if($codes){
            $id = Upload::createUpload('modem');
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
    }

    private function getTariffsFromUpload($data, $tempDir, $uploadId){
        $dataPath = $tempDir.'data.csv';
        $dataHandle = fopen($dataPath,'w');
        foreach ($data as $key => $value){
            if($key != 0) {
                $values = $value;
            } else continue;
            $values[1] = Region::getRegionFromData($values[1]);
            if(!$values[0]){
                continue;
            }
            $tariffData = [$uploadId,$values[1]];
            $values[4] = $this->getFloatFromString($value[4]);
            foreach ($values as $valueKey => $item){
                if (1 < $valueKey && $valueKey < 11) {
                    $tariffData[] = $item;
                }
            }
            $this->arrayToCsv($tariffData,$dataHandle);
        }
        fclose($dataHandle);
        $this->insertCsvToDb($dataPath, 'modem_tariff', [
            'upload_id',
            'region_id',
            'term',
            'term_measure',
            'speed',
            'speed_measure',
            'cost',
        ]);
        unlink($dataPath);
    }
}