<?php

class Token {

	private static $_token;

	public static function generate($stockToken=null){
		$tokenPath = is_null($stockToken) ? Config::get('session/token_name') : $stockToken ;
        if (function_exists('mcrypt_create_iv')) {
            $token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
	    } else {
	        $token = bin2hex(openssl_random_pseudo_bytes(32));
	    }

	    Session::put($tokenPath,$token);
	    return self::$_token = Session::get($tokenPath);
	}

	public static function check($inputToken,$stockToken=null){
		$tokenPath = is_null($stockToken) ? Config::get('session/token_name') : $stockToken;

		if(hash_equals($inputToken,Session::get($tokenPath))){
			Session::delete($tokenPath);
			return true;
		}

		return false;
	}

    public static function gen_PerForm($formName,$secondToken=null){
    	$secTokenPath = is_null($secondToken) ? Config::get('session/token_name') : $secondToken ;

    	self::generate($secTokenPath);
    	if(Session::exists($secTokenPath)){
	        return self::$_token=hash_hmac('sha256',$formName,Session::get($secTokenPath));    		
    	}else{
    		throw new RuntimeException("gen_PerForm:Second Token is empty,make sure it's stocked in $_SESSION.Current path=>'{$secTokenPath}'.");
    	}
    }

    public static function check_PerForm($formName,$inputToken,$secondToken=null){

    	$secTokenPath = is_null($secondToken) ? Config::get('session/token_name') : $secondToken ;
    	$stockToken = hash_hmac('sha256',$formName,Session::get($secTokenPath));

		if(hash_equals($inputToken,$stockToken)){
			Session::delete($secTokenPath);
			return true;
		}

		return false;
    }

    public static function get(){
    	return self::$_token;
    }
}