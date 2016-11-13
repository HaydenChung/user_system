<?php

class Session{
	public static function put($name,$value){
		setArray($name,$_SESSION,$value);
	}

	public static function exists($name){
		return !is_null(getArray($name,$_SESSION));
	}

	public static function get($name){
		if(self::exists($name)){
			return getArray($name,$_SESSION);
		}else{
			throw new RuntimeException("Invalid path : '{$name}' .");	
		} 
	}

	public static function delete($name){
		if(self::exists($name)){
			delArray($name,$_SESSION);
		}
	}
}

