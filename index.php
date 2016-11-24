<?php
/**
 * Created by : lastcoin
 * Date: 6/9/2016
 * Time: 17:04
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/user_system/php/core/init.php';


if(Session::exists('indexdotphp')){
echo Session::flash('indexdotphp');
}

$user = new User();
if($user->isLoggedin()){
?>
	<p>Hello <a href="profile.php?user=<?php echo htmlspecialchars($user->data()->user_id); ?>"><?php echo htmlspecialchars($user->data()->user_id); ?></a>!</p>

	<ul>
		<li><a href="logout.php">Log out</a></li>
		<li><a href="update.php">Update deital</a></li>
		<li><a href="changepassword.php">Change password</a></li>
	</ul>

<?php

	if($user->hasPermission('admin')){
		echo '<p>You are an administrator!</p>';
	}
} else {

	echo '<p>You need to <a href="login.php">log in</a> or <a href="register.php">register</a></p>';
}

