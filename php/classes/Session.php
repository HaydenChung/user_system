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
			return false;	
		} 
	}

	public static function toHtml($name){
		return htmlspecialchars(self::get($name));
	}

	public static function delete($name){
		if(self::exists($name)){
			delArray($name,$_SESSION);
		}
	}

	public static function flash($name,$string=null){
		
		if(self::exists($name)){
			if($string!==null){
				$string .= '<br>'.self::get($name);
				self::put($name,$string);
				return;
			} 
			$string = self::get($name);
			self::delete($name);
			return $string;
		}else{
			self::put($name,$string);
		}

	}
}

