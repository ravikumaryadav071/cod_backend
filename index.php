<?php
if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';

$user = new user();

if ($user->isLoggedIn()) {
		
	$arr = array("connection"=>"ALIVE", "status"=>"LoggedIn", "error"=>"");
	echo json_encode($arr, JSON_FORCE_OBJECT);
	
	if ($user->hasPermission('admin')) {
		//	
	}

}
?>