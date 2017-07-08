<?php
if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';

$user = new user();
if ($user->isLoggedIn()) {

	if(isset($_POST) && !empty($_POST)){

		$user_data = $user->data();
		$db_ut = new DB_data("mysql_for_cod_users_transactions");
		$orders_pp = 1;		//orders per page
		$response = array();
		if(isset($_POST['last_id']) && !empty($_POST['last_id'])){
			$query = "SELECT * FROM {$user_data->username}_orders WHERE id<{$_POST['last_id']} ORDER BY id DESC LIMIT 0,{$orders_pp}";
		}else{
			$query = "SELECT * FROM {$user_data->username}_orders ORDER BY id DESC LIMIT 0,{$orders_pp}";
		}

		$db_ut->query($query, array(), "SELECT *");
		if(!$db_ut->error()){
			$tot_orders = $db_ut->count();
			$orders = $db_ut->results();
			for($i=0; $i<$tot_orders; $i++){
				$order = $orders[$i];
				$db_ut->get($user_data->username."_address", array('id', '=', $order->address_id));
				$address = $db_ut->first();
				$response[$i]['order_id'] = $order->id;
				$response[$i]['d_order_id'] = $order->order_id;
				$response[$i]['address_id'] = $order->address_id;
				$response[$i]['distributor_id'] = $order->distributor_id;
				$response[$i]['date'] = $order->date;
				$response[$i]['amount'] = $order->amount;
				$response[$i]['receiver_id'] = $order->receiver_id;
				$response[$i]['status'] = $order->status;
				$response[$i]['paid'] = $order->paid;
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
?>