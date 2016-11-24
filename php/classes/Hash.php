<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:11
 */

class Hash {

	public static function generate($string,$salt = ''){
		return hash('sha256',$string.$salt);
	}

	public static function random($length = 32){
		return hash('sha256',bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)));
	}

	public static function salt($length = 32){
		return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
	}

	public static function password($password){
		return password_hash($password,PASSWORD_DEFAULT);
	}

	public static function password_check($password,$hash_password){
		return password_verify($password,$hash_password);
	}

	//uniqid() is a build-in php function,it return a unique string base on current time.
	public static function unique() {
		return	self::generate(uniqid().self::salt(8));
	} 
}