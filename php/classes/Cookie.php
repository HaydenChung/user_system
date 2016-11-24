<?php

class Cookie {
	public static function exists($name){
		return !is_null(getArray($name,$_COOKIE));
	}

	public static function get($name){
		if(self::exists($name)){
			return getArray($name,$_COOKIE);
		}else{
			return false;	
		} 
	}

	public static function put($name, $value, $expiry){
		if(setcookie($name,$value,time()+$expiry,'/')){
			return true;
		} 
		return false;
	}

	public static function delete($name){
		self::put($name,null, time()-1);
	}

}