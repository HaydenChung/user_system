<?php
/**
 * Created by PhpStorm.
 * User: lastcoin
 * Date: 6/9/2016
 * Time: 17:06
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';

$user = new User();

if(!$user->isLoggedIn()){
	Redirect::to('index.php');
}

if($input=Input::exists('post')){
	if(Token::check_PerForm('update.php',Input::get(Config::get('session/token_name')))){
		
		$validate = new Validate();
		$validation = $validate->check($input,array(
			'name' => array(
				array('regexp','/^.{0,20}$/','用戶名不可多於二十個字'),
				array('regexp','/^.{4,}$/','用戶名不可少於四個字'),
				array('required',true,'本欄不能留空')
			))
		);

		if($validation->passed()) {
			try{
				$user->update(array(
					'name' => Input::get('name')
					));

					Session::flash('indexdotphp',"你的資料已成功更改.");
					Redirect::to('index.php');

			} catch(Exception $e){
				die($e->getMessage());
			}
		} else {
			foreach($validation->error() as $error){
				echo $error."<br>";
			}
		}
	}
}

?>

<form action ="" method="post">
	<div class="field">
		<label for"name">Name</label>
		<input type=:"text" name="name" value="<?php echo htmlspecialchars($user->data()->name); ?>">

		<input type="submit" value="Update">
		<input type="hidden" name="token" value="<?php echo Token::gen_PerForm('update.php'); ?>">
	</div>
</form>

<p><a href="index.php">Back to Home page</a></p>