<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 22.03.2019
 * Time: 15:25
 */

namespace common\components;


class SecurityHelper
{

    //
    static public function parseSecretHashWithPrefix( $hash = null, $prefix_letters = 0 ){

        // return null - no value recieved
        // return false - secure check fail
        // return value - success

        if( is_null($hash) ) return null;
        if( !is_string($hash) ) return false;
        $result = base64_decode(substr($hash, $prefix_letters), true);
//        return $result;
        if( $result === false )  return false;
        $result = intval( $result );

        return $result;
    }

}