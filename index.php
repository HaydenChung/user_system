<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:04
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';
try{

$user = DB::getInstance()->update('users','user_id=Good,password=password,group=1,joined='.date("Y-m-d H:i:s"),'name=Good girl');

}catch(RuntimeException $e){
    echo $e->getMessage();
}catch(PDOException $e1){
    echo $e1->getMessage();
}

