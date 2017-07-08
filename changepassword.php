<?php
require_once 'core/init.php';

$user = new user();

if(!$user->isLoggedIn()) {
	redirect::to('index.php');
}
if (input::exists()) {
	if (token::check(input::get('token'))) {
		//echo 'ok';
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'password_current' => array(
				'required' => true,
				'min' => 6
			),
			'password_new' => array(
				'required' => true,
				'min' =>6
			),
			'password_new_again' => array(
				'required' => true,
				'min' =>6,
				'matches' => 'password_new'
			)
		));
		if ($validation->passed()) {
			// change of password
			if (hash::make(input::get('password_current'), $user->data()->salt) !== $user->data()->password) {
				# code...
				echo "Wrong password";
			} else {
				//echo "okay";
				$salt = hash::salt(32);
				$user->update(array(
						'password' => hash::make(input::get('password_new'), $salt),
						'salt' => $salt
				));

				session::flash('home', 'Your password has been changed');
				redirect::to('index.php');
			}

		} else {
			//loop thorugh errors
			foreach ($validation->errors() as $error) {
				echo $error,'<br>';
			}
		}
	}
}

?>
