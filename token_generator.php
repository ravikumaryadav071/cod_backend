<?php
if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	
	if(trim($_COOKIE["session"] != "")){
		session_id($_COOKIE["session"]);
	}
}
require_once 'core/init.php';
$token = token::generate();
$arr = array("token"=>$token);
echo json_encode($arr, JSON_FORCE_OBJECT);
?>