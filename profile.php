<?php

require_once 'php/core/init.php';

if($input = Input::exists('GET')){
	if(!$username = Input::get('user')){
		Redirect::to('index.php');
	} else {
		$user = new User($username);
		if(!$user->exists()) {
			Redirect::to(404);
		} else {
			$data = $user->data();
		}
	}
	
	?>

<h3><?php echo htmlspecialchars($data->user_id); ?></h3>
<p>Full name:<?php echo htmlspecialchars($data->name); ?>

<p><a href="index.php">Back to Home page</a></p>

	<?php
}