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
			$response["service"] = "DistributorRegistration";

			if(isset($_FILES) && !empty($_FILES)){
				
				if(isset($_FILES['adhar']) && !empty($_FILES['adhar']) && isset($_FILES['pan']) && !empty($_FILES['pan'])){

					$paths = array();
					$user_data = $user->data();
					$db_dt = new DB_data("mysql_for_cod_distributors_transactions");
					$db_dist = new DB_data("mysql_for_cod_distributor");
					
					$db_dist->get("distributors", array("userid", "=", $user_data->id));
					if($db_dist->count()>0){
						$response["error"] .= "\n Registration request already sent.";
					}
					// $db_dist->get("distributors", array("userid", "=", $user_data->id));
					// if($db_dist->count()>0){
					// 	$response["error"] .= "\n Already registered.";
					// };

					//adhar upload
					$id='adhar';
					$pic_name = $_FILES[$id]['name'];
					$pic_type = $_FILES[$id]['type'];
					$temp = $_FILES[$id]['tmp_name'];

					$ext = strtolower(end(explode('.', basename($pic_name))));
					$valid_format = array("image/jpeg", "image/jpg", "image/png", "image/bmp");
					$valid_ext = array("jpg", "jpeg", "png", "bmp");

					$target = "images/{$id}/";
					$path = $target.$user_data->username.'_'.$id.'.'.$ext;
					$paths[$id] = $path;

					if(in_array($ext, $valid_ext) && in_array($pic_type, $valid_format)){
						if(move_uploaded_file($temp, $path)){
							//do something		
						}else{
							$response['error'] .= "\n Server Error.";
						}
					}else{
						$response['error'] .= "\n Invalid {$id} file extention.";
					}

					//pan upload
					$id='pan';
					$pic_name = $_FILES[$id]['name'];
					$pic_type = $_FILES[$id]['type'];
					$temp = $_FILES[$id]['tmp_name'];

					$ext = strtolower(end(explode('.', basename($pic_name))));
					$valid_format = array("image/jpeg", "image/jpg", "image/png", "image/bmp");
					$valid_ext = array("jpg", "jpeg", "png", "bmp");

					$target = "images/{$id}/";
					$path = $target.$user_data->username.'_'.$id.'.'.$ext;
					$paths[$id] = $path;

					if(in_array($ext, $valid_ext) && in_array($pic_type, $valid_format)){
						if(move_uploaded_file($temp, $path)){
							//do something		
						}else{
							$response['error'] .= "\n Server Error.";
						}
					}else{
						$response['error'] .= "\n Invalid {$id} file extention.";
					}

					if($response['error'] == ""){

						$distributor_name = $_POST['distributor_name'];
						$postal_code = $_POST['postal_code'];
						$commission_rate = $_POST['commission_rate'];
						$details = $_POST['details'];
						$adhar_no = $_POST['adhar_no'];
						$pan_no = $_POST['pan_no'];

						if($distributor_name == ""){
							$response['error'] .= "\n Distributors name have not been entered.";
						}

						if($postal_code == ""){
							$response['error'] .= "\n Postal code have not been entered.";
						}else if(strlen($postal_code)!=6){
							$response['error'] .= "\n Postal code must have 6 digits only.";
						}

						if($commission_rate == ""){
							$response['error'] .= "\n Commission rate have not been entered.";
						}else if($commission_rate>1.5){
							$response['error'] .= "\n Commission rate cannot exceed 1.5% limit.";
						}

						if($adhar_no == ""){
							$response['error'] .= "\n Adhar number have not been entered.";
						}else if(strlen($adhar_no)!=12 || !ctype_digit($adhar_no)){
							$response['error'] .= "\n Adhar number must have 12 digits only.";
						}

						if($pan_no == ""){
							$response['error'] .= "\n Pan number have not been entered.";
						}else if(strlen($pan_no)!=10){
							$response['error'] .= "\n Pan number must have 10 characters only.";
						}elseif (!preg_match('/^[A-za-z]{5,5}\d{4,4}[A-za-z]$/', $pan_no)) {
							$response['error'] .= "\n Pan number is not valid.";
						}

						if($response['error']==""){

							$db_dist->insert("distributors", array("userid"=>$user_data->id, "distributor_name"=>$distributor_name, "commission_rate"=>$commission_rate, "postal_code"=>$postal_code, "details"=>$details));

							if(!$db_dist->error()){
								$db_dist->insert("distributors_ids", array("userid"=>$user_data->id, "adhar_no"=>$adhar_no, "pan_no"=>$pan_no, "adhar_card"=>$paths['adhar'], "pan_card"=>$paths['pan']));
								if(!$db_dist->error()){
									$db_dt->query("CREATE TABLE {$user_data->username}_transactions (id int auto_increment primary key not null, order_id int not null, userid int not null, status varchar(10) not null DEFAULT 'PENDING')", array(), "CREATE");
									if(!$db_dt->error()){
										$response['status'] = "REGISTERED";
									}
								}else{
									$response['error'] .= "\n Connection to server failed.";
								}
							}else{
								$response['error'] .= "\n Connection to server failed.";
							}

						}

					}

				}

			}

			echo json_encode($response, JSON_FORCE_OBJECT);

		}
	}

}

?>