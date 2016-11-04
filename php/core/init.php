<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:15
 */

session_start();

$GLOBALS['config']=array(

    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'user_system'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),
    'session' => array(
        'session_name' => 'user'
    ),
    'document' => array(
        'root' => $_SERVER['DOCUMENT_ROOT'].'/user_system/',
        'php'  => $_SERVER['DOCUMENT_ROOT'].'/user_system/php/'
    )
);

spl_autoload_register(function($class) {
    require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/classes/'.$class.'.php';
});

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/functions/sanitize.php';
