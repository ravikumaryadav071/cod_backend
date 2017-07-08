<?php

if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';

$user = new user();
$response = array();
$response["completed"] = "NO";
if($user->isLoggedIn()){

	if(isset($_POST)){

		$user_data = $user->data();
		$db = DB::getInstance();
		$db_dt = new DB_data("mysql_for_cod_distributors_transactions");
		$db_ut = new DB_data("mysql_for_cod_users_transactions");
		$action = $_POST['action'];
		$amount = $_POST['amount'];
		$distributor_id = $_POST['distributorId'];
		if($action=="send"){
			$rec_name = $_POST['receiversName'];
			$rec_phone_no = $_POST['receiversPhoneNo'];
			$rec_addr = $_POST['receiversAddress'];
			$rec_pin_code = $_POST['pinCode'];
			$rec_city = $_POST['city'];
		}else if($action=="receive"){
			$rec_name = $user_data->name;
			$rec_phone_no = $user_data->mobile_no;
			$rec_addr = "";
		}

		$db->get("users", array("id", "=", $distributor_id));
		$dis_data = $db->first();
		$db_ut->insert($user_data->username."_address", array("name"=>$rec_name, "address"=>$rec_addr, "city"=>$rec_city, "pin_code"=>$rec_pin_code, "phone_no"=>$rec_phone_no));
		$db_ut->query("SELECT LAST_INSERT_ID() AS id", array(), "SELECT");
		$addr_det = $db_ut->first();
		$addr_id = $addr_det->id;
		if($addr_id!=0){
			$db_ut->insert($user_data->username."_orders", array("distributor_id"=>$distributor_id, "address_id"=>$addr_id, "amount"=>$amount, "paid"=>"YES"));
			$db_ut->query("SELECT LAST_INSERT_ID() AS id", array(), "SELECT");
			$order_det = $db_ut->first();
			if($order_det->id!=0){
				$db_dt->insert($dis_data->username."_transactions", array("order_id"=>$order_det->id, "userid"=>$user_data->id));
				$db_dt->query("SELECT LAST_INSERT_ID() AS id", array(), "SELECT");
				$d_order_det = $db_dt->first();
				$db_ut->update($user_data->username."_orders", array("order_id"=>$d_order_det->id), array("id", "=", $order_det->id));
				$response["completed"] = "YES";
				$response["order_id"] = $order_det->id;
				$response["d_order_id"] = $d_order_det->id;
				$response["address_id"] = $addr_det->id;
			}else{
				$response["error"] = "Not able to connect to database1.";
			}
		}else{
			$response["error"] = "Not able to connect to database2.";
		}
	}

}
echo json_encode($response, JSON_FORCE_OBJECT);
?>