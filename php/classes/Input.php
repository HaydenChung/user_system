<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:11
 */

class Input {
    /**
     * @param string $tyep => method name.eg.POST,GET.
     *
     * @return 
     */


	private static	$_instance = null,
					$_method = '';

	public static function exists($type){

		$method='';

		switch(strtoupper($type)){
			case 'POST'	:	if(!empty($_POST)){	$method='POST'; } ;
			break;
			case 'GET'	:	if(!empty($_GET)){ $method='GET'; } ;
			break;
			default 	:	return false;
		}

		self::$_method = $method;
		self::$_instance = new Input();

		return self::$_instance;
		
	}

	/**
	 * @param string $item => input element's name.
	 * 
	 * @return mixed Element's value,null if not exists.
	 *
	 */

	public static function get($item=null){

		$results;

		switch(self::$_method){
			case 'POST'	:	$result = $_POST;
			break;
			case 'GET'	:	$result = $_GET;
			break;
			default:		return '';
		}

		if($item!==null && isset($result[$item])){
			return $result[$item];
		}

		return $result;
		
	}

	public static function toHtml($item=null){
		return htmlspecialchars(self::get($item));
	}

}
