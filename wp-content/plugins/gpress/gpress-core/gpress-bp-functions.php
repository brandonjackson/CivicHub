<?php

function gpress_bp_profile() {
	
	global $bp, $tppo;
	$use_bp_profile = $tppo->get_tppo('use_bp_profile', 'blogs');
	
	$user_id = $bp->displayed_user->id;
	$user_id_logged_in = $bp->loggedin_user->id;
	$nicename = $bp->displayed_user->userdata->user_nicename;
	$users_post_array = get_users_blog_posts($user_id, 100);
	
	$my_user_info = get_userdata($user_id);
	$my_name = $my_user_info->display_name;
	$my_firstname = $my_user_info->first_name;

	$nicename = $my_name;
	
	//echo '<pre>';
	//print_r($bp);
	//echo '</pre>';
	$empty_array = true;
	
	$show_posts_on_profile = false;
	
	if($show_posts_on_profile) {
		$post_array = array();
		foreach($users_post_array as $key => $post) {
			$geo_public = $post['geo_public'];
			$post_id = $post['post_id'];
			if($geo_public) {
				$post_array[$key]['blog_id'] = $post['blog_id'];
				$post_array[$key]['post_id'] = $post_id;
				$post_array[$key]['post_date'] = $post['post_date'];
				$post_array[$key]['post_title'] = $post['post_title'];
				$post_array[$key]['post_url'] = $post['post_url'];
				$post_array[$key]['post_type'] = $post['post_type'];
				$post_array[$key]['geo_public'] = $geo_public;
				$post_array[$key]['geo_latlng'] = $post['geo_latlng'];
				$empty_array = false;
			}
		}
		$map_id = '_bp_profile';
	}
	
	$show_user_locations = true;	
	if($show_user_locations) {
		
		$map_id = '_bp_user_location';
		
		$gpress_user_location = 'gpress_user_location';
		$gpress_user_location_address = 'gpress_user_location_address';
		$user_position = get_user_meta( $user_id, $gpress_user_location, true);
		$user_address = get_user_meta( $user_id, $gpress_user_location_address, true);
		
		$user_array = array();
		$user_array[$user_id]['user_id'] = $user_id;
		$user_array[$user_id]['latlng'] = $user_position;
		$user_array[$user_id]['address'] = $user_address;
		$user_array[$user_id]['title'] = $nicename;
		if(!empty($user_position)) {
			$empty_array = false;
		}
	
	}
	
	//echo '<pre>';
	//print_r($post_array);
	//echo '</pre>';
	
	$map_settings = array(
		'map_id'		=> $map_id,
		'post_type'		=> 'post',
		'bp_user_array'	=> $user_array,
		'post_id'		=> $post_array
	);

	if($empty_array == false) {
		if($show_user_locations) {
			echo '<h2><span style="text-transform:capitalize">'.$nicename.'</span>\'s '.__('Present Location:', 'gpress').'</h2>';				
		} else {
			echo '<h2><span style="text-transform:capitalize">'.$nicename.'</span>\'s '.__('Sitewide Geo-Tagged Posts:', 'gpress').'</h2>';
		}
		gpress_add_map($map_settings);
	}else{
		if($user_id == $user_id_logged_in) {
			$geo_settings_link = '<a href="'.$bp->loggedin_user->domain . $bp->settings->slug . '/geo">'.__('Geo-Settings', 'gpress').'</a>';
			$this_message = sprintf(__('If you would like to display your location here, you first need to goto your %s page and add your location.', 'gpress'), $geo_settings_link);
			echo '<p>'.$this_message.'</p>';
		}
	}
	
}

function gpress_bp_activity() {
	
	global $bp, $tppo;
	$use_bp_activity = $tppo->get_tppo('use_bp_activity', 'blogs');
	
	$user_id = $bp->displayed_user->id;
	$nicename = $bp->displayed_user->userdata->user_nicename;
	$users_post_array = get_users_blog_posts($user_id, 100);
	
	$my_user_info = get_userdata($user_id);
	$my_name = $my_user_info->display_name;
	$my_firstname = $my_user_info->first_name;

	$nicename = $my_name;
	
	//echo '<pre>';
	//print_r($bp);
	//echo '</pre>';
	
	$post_array = array();
	$empty_array = true;
	foreach($users_post_array as $key => $post) {
		$geo_public = $post['geo_public'];
		$post_id = $post['post_id'];
		if($geo_public) {
			$post_array[$key]['blog_id'] = $post['blog_id'];
			$post_array[$key]['post_id'] = $post_id;
			$post_array[$key]['post_date'] = $post['post_date'];
			$post_array[$key]['post_title'] = $post['post_title'];
			$post_array[$key]['post_url'] = $post['post_url'];
			$post_array[$key]['post_type'] = $post['post_type'];
			$post_array[$key]['geo_public'] = $geo_public;
			$post_array[$key]['geo_latlng'] = $post['geo_latlng'];
			$empty_array = false;
		}
	}
	
	//echo '<pre>';
	//print_r($post_array);
	//echo '</pre>';
	
	$map_settings = array(
		'map_id'		=> '_bp_profile',
		'post_type'		=> 'post',
		'post_id'		=> $post_array
	);

	if($use_bp_activity == 'enabled') {
		if($empty_array == false) {
			echo '<h2><span style="text-transform:capitalize">'.$nicename.'</span>\'s '.__('Sitewide Geo-Tagged Posts:', 'gpress').'</h2>';
			gpress_add_map($map_settings);
		}
	}	
	
}

function gpress_bp_geo_settings() {
	global $current_user, $bp_settings_updated, $pass_error, $bproot;

	add_action( 'bp_template_title', 'gpress_geo_settings_title' );
	add_action( 'bp_template_content', 'gpress_geo_settings_content' );
	if(empty($bproot)) {
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}else{
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', ''.$bproot.'/core/templates/members/single/plugins' ) );
	}
}

function gpress_geo_settings_title() {
	_e( 'BuddyPress Geo Settings', 'gpress' );
}

function gpress_geo_settings_content() {
	global $bp, $current_user, $bp_settings_updated, $pass_error, $tppo; ?>
	
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo GPRESS_URL; ?>/gpress-core/css/geo-settings.css" />
    
    <form action="<?php echo $bp->loggedin_user->domain . $bp->settings->slug . '/geo' ?>" method="post" class="standard-form" id="geo-form">

	<h3><?php echo __('Geo-Settings', 'gpress'); ?></h3>
    <p><?php echo __('For now, the only option here is for setting your location as a user, which may then be used on your profile or activity page. More user-centric geo-options will be available soon.', 'gpress'); ?></p>
    
    <label><?php echo __('Your Present Location:', 'gpress'); ?></label>
    
	<?php
	$user_id = $bp->loggedin_user->id;
	$gpress_user_location = 'gpress_user_location';
	$gpress_user_location_address = 'gpress_user_location_address';
    $this_map_id = '_bp_geo_settings';
    $this_map_type = 'ROADMAP';
    $this_map_zoom = '13';
	$geo_settings_latlng = $_POST['geo_settings_latlng'];
	$geo_settings_closest_address = $_POST['geo_settings_closest_address'];
	if (@$_POST['submit'] == 'Save Changes') {
		update_user_meta($user_id, $gpress_user_location, $geo_settings_latlng);
		update_user_meta($user_id, $gpress_user_location_address, $geo_settings_closest_address);
	}
	$this_map_position = get_user_meta( $user_id, $gpress_user_location, true);
	$this_map_address = get_user_meta( $user_id, $gpress_user_location_address, true);
    gpress_geoform($this_map_id, $this_map_type, $this_map_zoom, $this_map_position, false, true, true);
    ?>
    
    <p>&nbsp;</p>    
    <p class="submit">
    	<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'gpress' ) ?>" id="submit" class="auto"/>
    </p>
    
	</form>
<?php
}

function gpress_bp_profile_address() {
	global $bp;
	$user_id = $bp->displayed_user->id;
	$user_id_logged_in = $bp->loggedin_user->id;
	$nicename = $bp->displayed_user->userdata->user_nicename;
	$closest_address = get_user_meta( $user_id, 'gpress_user_location_address', true);
	if(!empty($closest_address)) {
		$this_address = sprintf(__('%s is presently located near %s', 'gpress'), $nicename, $closest_address);
		echo '<p class="bp_profile_header_address">'.$this_address.'.</p>';
	}else{
		if($user_id == $user_id_logged_in) {
			$geo_settings_link = '<a href="'.$bp->loggedin_user->domain . $bp->settings->slug . '/geo">Geo-Settings</a>';
			$geo_message = sprintf(__('If you would like to display your location here, you first need to goto your %s page and add your location.', 'gpress'), $geo_settings_link);
			echo '<p>'.$geo_message.'</p>';
		}
	}
}

function gpress_add_new_settings_nav() {
	global $bp;
	
	$settings_link = $bp->loggedin_user->domain . BP_SETTINGS_SLUG . '/';
	
	bp_core_new_subnav_item( array( 'name' => __( 'Geo-Settings', 'gpress' ), 'slug' => 'geo', 'parent_url' => $settings_link, 'parent_slug' => $bp->settings->slug, 'screen_function' => 'gpress_bp_geo_settings', 'position' => 1, 'user_has_access' => bp_is_home() ) );

}
		
?>