<?php
/**
 * Created by PhpStorm.
 * User: lastcoin
 * Date: 6/9/2016
 * Time: 17:05
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';

$user = new User();
$user->logout();

Redirect::to('index.php');