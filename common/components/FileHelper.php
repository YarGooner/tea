<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 21.03.2019
 * Time: 15:58
 */

namespace common\components;


use Yii;
use yii\helpers\ArrayHelper;

class FileHelper
{

    //
    static public function generateFileName( $image, $file_name ) {
        return UserUrlManager::USER_UPLOADS . $file_name . '.' . $image->getExtension();
    }

    //
    static public function generateUserFileName( $image = null, $user_id = null ) {
        $extension = $image ? '.'.$image->getExtension() : '';
        return UserUrlManager::USER_UPLOADS .DIRECTORY_SEPARATOR. time().'_'. ( $user_id ? $user_id : \Yii::$app->user->id ).$extension;
    }

    //
    static public function stringToBytes($value) {
        return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
            switch (strtolower($m[2])) {
                case 't': $m[1] *= pow(1024,4); break;
                case 'g': $m[1] *= pow(1024,3); break;
                case 'm': $m[1] *= pow(1024,2); break;
                case 'k': $m[1] *= 1024; break;
            }
            return $m[1];
        }, $value);
    }

    //
    static public function bytesToString( $value = null, $to = null ){
        if(is_null($value)) return $value;
        $l = ['B','K','M','G','T'];
        $value = intval( $value );

        if( !$to ){
            $l_count = count($l);
            for( $i=0; $i<$l_count; $i++ ){
                if( floor($value / 1024) <= 0 ) {
                    return round( $value, 2 ).$l[$i];
                }
                $value /= 1024;
            }
            return $value;
        }

        $to = strtoupper( $to );
        $index = array_search( $to, $l );
        if( $index === false ) return $value;

        return ( round($value / pow(1024,$index)) ).$l[$index];
    }

    //
    //сохранение картинки
    static public function saveFile( $image, $file_name, $file_type = null, $max_size = null, $return_absolute_path = false ){

        if( !$image ) return ['error' => 'Файл не передан'];

        // Check file type
        if( is_string( $file_type ) ) $file_type = [$file_type];
        else if( $file_type === true ) $file_type = ['image/jpg','image/jpeg','image/png'];
        if( $file_type && !array_search( $image->type, $file_type ) ) return ['error' => 'Не допустимый формат файла'];

        // Check filez size
        if( !$max_size ) $max_size = ArrayHelper::getValue( Yii::$app->params, 'upload-max-size');
        if( $max_size && $image->size > $max_size ) return ['error' => 'Размер файла не должен превышать '.round($max_size/1024/1024, 3).' Mb.' ];

        // /
        $prefix = explode( DIRECTORY_SEPARATOR, $file_name );
        if( count($prefix) > 1 ){
            $file_name = array_pop($prefix);
            $prefix = DIRECTORY_SEPARATOR.join( DIRECTORY_SEPARATOR, $prefix );
        }

        $fileDir = \Yii::getAlias('@uploads').$prefix;
        self::checkDir($fileDir);

//        $image_name = $file_name;// . '.'.$image->getExtension();
        $filePath = $fileDir . DIRECTORY_SEPARATOR . $file_name;

        if (!$image->saveAs($filePath)) {

            return [ 'error' => $image->errors ];

        }
//        return $new_image; // !!!
        return $return_absolute_path ? $file_name : $filePath;
    }


    //
    static public function deleteFile( $file_name, $uploads_folder = true ){
        if( $uploads_folder ) {
            $fileDir = \Yii::getAlias('@uploads');
            $file_name = $fileDir . $file_name;
        }
        if( !file_exists($file_name) ) return false;
        if( unlink($file_name) !== true ) return false;
//        return $file_name;
//        if( @unlink($file_name) !== true ) return false;
        return true;
    }


    //проверка директории
    static public function checkDir($path){
        if (!is_dir($path)) {
            mkdir($path);
            chmod($path, '0777');
        }
    }

}