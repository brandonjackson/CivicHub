<?php

/* Additional Functions Developed by ThePremiumPress to Debug and Return Specific Data from Foursquare */

// CONFIG
global $show_errors;
$show_errors = false;
// END CONFIG

function tpp4sq_user($display_badges, $display_mayor, $debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$params = array("badges"=>$display_badges, "mayor"=>$display_mayor);
		$foursquare_data = $foursquareObj->get_user($params);
		
		if($debug == true) {
			echo '<h1>Your Foursquare User Information<br />tpp4sq_user($display_badges, $display_mayor, $debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

function tpp4sq_checkins($use_geolat, $use_geolong, $debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$params = array("geolat"=>$use_geolat, "geolong"=>$use_geolong);
		$foursquare_data = $foursquareObj->get_checkins($params);
		
		if($debug == true) {
			echo '<h1>Your Friend\'s Check-In Information<br />tpp4sq_checkins($use_geolat, $use_geolong, $debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

function tpp4sq_history($set_limit, $set_since, $debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$params = array("l"=>$set_limit, "sinceid"=>$set_since);
		$foursquare_data = $foursquareObj->get_history($params);
		
		if($debug == true) {
			echo '<h1>Your Recent Check-In History<br />tpp4sq_history($set_limit, $set_since, $debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

function tpp4sq_friends($set_uid, $not_used, $debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$params = array("uid"=>$set_uid);
		$foursquare_data = $foursquareObj->get_friends($params);
		
		if($debug == true) {
			echo '<h1>Your Friends<br />tpp4sq_friends($set_uid, $not_used, $debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

function tpp4sq_venues($use_geolat, $use_geolong, $set_limit, $set_search, $debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$params = array("geolat"=>$use_geolat, "geolong"=>$use_geolong, "l"=>$set_limit, "q"=>$set_search);
		$foursquare_data = $foursquareObj->get_venues($params);
		
		if($debug == true) {
			echo '<h1>Nearby Venues<br />tpp4sq_venues($use_geolat, $use_geolong, $set_limit, $set_search, $debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

function tpp4sq_venue($venue_id, $debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$params = array("vid"=>$venue_id);
		$foursquare_data = $foursquareObj->get_venue($params);
		
		if($debug == true) {
			echo '<h1>Specific Venue Information<br />tpp4sq_venue($venue_id, $debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

function tpp4sq_tips($use_geolat, $use_geolong, $set_limit, $debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$params = array("geolat"=>$use_geolat, "geolong"=>$use_geolong, "l"=>$set_limit);
		$foursquare_data = $foursquareObj->get_tips($params);
		
		if($debug == true) {
			echo '<h1>Nearby Tips<br />tpp4sq_tips($use_geolat, $use_geolong, $set_limit, $debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

function tpp4sq_categories($debug) {
	
	global $consumer_key,$consumer_secret,$my_oauth_token,$my_oauth_token_secret;
	
	$foursquareObj = new EpiFoursquare($consumer_key, $consumer_secret);
	$foursquareObj->setToken($my_oauth_token,$my_oauth_token_secret);
	
	try {
		
		$foursquare_data = $foursquareObj->get_categories();
		
		if($debug == true) {
			echo '<h1>Supported Categories<br />tpp4sq_categories($debug)</h1>';
			echo '<pre>';
			print_r($foursquare_data->response);
			echo '</pre>';
		} else {
			return $foursquare_data->response;
		}
		
	} catch (Exception $e) {
		global $show_errors;
		if($show_errors == true) {
			echo "Error: " . $e;
		}
	}
	
}

?>