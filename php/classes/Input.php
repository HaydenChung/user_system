<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:11
 */

class Input {

	private static	$_instance = null,
					$_method = '';

	public static function exists($type){

		$method='';

		if(!empty($_GET)){
			$method='GET';
		}elseif(!empty($_POST)){
			$method='POST';
		}

		if(strtoupper($type)==$method){
			if(!isset(self::$_instance)){
				self::$_method = $method;
				self::$_instance = new Input();
			}
			return self::$_instance;
		}
		return false;
	}

	public static function get($item=null){

		$results;

		switch(self::$_method){
			case 'POST'	:	$result = $_POST;
			break;
			case 'GET'	:	$result = $_GET;
			break;
			default:		return '';
		}

		if($item!==null){
			return $result[$item];
		}

		return $result;
		
	}

}
