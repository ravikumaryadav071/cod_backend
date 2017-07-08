<?php
session_start();

$GLOBALS['config'] = array(

	'mysql' => array(
			'host' => '127.0.0.1',
			'username' => 'root',
			'password' => '',
			'db' => 'jeweller_login'
		),
	'mysql_for_jewel_data' => array(
			'host' => '127.0.0.1',
			'username' => 'root',
			'password' => '',
			'db' => 'jeweller_data'
		),
	'remember' => array(
			 'cookie_name' => 'hash',
			 'cookie_expiry' => 604800
		),
	'session' => array(
			'session_name' => 'user',
			'token_name' => 'token'

		)
	);

spl_autoload_register(function ($class) {
	require_once '../classes/'. $class .'.php';

});

require_once '../functions/sanitize.php';

if (cookie::exists(config::get('remember/cookie_name')) && !session::exists(config::get('session/session_name'))) {
	//echo "User Asked to be remembered";
	$hash = cookie::get(config::get('remember/cookie_name'));
	$hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

	if ($hashCheck->count()) {
		//echo "hash matches, log user in";
		//echo $hashCheck->first()->user_id;
		$user = new user($hashCheck->first()->user_id);
		$user->login();
	}
}

?>