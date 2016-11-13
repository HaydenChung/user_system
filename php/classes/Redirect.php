<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:11
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';


try{

if(Input::exists('POST')){

echo Token::check_PerForm('Redirect.php',Input::get(Config::get('session/token_name')))?'true':'false';

}


$token=Token::gen_PerForm('Redirect.php');
echo Token::get();
echo "<form action='' method='post'>";

echo "<input type='hidden' name='".Config::get('session/token_name')."' value='{$token}'>";

nl();
echo "<input type='submit'>";

echo "</form>";
}catch(Exception $e){
	echo $e->__toString();

}