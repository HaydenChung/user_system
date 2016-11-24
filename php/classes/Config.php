<?php
/**
 * Create By: lastcoin
 * Date: 6/9/2016
 * Time: 17:08
 */

class Config {
    public static function get($path = null){
        if($path){
            $config = $GLOBALS['config'];
            $path = explode('/',$path);

            foreach($path as $bit){
                if(isset($config[$bit])){
                    $config = $config[$bit];
                }else{
                    throw new RuntimeException('Invalid path');
                }
            }

            return $config;

        }else{
            throw new RuntimeException('Parameter is empty.');

        }
    }

    public static function toHtml($path = null){
        return self::get($path);
    }
}
