<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}
require_once 'core/init.php';
$response = array();
if(input::exists()){
	if(token::check(input::get('token'))){
		$validate = new validate();
		$validation = $validate->check($_POST, array(
			'username' => array(
					'required' => true,
					'min' => 4,
					'max' => 30,
					'unique' => 'users'
				),
			'password' => array(
					'reqiured' => true,
					'min' => 6
				),
			'password_again' => array(
				'require' => true,
				'matches' => 'password'
				),
			'name' => array(
				'require' => true,
				'min' => 2,
				'max' => 50
				),
			'email' => array(
				'require'=>true,
				'min' => 5
				),
			'mobile_no' => array(
				'require'=>true,
				'min'=>10,
				'max'=>10)
			));
		if($validation->passed()){
			$db_ut = new DB_data("mysql_for_cod_users_transactions");
			$user =  new user();
			$salt = hash::salt(32);
			try{
				$user->create(array(
						'username' => input::get('username'),
						'password' => hash::make(input::get('password'), $salt),
						'salt' => $salt,
						'name' => input::get('name'),
						'email' => input::get('email'),
						'mobile_no' => input::get("mobile_no"),
						'joined' => date('Y-m-d H:i:s'),
						'group' => 1

					));
				$username = input::get('username');
				$db_ut->query("CREATE TABLE {$username}_address (id int auto_increment primary key not null, name varchar(100) not null, address varchar(200) not null, city varchar(50) not null, country varchar(50) not null DEFAULT 'INDIA', pin_code varchar(6) not null, phone_no varchar(10) not null)", array(), "CREATE");
				$db_ut->query("CREATE TABLE {$username}_orders (id int auto_increment primary key not null, distributor_id int not null, order_id int not null, receiver_id int not null, address_id int not null, amount varchar(10) not null, status varchar(10) not null DEFAULT 'PENDING', paid varchar(5) not null DEFAULT 'NO', date timestamp not null DEFAULT CURRENT_TIMESTAMP, CONSTRAINT FOREIGN KEY (address_id) REFERENCES {$username}_address(id))", array(), "CREATE");
				$response["status"] = "You have registered successfully";
			} catch(Exception $e){
				die($e->getMessage());
			}
		} else{
			$response["error"] = "";
			foreach($validation->errors() as $error){
				$response["error"] .= $error."\n";
			}
			$response["status"] = "error";
		}

		echo json_encode($response, JSON_FORCE_OBJECT);

	}

}
?>