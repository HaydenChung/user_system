<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:11
 */

class Redirect{
	public static function to($location = null){
		if($location){
			if(is_numeric($location)){
				switch($location){
				case 404:
					header('HTTP/1.0 404 Not Found');
					include Config::get('document/php').'includes/error/404.php';
					exit();
				break;
				}

			}
			header('Location: '.$location);
			exit;
		}
	}
}