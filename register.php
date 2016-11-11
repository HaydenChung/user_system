<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';

if($input = Input::exists('POST')){
	$validate = new Validate();
/*	$validation = $validate->check($_POST,[
		'username'=>[
			'required'=>true,
			'min'=>2,
			'max'=>20,
			'unique'=>'users'
		],'password'=>[
			'required'=>true,
			'min'=>6
		],'pw_again'=>[
			'required'=>true,
			'matches'=>'password'
		],'realname'=>[
			'required'=>true,
			'min'=>2,
			'max'=>50
		]
		]);*/
		$validation = $validate->check($input,['username'=>array(
			'regexp'=>['/^.{0,20}$/','登入名稱不可多於二十個字'],
			'regexp'=>['/^.{4,}$/','登入名稱不可少於四個字'],
			'regexp'=>['/^[A-Za-z0-9]+$/','使用者名稱只接受英文字母及數目字'],
			'required'=>[true,'必須填寫本欄']
			),
		'password'=>array(
			'regexp'=>['/^.{0,20}$/','密碼請勿長於20字'],
			'regexp'=>['/^.{9,}$/','密碼請忽短於9個字'],
			'regexp'=>['/[A-Z]+/','密碼最少須含有一個大寫英文字母'],
			'regexp'=>['/[0-9]+/','密碼最少須含有一個數目字'],
			'regexp'=>['/[a-z]+/','密碼最少須含有一個小寫英文字母'],
			'required'=>[true,'本欄不能留空']
			),
		'pw_again'=>array(
			'match'=>['password','內容不相符'],
			'required'=>[true,'本欄不能留空']
			),
		'realname'=>array(
			'regexp'=>['/^.{0,20}$/','用戶名不可多於二十個字'],
			'regexp'=>['/^.{4,}$/','用戶名不可少於四個字']
			)]);

	if($validate->passed()){
		echo 'Passed';
	}else{
		print_r($validate->error());
	}
}

?>

<form action="" method="post">
	<div class="field">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<?php echo htmlspecialchars(Input::get('username')); ?>" autocomplete="off">
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
		<input type="text" name="realname" id="realname" value="<?php echo htmlspecialchars(Input::get('realname')); ?>" autocomplete="off">
	</div>
	<input type=submit>
</form>
