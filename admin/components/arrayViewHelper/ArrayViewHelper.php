<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 26.02.2019
 * Time: 13:35
 */

namespace admin\components\arrayViewHelper;


class ArrayViewHelper
{

    /**
     * Возвращает значение аттрибута по значению из БД. Используется в виде
     *  'value' => function($data){
     *      return ArrayViewHelper::returnValueArray('user-admin', 'status', $data->status);
     *  }
     *  в GridView или DetailView
     *
     * @param $model
     * @param $attribute
     * @param $data
     * @return mixed
     */

    public static function returnValueArray($model,$attribute,$data){
        $initArray = self::_getInitArray($model, $attribute);
        return $initArray[$data];
    }

    /**
     * Возвращает массив значений аттрибутов, где ключи - значения из БД. Используется в виде
     * 'filter' => ArrayViewHelper::returnFilterArray('user-admin', 'status')
     * в GridView
     *
     * @param $model
     * @param $attribute
     * @return mixed
     */
    public static function returnFilterArray($model, $attribute){
        $initArray = self::_getInitArray($model, $attribute);
        return $initArray;
    }

    /**
     * Возвращает массив значений аттрибутов, где ключи - значения из БД.
     *
     * @param $model
     * @param $attribute
     * @return mixed
     */
    private static function _getInitArray($model, $attribute){
        $_initArray = require __DIR__ . '\array-config.php';
        return $_initArray[$model][$attribute];
    }
}