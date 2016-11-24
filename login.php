<?php
/**
 * Created by PhpStorm.
 * User: lastcoin
 * Date: 6/9/2016
 * Time: 17:05
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';
print_r($_SESSION);
nl();


if($input = Input::exists('POST')){

	if(Token::check_PerForm('login.php',Input::get(Config::get('session/token_name')))){

		$validate =new Validate;
		$validation = $validate->check($input,
			array(
				'user_id'=>array(array('required',true,'本欄不能留空')),
				'password'=>array(array('required',true,'本欄不能留空'))
				));

		if($validation->passed()){
			$user = new User();

			$remember = (Input::get('remember') === 'on') ? true : false;
			$login = $user->login(Input::get('user_id'),Input::get('password'),$remember);

			echo $login === true ? Redirect::to('index.php') : '<b>Invild user or password</b>' ;

		} else {
			foreach($validation->error() as $error){
				echo $error;
				nl();
			}
		}
	}

}



?>

<form action="" method="post">
	<div class="field">
		<label for="user_id">Username</label>
		<input type="text" name="user_id" id="user_id" autocomplete="off">
	</div>

	<div class="field">
		<label for="password">Password</label>
		<input type="password" name="password" id="password" autocomplete="off">
	</div>

	<div class="field">
		<label for="remember">
			<input type="checkbox" name="remember" id="remember">Remember Me
		</label>

	</div>

	<input type="hidden" name="<?php echo Config::toHtml('session/token_name'); ?>" value="<?php echo Token::gen_PerForm('login.php') ?>" >
	<input type="submit">

</form>

<p><a href="index.php">Back to Home page</a></p>