<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 31.07.2018
 * Time: 15:35
 */

namespace common\components;

class DateHelper
{
    const DATE_FORMAT = 'php:Y-m-d';
    const DATETIME_FORMAT = 'php:Y-m-d H:i:s';
    const TIME_FORMAT = 'php:H:i:s';

    //
    public static function convert($dateStr, $type='date', $format = null) {
        if ($type === 'datetime') {
            $fmt = ($format == null) ? self::DATETIME_FORMAT : $format;
        }
        elseif ($type === 'time') {
            $fmt = ($format == null) ? self::TIME_FORMAT : $format;
        }
        else {
            $fmt = ($format == null) ? self::DATE_FORMAT : $format;
        }
        return \Yii::$app->formatter->asDate($dateStr, $fmt);
    }

    //
    public static function getCurrentTimeStamp( $timestamp = false ){
        if( $timestamp ) return time();
        return self::convert( time(), 'datetime' );
    }

    //
    public static function getCurrentDate(){
        return self::convert( time(), 'date' );
    }

    //
    public static function convertViewToDB( $str, $delimeter = null ){
//        return date("Y/d/m", $str );
        if( empty($str) ) return $str;
        if( $delimeter == null ) $delimeter = '/';

        // timestamp
        if( $delimeter === true ){
            return strtotime( $str );
        }

        $a = explode( $delimeter, trim($str) );
        return $a[2].'-'.$a[1].'-'.$a[0];
    }

    public static function convertDBToView( $str, $delimeter = null ){
//        return date("Y/d/m", $str );
        if( empty($str) ) return $str;
        if( $delimeter == null ) $delimeter = '/';

        // timestamp
        if( $delimeter === true ){
            return date("d/m/Y", $str );
        }

        // simple date
        $a = explode( '-', trim($str) );
        return $a[2] . $delimeter . $a[1] . $delimeter . $a[0];
    }

    public static function convertViewToTimestamp( $str, $timeadd = null ){
        if( empty($str) ) return $str;

        $str = static::convertViewToDB( $str ) . $timeadd;
        return strtotime( $str );
    }

    public static function convertModelDatesToDB( $dates, $model, $delimeter = null )
    {
        foreach ($dates as $date) {
            $model->$date = DateHelper::convertViewToDB( $model->$date, $delimeter, $delimeter = null );
        }
    }

    public static function convertModelDatesToView( $dates, $model, $delimeter = null )
    {
        foreach ($dates as $date) {
            $model->$date = DateHelper::convertDBToView( $model->$date, $delimeter );
        }
    }
}