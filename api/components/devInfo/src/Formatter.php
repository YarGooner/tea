<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 22.11.2018
 * Time: 18:28
 */

namespace api\components\devInfo\src;


class Formatter
{
    public static function returnData($data, $header, $status){
        $response = [$status => true, $header => $data];
        return $response;
    }

    public static function formatRequestParams($name,$params,&$result){
        $paramList = explode(' ', $params);
        $type = $paramList[0];
        $param_info = "";
        $param_info .= $type.' :: ';
        if($paramList[1]){
            unset($paramList[0]);
            foreach ($paramList as $item){
                switch ($item){
                    case 'dev': $param_info .= ' :: is_dev';
                        break;
                    default:  $param_info .= 'required';
                }
            }
        }
        $result[$name] = $param_info;
        return $result;
    }
}