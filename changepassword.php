<?php
require_once 'php/core/init.php';

$user = new User();

if(!$user->isLoggedin()) {
	Redirect::to('index.php');
}

if($input=Input::exists('POST')) {
	if(Token::check_PerForm('changepassword.php',Input::get('token'))){

		$validate = new Validate();
		$validation = $validate->check($input,array(
			'password_current' => array(
				['required',true,'必須提供舊密碼.']
				),
			'password_new' => array(
				['regexp','/^.{0,20}$/','密碼請勿長於20字'],
				['regexp','/^.{9,}$/','密碼請忽短於9個字'],
				['regexp','/[A-Z]+/','密碼最少須含有一個大寫英文字母'],
				['regexp','/[0-9]+/','密碼最少須含有一個數目字'],
				['regexp','/[a-z]+/','密碼最少須含有一個小寫英文字母'],
				['required',true,'本欄不能留空']
				),
			'pw_new_again' => array(
				['match','password_new','輸入之密碼不相符'],
				['required',true,'本欄不能留空']
				)
			));

		if($validation->passed()){
			
			if(Hash::password_check(Input::get('password_current'),$user->data()->password)) {
				$password_new = Hash::password(Input::get('password_new'));
				$user->update(array(
					'password' => $password_new
					));

				Session::flash('indexdotphp','Your password has been changed.');
				Redirect::to('index.php');
			} else {
				echo '你所輸入的舊密碼不正確.';		
			}

		} else {
			foreach($validation->error() as $error){
				echo $error."<br>";
			}
		}
	}
}

?>

<form action="" method="post">
	<div class="field">
		<label for="password_current">Entry your Current password</label>
		<input type="password" name="password_current" id="password_current" >
	</div>
	<div class="field">
		<label for="password_new">Entry new password</label>
		<input type="password" name="password_new" id="password_new" >
	</div>
	<div class="field">
		<label for="pw_new_again">Entry new password again</label>
		<input type="password" name="pw_new_again" id="pw_new_again" >
	</div>
	<div>
		<input type="hidden" name="<?php echo Config::toHtml('session/token_name') ?>" value="<?php echo Token::gen_PerForm('changepassword.php'); ?>" >
	</div>
	<input type=submit>
</form>

<p><a href="index.php">Back to Home page</a></p>