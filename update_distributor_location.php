<?php

if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';

$user = new user();

if($user->isLoggedIn()){

	if(input::exists()){
		if(token::check(input::get('token'))){

			$response = array();
			$response["error"] = "";
			$response["service"] = "DistributorLocationUpdate";

			$db_data = new DB_data("mysql_for_cod_distributor");
			$user_data = $user->data();

			$long = $_POST['longitude'];
			$lat = $_POST['latitude'];

			$db_data->get("distributors_location", array("userid", "=", $user_data->id));
			if($db_data->count()>0){
				$db_data->update("distributors_location", array("longitude"=>$long, "latitude"=>$lat), array("userid", "=", $user_data->id));
			}else{
				$db_data->insert("distributors_location", array("userid"=>$user_data->id, "longitude"=>$long, "latitude"=>$lat));
			}
			if(!$db_data->error()){
				$response['status'] = "UPDATED";
			}else{
				$response['error'] .= "Connection to server failed.";
			}
			echo json_encode($response, JSON_FORCE_OBJECT);

		}
	}
}
?>