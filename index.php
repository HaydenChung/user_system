<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:04
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';
try{

//$params=['user_id'=>'Good','password'=>'password','group'=>'1'];

$param1=['user_id'=>'Good','password'=>'password2','group'=>'1'];
$param2=['user_id'=>'Good2','password'=>'password3','group'=>'1','joined'=>date("Y-m-d H:i:s")];
$params=[$param1,$param2];

$user = DB::getInstance()->multiUpdate('users',$params,'name=Good girl');
//$user = DB::getInstance()->update('users','user_id=Good,password=password,group=1,joined='.date("Y-m-d H:i:s"),'name=Good girl');

}catch(RuntimeException $e){
    echo $e->getMessage();
}catch(PDOException $e1){
    echo $e1->getMessage();
}

