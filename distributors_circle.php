<?php

if(isset($_COOKIE['session']) && !empty($_COOKIE['session'])){
	session_id($_COOKIE['session']);
}

require_once 'core/init.php';

$user = new user();

if($user->isLoggedIn()){

	if(input::exists()){
		if(token::check(input::get('token'))){
			
      		$db_data = new DB_data("mysql_for_cod_distributor");
      		$no_of_dist = 30;		//number of distributors
      		$lat = $_POST['latitude'];
      		$long = $_POST['longitude'];
      		$rad = 3;	//boundry in kms
      		$response = array();
      		$R = 6371;  // earth's mean radius, km
      		// first-cut bounding box (in degrees)
		    $maxLat = $lat + rad2deg($rad/$R);
		    $minLat = $lat - rad2deg($rad/$R);
		    $maxLon = $long + rad2deg(asin($rad/$R) / cos(deg2rad($lat)));
		    $minLon = $long - rad2deg(asin($rad/$R) / cos(deg2rad($lat)));
		    //dist = arccos(sin(lat1) · sin(lat2) + cos(lat1) · cos(lat2) · cos(lon1 - lon2)) · R
		    $sql = "Select userid, latitude, longitude, acos(sin(radians({$lat}))*sin(radians(latitude)) + cos(radians({$lat}))*cos(radians(latitude))*cos(radians(longitude)-radians({$long}))) * {$R} As D
            From (
                Select userid, latitude, longitude
                From distributors_location
                Where latitude Between {$minLat} And {$maxLat}
                  And longitude Between {$minLon} And {$maxLon}
            ) As FirstCut
            Where acos(sin(radians({$lat}))*sin(radians(latitude)) + cos(radians({$lat}))*cos(radians(latitude))*cos(radians(longitude)-radians({$long}))) * {$R} < {$rad}
            Order by D ASC LIMIT 0,{$no_of_dist}";

            $db_data->query($sql, array(), "SELECT *");
            if(!$db_data->error()){
            	$count = $db_data->count();
            	$response["distributors_count"] = $count;
            	$results = $db_data->results();
            	for($i=0; $i<$count; $i++){
            		$response["distributor_".($i+1)] = array();
            		$result = $results[$i];
            		$db_data->get("distributors", array("userid", "=", $result->userid));
            		if(!$db_data->error()){
            			$dis_data = $db_data->first();
            			$response["distributor_".($i+1)]["distributor_id"] = $dis_data->userid;
            			$response["distributor_".($i+1)]["distributor_name"] = $dis_data->distributor_name;
            			$response["distributor_".($i+1)]["commission_rate"] = $dis_data->commission_rate;
            			$response["distributor_".($i+1)]["details"] = $dis_data->details;
            			$response["distributor_".($i+1)]["latitude"] = $result->latitude;
            			$response["distributor_".($i+1)]["longitude"] = $result->longitude;
            		}else{
            			$response["distributor_".($i+1)]["error"] = "Unable to find distributors data.";
            		}
            	}
            }else{
            	$response['error'] = "Unable to connect.";
            }

            echo json_encode($response, JSON_FORCE_OBJECT);

		}
	}

}

?>