<?php
if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';

$user = new user();

if ($user->isLoggedIn()) {
	$response = array();
	$user_data = $user->data();
	$db_dt = new DB_data("mysql_for_cod_distributors_transactions");
	$db_ut = new DB_data("mysql_for_cod_users_transactions");
	//$db_dist = new DB_data("mysql_for_cod_distributor");
	$arr_count = 0;
	$db = DB::getInstance();
	$db_ut->query("SELECT id, distributor_id, order_id, status FROM {$user_data->username}_orders WHERE (status='PENDING' OR status='ACCEPTED') AND paid='YES' ORDER BY id DESC", array(), "SELECT *");
	if(!$db_ut->error()){

		$results = $db_ut->results();
		$count = $db_ut->count();
		for($i=0; $i<$count; $i++){
			$result = $results[$i];
			$db->get("users", array("id", "=", $result->distributor_id));
			$dist_det = $db->first();
			$db_dt->query("SELECT * FROM {$dist_det->username}_transactions WHERE id=$result->order_id", array(), "SELECT *");
			$order_det = $db_dt->first();
			if($order_det->status != $result->status){
				$db_ut->update($user_data->username."_orders", array("status"=>$order_det->status), array("id", "=", $result->id));
				//$response[] = "{\"order_id:\" {$result->id},\"status:\" {$order_det->status}}";
				$response[$arr_count] = array();
				$response[$arr_count]["order_id"] = $result->id;
				$response[$arr_count]["status"] = $order_det->status;
				$arr_count++;
			}
		}

	}
	echo json_encode($response);
}
?>