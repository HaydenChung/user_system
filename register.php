<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';

if($input = Input::exists('POST')){
	if(Token::check_PerForm('register.php',Input::get(Config::get('session/token_name')))){
		$validate = new Validate();
			$validation = $validate->check($input,['user_id'=>array(
				['regexp',['/^.{0,20}$/','登入名稱不可多於二十個字']],
				['regexp',['/^.{4,}$/','登入名稱不可少於四個字']],
				['regexp',['/^[A-Za-z0-9]+$/','使用者名稱只接受英文字母及數目字']],
				['required',[true,'必須填寫本欄']],
				['regexp',['/^[^\s]+$/','登入名稱中不可留有空格']],
				['unique',['users','登入名稱己被註冊']]
				),
			'password'=>array(
				['regexp',['/^.{0,20}$/','密碼請勿長於20字']],
				['regexp',['/^.{9,}$/','密碼請忽短於9個字']],
				['regexp',['/[A-Z]+/','密碼最少須含有一個大寫英文字母']],
				['regexp',['/[0-9]+/','密碼最少須含有一個數目字']],
				['regexp',['/[a-z]+/','密碼最少須含有一個小寫英文字母']],
				['required',[true,'本欄不能留空']]
				),
			'pw_again'=>array(
				['match',['password','內容不相符']],
				['required',[true,'本欄不能留空']]
				),
			'name'=>array(
				['regexp',['/^.{0,20}$/','用戶名不可多於二十個字']],
				['regexp',['/^.{4,}$/','用戶名不可少於四個字']]
				)]);

		if($validate->passed()){
			echo 'Passed';
		}else{
			print_r($validate->error());
		}		
	}

}



?>


<form action="" method="post">
	<div class="field">
		<label for="username">Username</label>
		<input type="text" name="user_id" id="username" value="<?php echo htmlspecialchars(Input::get('user_id')); ?>" autocomplete="off">
	</div>
	<div class="field">
		<label for="password">Input password</label>
		<input type="password" name="password" id="password" >
	</div>
	<div class="field">
		<label for="pw_again">Enter your password again</label>
		<input type="password" name="pw_again" id="pw_again" >
	</div>
	<div class="field">
		<label for="realname">Realname</label>
		<input type="text" name="name" id="realname" value="<?php echo htmlspecialchars(Input::get('name')); ?>" autocomplete="off">
	</div>
	<div>
		<input type="hidden" name="<?php echo Config::get('session/token_name') ?>" value="<?php echo Token::gen_PerForm('register.php'); ?>" >
	</div>
	<input type=submit>
</form>