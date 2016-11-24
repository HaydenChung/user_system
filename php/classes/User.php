<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:13
 */

class User {
	private $_db,
			$_data,
			$_sessionName,
			$_isLoggedIn;

	public function __construct($user = null){
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session/session_name');

		if(!$user){
			if(Session::exists($this->_sessionName)){
				$user = Session::get($this->_sessionName);

				if($this->get($user)){
					$this->_isLoggedIn = true;
				} else {
					self::logout();
				}
			}
		} else {
			$this->get($user);
		}

	}

	public function create($fields = array()){
		if($this->_db->insert('users',$fields)){
			return true;
		}
		throw new RuntimeException('Unable to create an account.');
	}

	public function get($user = null){
		if($user){
			$field = (is_numeric($user)) ? 'id' : 'user_id';
			$data = $this->_db->get('users',array($field,'=',$user));

			if($data->count()){
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function update ($fields = array(),$id = null){

		if(!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}

		if(!$this->_db->update(Config::get('user_table/table_name'),$fields,'id='.$id)){
			throw new Exception('There was a problem updating user info.');
		}
	}

	public function login($username = null,$password = null,$remember = false){

		if(!$username && !$password && $this->exists()){
			Session::put($this->_sessionName, $this->data()->id);
			return true;
		}

		if($this->get($username)){
			if(password_verify($password,$this->data()->password)){
				Session::put($this->_sessionName, $this->data()->id);
				Token::generate();

				if($remember){

					/**
					 *	Create new token whenever user logging in 
					 *	without token but want to be remember.
					 *	(eg. Logging in with a new device.)
					 *
					 */
					$this->rememberMe();
				}
				return true;
			}
		}
		return false;
	}



	public function rememberMe(){

		/** 
		 *
		 *  User carry selector and token in their cookie,
		 *  server database store the selector and a rehash token.
		 *  Rehash token was create with the selector and token 
		 *	store in user's cookie.
		 *  When user want the server to rememeber him,server will try
		 *	to match selector of both side.If any matched,
		 *	it will rehash the token to see if it match the token
		 *  stored in database.
		 *	By this,bad guys would have a harder time to crack both number,
		 *	it is harder to lock down specific user and if the token
		 *	table is lacked,hacker still need the user token(which
		 *	store in user's cookie) to login.
		 *
		 */

		$expire = Config::get('remember/cookie_expire');
		$selectorVal = Hash::salt(8).uniqid();
		$cookie_Token = Hash::random();
		$hash_token = Hash::generate($selectorVal,$cookie_Token);

		$this->_db->insert(Config::get('remember/table_name'),array(
			'user_id'=> $this->data()->id,
			Config::get('remember/token_name')=> $hash_token,
			Config::get('remember/selector_name') => $selectorVal,
			'expires'=> time()+$expire
			));

		Cookie::put(Config::get('remember/token_name'),$cookie_Token,$expire,'/');
		Cookie::put(Config::get('remember/selector_name'),$selectorVal,$expire,'/');

	}

	public function rememberRenew($seriseID){

		$expire = Config::get('remember/cookie_expire');
		$selectorVal = Hash::salt(8).uniqid();
		$cookie_Token = Hash::random();
		$hash_token = Hash::generate($selectorVal,$cookie_Token);

		$this->_db->update(Config::get('remember/table_name'),array(
			Config::get('remember/token_name') => $hash_token,
			Config::get('remember/selector_name') => $selectorVal,
			'expires' => time()+$expire
			),'id='.$seriseID
			);

		Cookie::put(Config::get('remember/token_name'),$cookie_Token,$expire,'/');
		Cookie::put(Config::get('remember/selector_name'),$selectorVal,$expire,'/');

	}

	public static function rememberCheck(){

	$selector_name = Config::get('remember/selector_name');
	$token_name = Config::get('remember/token_name');
	$table_name = Config::get('remember/table_name');

	    if(Cookie::exists($selector_name) && Cookie::exists($token_name) && !Session::exists(Config::get('session/session_name'))){

	        $selector = Cookie::get($selector_name);
	        $hashCheck = DB::getInstance()->get($table_name,array($selector_name,'=',$selector));

	        if($hashCheck->count()){
	            $livingToken = $hashCheck->first();
	            $hashCheckID = $hashCheck->first()->id;            
	            if($livingToken->expires < time()) {
	                $hashCheck->delete($table_name,array($selector_name,'=',$livingToken->$selector_name));
	                Cookie::delete($token_name);
	                Cookie::delete($selector_name);

	                return;
	            }

	            $rehashToken = Hash::generate(Cookie::get($selector_name),Cookie::get($token_name));

	            if(hash_equals($livingToken->$token_name,$rehashToken)){
	                $user = new User($hashCheck->first()->user_id);
	                $user->login();
	                //extend expiry.
	                $user->rememberRenew($hashCheckID);
	            }else{
	                $hashCheck->delete($table_name,array($selector_name,'=',$livingToken->$selector_name));
	                Cookie::delete($token_name);
	                Cookie::delete($selector_name);

	                return;
	            }
	        }
	    }    
	}

	public function exists(){
		return (!empty($this->_data)) ? true : false ;
	}

	public function logout(){
		Session::delete($this->_sessionName);
        $this->_db->delete(Config::get('remember/table_name'),array(Config::get('remember/selector_name'),'=',Cookie::get(Config::get('remember/selector_name'))));
        Cookie::delete(Config::get('remember/token_name'));
        Cookie::delete(Config::get('remember/selector_name'));		
	}

	public function hasPermission($key) {
		$group = $this->_db->get(Config::get('usergroup_table/table_name'),array('id','=',$this->data()->group));
		
		if($group->count()) {
			$permissions = json_decode($group->first()->{Config::get('usergroup_table/permission_col')},true);

			if($permissions[$key] == true) {
				return true;
			}
		}
		return false;
	}

	public function setPassword($password){
		if($password){
			$this->data()->password=password_hash($password,DEFAULT_HASH);
		}
	}

	public function save(){
		$this->_db->update('users',$this->data(),'user_id = '.$this->data()->user_id);
	}

	public function data(){
		return $this->_data;
	}

	public function isLoggedIn(){
		return $this->_isLoggedIn;
	}


}