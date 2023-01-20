<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 19.06.2018
 * Time: 15:50
 */

namespace common\components;

use Yii;
use yii\helpers\Url;


class Utils
{
//    const UPLOAD_SUFFIX = '/uploads/';

    static public $ROOT_DOMAIN = '';
    static public $base_url = '';
    static public $upload_folder = '';
    static public $upload_folder_url = '';

    //
    static public function getBaseUrl(){
        if( self::$base_url == '' ){
            self::$base_url = str_replace(self::$ROOT_DOMAIN, '', (new Request())->getBaseUrl());
        }
        return self::$base_url;
    }

    //
    static public function getUploadFolder(){
        if( self::$upload_folder == '' ){
            self::$upload_folder = Yii::getAlias('@root'). Yii::$app->params['uploadFolder'].'/';
        }
//        return Yii::getAlias('@backend') . '/web' . Yii::$app->params['uploadFolder'];
        return self::$upload_folder;
    }

    static public function deleteUploadedFile( $url ){
        $path = self::getUploadFolder() . $url;
        if( !file_exists( $path ) ) return false;
        unlink( $path );
        return true;
    }

    static public function getUploadFolderUrl( $url = '' ){
        if( self::$upload_folder_url == '' ){
//            $domain = self::getDomain();
            $domain = self::getDomain() . dirname( self::getBaseUrl() );
            self::$upload_folder_url = "http://{$domain}" . Yii::$app->params['uploadFolder'];
//            self::$upload_folder_url = $domain . dirname( self::getBaseUrl() );
        }
        return self::$upload_folder_url . $url;
        /*
        $param = Yii::$app->params['_uploadFolder'];
        if( $param ) return $param . $url;

//        $domain = Url::base('');
//        $domain = str_replace( '//cp.', '', $domain );
//        $domain = str_replace( '//', '', $domain );
        $domain = self::getDomain();
        $subdomain = Yii::$app->params['uploadSubdomain'];
        if( $subdomain ) $domain = $subdomain .'.'. $domain;
//        return $domain;
        Yii::$app->params['_uploadFolder'] = $param = "http://{$domain}" . Yii::$app->params['uploadFolder'];
        return $param . $url;
        */
    }

    //
    static public function getSiteUrl( $remove_back_folders = 0 ){
        $url = self::getFromRootUrl('', true ).self::getBaseUrl();
        $url_ = explode( '/htdocs/', $url );
        return $url_[0];
    }

    //
    static public function getDomain(){
        return Yii::$app->request->serverName;
//        $param = Yii::$app->params['_Domain'];
//        if( $param ) return $param;

//        $url = Yii::$app->request->serverName;
//        return '['.$url.']';
//        Yii::$app->params['_Domain'] = $param = $url;
//        return $param;
    }

    /**
     * Gets the absolute url from the root if where's no need to use the current app subfolder in the url
     * @param $params
     * @return string
     */
    static public function getFromRootUrl($params='', $nolastslash = false ){
        $request = Yii::$app->request;
        $url = ($request->isSecureConnection ? 'https://' : 'http://' ) . $request->serverName ;
        $controller = ltrim( Yii::$app->urlManager->createUrl($params),'/' );
        $controller = substr( $controller, strpos( $controller, '/' ) );
        if( $nolastslash ) $controller = rtrim( $controller, '/' );
        return $url . $controller;
    }


    // ================= STRING ====================================
    static public function camelize($input, $separator = '-')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    // ================= ARRAY ====================================
    static public function convertArrayToAssosiated( $arr ){
        $new_arr = [];
        foreach ( $arr as $key => $val ){
            $new_arr[ $val ] = '';//$val == $key ? '' : $val;
        }
        return $new_arr;
    }

    public static function getUserIp(){
        $ip = null;
        if(array_key_exists('HTTP_X_FORWARDED_FOR',$_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if($ip == null){
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (stripos($ip,',')) {
            $ip = substr($ip,0,stripos($ip,','));
        }
        return $ip;
    }

}