<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';
if (input::exists()) {

	if (token::check(input::get('token'))) {
		$validate = new validate();
		$validation = $validate->check($_POST, array(
				'username' => array('required' => true),
				'password' => array('required' => true)
			));
		$arr = array();
		$arr["error"] = "";
		$errors = "";
		if ($validation->passed()) {
			//Log user in
			$user = new user();
			$remember = (input::get('remember') === 'on') ? true : false;
			$login = $user->login(input::get('username'), input::get('password'), $remember);
			
			if ($login) {
				$user_data = $user->data();
				$arr["status"] = "LoggedIn";
				$arr["user_info"] = array("username"=>$user_data->username,
					"userid"=>$user_data->id,
					"name"=>$user_data->name);
				//redirect::to('index.php');
			} else {
				$arr["status"] = "LogInFailed";
				$arr["error"] = "Incorrect username or password.";
			}

		} else {
			foreach ($validation->errors() as $error) {
				$errors = $error.'\n';
			}
			$arr["error"] = $errors;
		}
		$arr["connection"] = "ALIVE";
		echo json_encode($arr, JSON_FORCE_OBJECT);

	}
}

?>