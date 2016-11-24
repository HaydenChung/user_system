<?php

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
        'token_name' => 'token',
        'selector_name' => 'selector',
        'cookie_expire' => 604800,
        'table_name' => 'users_session'
    ),
    'session' => array(
        'session_name' => 'user',
        'token_name' => 'token'
    ),
    'user_table'=> array(
        'table_name' => 'users',
        'user_col'  => 'user_id',
        'password_col' => 'password'
    ),
    'usergroup_table' => array(
        'table_name' => 'groups',
        'name_col' => 'name',
        'permission_col' => 'permissions'
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


User::rememberCheck();