<?php
if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';

$user = new user();
if ($user->isLoggedIn()) {
	if(token::check(input::get('token'))){
		if(isset($_POST) && !empty($_POST)){

			$user_data = $user->data();
			$db_ut = new DB_data("mysql_for_cod_users_transactions");
			$address_pp = 1;		//address per page
			$response = array();
			if(isset($_POST['last_id']) && !empty($_POST['last_id'])){
				$query = "SELECT * FROM {$user_data->username}_address WHERE id<{$_POST['last_id']} ORDER BY id DESC LIMIT 0,{$address_pp}";
			}else{
				$query = "SELECT * FROM {$user_data->username}_address ORDER BY id DESC LIMIT 0,{$address_pp}";
			}

			$db_ut->query($query, array(), "SELECT *");
			if(!$db_ut->error()){
				$tot_add = $db_ut->count();
				$addresses = $db_ut->results();
				for($i=0; $i<$tot_add; $i++){
					$address = $addresses[$i];
					$response[$i]['address_id'] = $address->id;
					$response[$i]['name'] = $address->name;
					$response[$i]['address'] = $address->address;
					$response[$i]['city'] = $address->city;
					$response[$i]['country'] = $address->country;
					$response[$i]['pin_code'] = $address->pin_code;
					$response[$i]['phone_no'] = $address->phone_no;
				}
			}
			echo json_encode($response);
		}
	}
}
?>