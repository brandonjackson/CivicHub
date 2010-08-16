<?php

function gpress_bp_profile() {
	
	global $bp, $tppo, $tppobp;
	
	$user_id = $bp->displayed_user->id;
	$user_id_logged_in = $bp->loggedin_user->id;
	$nicename = $bp->displayed_user->userdata->user_nicename;
	$users_post_array = get_users_blog_posts($user_id, 100);
	
	$use_bp_profile = $tppo->get_tppo('use_bp_profile', 'blogs');
	$user_bp_location = $tppobp->get_tppo('user_bp_location', 'users', $user_id);
	
	$my_user_info = get_userdata($user_id);
	$my_name = $my_user_info->display_name;
	$my_firstname = $my_user_info->first_name;
	
	// gPress User Markers
	$marker_users_icon = $tppo->get_tppo('marker_users_icon', 'blogs');
	$marker_users_shadow = $tppo->get_tppo('marker_users_shadow', 'blogs');
	$marker_users_icon_file = $marker_users_icon['filename'];
	$marker_users_icon_url = $marker_users_icon['fileurl'];
	$marker_users_shadow_file = $marker_users_shadow['filename'];
	$marker_users_shadow_url = $marker_users_shadow['fileurl'];
	if(!empty($marker_users_icon_url)) {
		$default_marker_icon_user = $marker_users_icon_url;
	}else{
		if(!empty($marker_users_icon_file)) {
			$default_marker_icon_user = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_users_icon_file;
		} else {
			$default_marker_icon_user = GPRESS_URL.'/gpress-core/images/markers/user.png';
		}
	}
	if(!empty($marker_users_shadow_url)) {
		$default_marker_shadow_user = $marker_users_shadow_url;
	}else{
		if(!empty($marker_users_shadow_file)) {
			$default_marker_shadow_user = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_users_shadow_file;
		} else {
			$default_marker_shadow_user = GPRESS_URL.'/gpress-core/images/markers/bg.png';
		}
	}

	$nicename = $my_name;
	
	$empty_array = true;
		
	$map_id = '_bp_user_location';
	
	$user_position = $user_bp_location['latlng'];
	$user_address = $user_bp_location['address'];
	
	$user_array = array();
	$user_array[$user_id]['user_id'] = $user_id;
	$user_array[$user_id]['latlng'] = $user_position;
	$user_array[$user_id]['address'] = $user_address;
	$user_array[$user_id]['title'] = $nicename;
	$user_array[$user_id]['default_icon'] = $default_marker_icon_user;
	$user_array[$user_id]['default_shadow'] = $default_marker_shadow_user;
	if(!empty($user_position)) {
		$empty_array = false;
	}
	
	$map_settings = array(
		'map_id'		=> $map_id,
		'post_type'		=> 'post',
		'bp_user_array'	=> $user_array,
		'post_id'		=> $post_array
	);

	if(!$empty_array) {
		echo '<h2><span style="text-transform:capitalize">'.$nicename.'</span>\'s '.__('Present Location:', 'gpress').'</h2>';				
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
	
	global $bp, $tppo, $tppobp;
	
	$user_id = $bp->displayed_user->id;
	$nicename = $bp->displayed_user->userdata->user_nicename;
	$users_post_array = get_users_blog_posts($user_id, 100);
	
	$user_rights = $tppo->get_tppo('user_rights', 'sitewide');
	$default_use_bp_activity = $tppo->get_tppo('default_use_bp_activity', 'sitewide');
	$user_bp_activity = $tppobp->get_tppo('user_bp_activity', 'users', $user_id);
	if(empty($user_rights)) {
		$user_rights = 'individual';
	}
	if($user_rights == 'individual') {
		$use_bp_activity = $user_bp_activity;
	}else{
		$use_bp_activity = $default_use_bp_activity;
	}
	if(empty($use_bp_activity)) {
		$use_bp_activity = 'enabled';
	}
	
	$my_user_info = get_userdata($user_id);
	$my_name = $my_user_info->display_name;
	$my_firstname = $my_user_info->first_name;

	$nicename = $my_name;
	
	$post_array = array();
	$empty_array = true;
	if(is_array($users_post_array)) {
		foreach($users_post_array as $key => $post) {
			$geo_public = $post['geo_public'];
			$post_id = $post['post_id'];
			$blog_id = $post['blog_id'];
			switch_to_blog($blog_id);
				$adhoc_markers = get_post_meta($post_id,'_gpress_posts',TRUE);
				// gPress Post Markers
				$marker_posts_icon = $tppo->get_tppo('marker_posts_icon', 'blogs', $blog_id);
				$marker_posts_shadow = $tppo->get_tppo('marker_posts_shadow', 'blogs', $blog_id);
				$marker_posts_icon_file = $marker_posts_icon['filename'];
				$marker_posts_icon_url = $marker_posts_icon['fileurl'];
				$marker_posts_shadow_file = $marker_posts_shadow['filename'];
				$marker_posts_shadow_url = $marker_posts_shadow['fileurl'];
				if(!empty($marker_posts_icon_url)) {
					$default_marker_icon_post = $marker_posts_icon_url;
				}else{
					if(!empty($marker_posts_icon_file)) {
						$default_marker_icon_post = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_posts_icon_file;
					} else {
						$default_marker_icon_post = GPRESS_URL.'/gpress-core/images/markers/post.png';
					}
				}
				if(!empty($marker_posts_shadow_url)) {
					$default_marker_shadow_post = $marker_posts_shadow_url;
				}else{
					if(!empty($marker_posts_shadow_file)) {
						$default_marker_shadow_post = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_posts_shadow_file;
					} else {
						$default_marker_shadow_post = GPRESS_URL.'/gpress-core/images/markers/bg.png';
					}
				}
			restore_current_blog();
			if($geo_public) {
				$post_array[$key]['blog_id'] = $blog_id;
				$post_array[$key]['post_id'] = $post_id;
				$post_array[$key]['post_date'] = $post['post_date'];
				$post_array[$key]['post_title'] = $post['post_title'];
				$post_array[$key]['post_url'] = $post['post_url'];
				$post_array[$key]['post_type'] = $post['post_type'];
				$post_array[$key]['geo_public'] = $geo_public;
				$post_array[$key]['geo_latlng'] = $post['geo_latlng'];
				$post_array[$key]['default_icon'] = $default_marker_icon_post;
				$post_array[$key]['default_shadow'] = $default_marker_shadow_post;
				$post_array[$key]['icon_url'] = $adhoc_markers['icon_url'];
				$post_array[$key]['icon_file'] = $adhoc_markers['icon_file'];
				$post_array[$key]['shadow_url'] = $adhoc_markers['shadow_url'];
				$post_array[$key]['shadow_file'] = $adhoc_markers['shadow_file'];
				$empty_array = false;
			}
		}
	}
	
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
	global $bp, $current_user, $bp_settings_updated, $pass_error, $tppo, $tppobp;
	
	/*
		NEED TO DO THIS DUE TO CUSTOM MARKERS NOT WORKING IN WIDGETS WHEN VIEWED FROM GEO-SETTINGS PAGE
		SEEMS THAT SELECTS.JS FROM TPPO CONFLICTS WITH CUSTOM MARKERS IN WIDGETS
		THIS MEANS THAT WE CANNOT USE THE DROP DOWN FIELD TYPE IN GEO-SETTINGS
	*/
	
	function gpress_tppo_use_selects($use) {
		return false;
	}
	add_filter('tppo_use_selects', 'gpress_tppo_use_selects');
	$use_ui_in_theme = $tppo->get_tppo('use_ui_in_theme', 'blogs');
	if(empty($use_ui_in_theme)) {
		$use_ui_in_theme = 'yes';
	}
	?>
	
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo GPRESS_URL; ?>/gpress-core/css/geo-settings.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo GPRESS_URL; ?>/gpress-bp/css/tppobp.css" />
    
    <?php if($use_ui_in_theme == 'yes') { ?>
	    <script type="text/javascript" src="<?php echo GPRESS_URL; ?>/gpress-admin/js/ui-customised.js"></script>
    <?php } ?>
    
    <form action="<?php echo $bp->loggedin_user->domain . $bp->settings->slug . '/geo' ?>" method="post" class="standard-form" id="geo-form">

	<h3><?php echo __('Geo-Settings', 'gpress'); ?></h3>
    
    <?php /* COLLECT STYLE OPTIONS */
	
	$geo_setting_styles = $tppo->get_tppo('geo_setting_styles', 'sitewide');
	
	/* CHECK TO SEE WHICH GEO-SETTINGS ARE NEEDED */
	global $tppo;
	$user_rights = $tppo->get_tppo('user_rights', 'sitewide');
	if(empty($user_rights)) {
		$user_rights = 'individual';
	}

	$default_primary_bg = '#EEE';
	$default_primary_border = '#DDD';
	$default_primary_color = '#999';
	$default_primary_bg_hover = '#EEE';
	$default_primary_border_hover = '#CCC';
	$default_primary_color_hover = '#666';
	$default_secondary_bg = '#FFF';
	$default_secondary_border = '#DDD';
	$default_secondary_color = '#666';
	$default_secondary_bg_hover = '#EEE';
	$default_secondary_border_hover = '#DDD';
	$default_secondary_color_hover = '#999';
	
	$primary_bg = $geo_setting_styles['primary_bg']['hex'];
	$primary_border = $geo_setting_styles['primary_border']['hex'];
	$primary_color = $geo_setting_styles['primary_color']['hex'];
	$primary_bg_hover = $geo_setting_styles['primary_bg_hover']['hex'];
	$primary_border_hover = $geo_setting_styles['primary_border_hover']['hex'];
	$primary_color_hover = $geo_setting_styles['primary_color_hover']['hex'];
	$secondary_bg = $geo_setting_styles['secondary_bg']['hex'];
	$secondary_border = $geo_setting_styles['secondary_border']['hex'];
	$secondary_color = $geo_setting_styles['secondary_color']['hex'];
	$secondary_bg_hover = $geo_setting_styles['secondary_bg_hover']['hex'];
	$secondary_border_hover = $geo_setting_styles['secondary_border_hover']['hex'];
	$secondary_color_hover = $geo_setting_styles['secondary_color_hover']['hex'];
	
	if(empty($primary_bg)) {
		$primary_bg = $default_primary_bg;
	}
	if(empty($primary_border)) {
		$primary_border = $default_primary_border;
	}
	if(empty($primary_color)) {
		$primary_color = $default_primary_color;
	}
	if(empty($primary_bg_hover)) {
		$primary_bg_hover = $default_primary_bg_hover;
	}
	if(empty($primary_border_hover)) {
		$primary_border_hover = $default_primary_border_hover;
	}
	if(empty($primary_color_hover)) {
		$primary_color_hover = $default_primary_color_hover;
	}
	if(empty($secondary_bg)) {
		$secondary_bg = $default_secondary_bg;
	}
	if(empty($secondary_border)) {
		$secondary_border = $default_secondary_border;
	}
	if(empty($secondary_color)) {
		$secondary_color = $default_secondary_color;
	}
	if(empty($secondary_bg_hover)) {
		$secondary_bg_hover = $default_secondary_bg_hover;
	}
	if(empty($secondary_border_hover)) {
		$secondary_border_hover = $default_secondary_border_hover;
	}
	if(empty($secondary_color_hover)) {
		$secondary_color_hover = $default_secondary_color_hover;
	}
	
	?>
    
    <style>
	
	/* SECONDARY BG = <?php echo $secondary_bg; ?> */
	form.standard-form .tpp_form_container .ui-state-default, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-default {
		background:<?php echo $secondary_bg; ?> !important;
	}
	
	/* SECONDARY BORDER = <?php echo $secondary_border; ?> */
	form.standard-form .tpp_form_container #mapCanvas_user_bp_location, 
	form.standard-form .tpp_form_container #search_address_user_bp_location, 
	form.standard-form .tpp_form_container #search_address_user_bp_location:hover, 
	form.standard-form .tpp_form_container .ui-state-default, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-default {
		border-color:<?php echo $secondary_border; ?> !important;
	}
	
	/* SECONDARY COLOR = <?php echo $secondary_color; ?> */
	form.standard-form .tpp_form_container .ui-state-default a, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-default a {
		color:<?php echo $secondary_color; ?> !important;
	}
	
	/* SECONDARY BG HOVER = <?php echo $secondary_bg_hover; ?> */
	form.standard-form .tpp_form_container .ui-state-default:hover, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-default:hover {
		background:<?php echo $secondary_bg_hover; ?> !important;
	}
	
	/* SECONDARY BORDER HOVER = <?php echo $secondary_border_hover; ?> */
	form.standard-form .tpp_form_container .ui-state-default:hover, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-default:hover {
		border-color:<?php echo $secondary_border_hover; ?> !important;
	}
	
	/* SECONDARY COLOR HOVER = <?php echo $secondary_color_hover; ?> */
	form.standard-form .tpp_form_container .ui-state-default a:hover, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-default a:hover,
	form.standard-form .tpp_form_container .ui-state-default:hover a, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-default:hover a {
		color:<?php echo $secondary_color_hover; ?> !important;
	}
	
	/* PRIMARY BG = <?php echo $primary_bg; ?> */
	form.standard-form .tpp_form_container .ui-state-active, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-active, 
	form.standard-form .tpp_form_container .right-container .form-container .button input.submit, 
	form.standard-form .tpp_form_container .right-container .form-container input[type="button"] {
		background:<?php echo $primary_bg; ?> !important;
	}
	
	/* PRIMARY BORDER = <?php echo $primary_border; ?> */
	form.standard-form .tpp_form_container .ui-tabs .ui-tabs-nav, 
	form.standard-form .tpp_form_container .ui-state-active, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-active, 
	form.standard-form .tpp_form_container .right-container .form-container .button input.submit, 
	form.standard-form .tpp_form_container .right-container .form-container input[type="button"] {
		border-color:<?php echo $primary_border; ?> !important;
	}
	
	/* PRIMARY COLOR = <?php echo $primary_color; ?> */
	form.standard-form .tpp_form_container .ui-state-active a, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-active a, 
	form.standard-form .tpp_form_container .right-container .form-container .button input.submit, 
	form.standard-form .tpp_form_container .right-container .form-container input[type="button"] {
		color:<?php echo $primary_color; ?> !important;
	}
	
	/* PRIMARY BG HOVER = <?php echo $primary_bg_hover; ?> */
	form.standard-form .tpp_form_container .ui-state-active:hover, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-active:hover, 
	form.standard-form .tpp_form_container .right-container .form-container .button input.submit:hover, 
	form.standard-form .tpp_form_container .right-container .form-container input[type="button"]:hover {
		background:<?php echo $primary_bg_hover; ?> !important;
	}
	
	/* PRIMARY BORDER HOVER = <?php echo $primary_border_hover; ?> */
	form.standard-form .tpp_form_container .ui-state-active:hover, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-active:hover, 
	form.standard-form .tpp_form_container .right-container .form-container .button input.submit:hover, 
	form.standard-form .tpp_form_container .right-container .form-container input[type="button"]:hover {
		border-color:<?php echo $primary_border_hover; ?> !important;
	}
	
	/* PRIMARY COLOR HOVER = <?php echo $primary_color_hover; ?> */
	form.standard-form .tpp_form_container .ui-state-active a:hover, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-active a:hover, 
	form.standard-form .tpp_form_container .ui-state-active:hover a, 
	form.standard-form .tpp_form_container .ui-widget-content .ui-state-active:hover a, 
	form.standard-form .tpp_form_container .right-container .form-container .button input.submit:hover, 
	form.standard-form .tpp_form_container .right-container .form-container input[type="button"]:hover {
		color:<?php echo $primary_color_hover; ?> !important;
	}
	
	<?php if($user_rights == 'override') { ?>
		form.standard-form .tpp_form_container ul.ui-tabs-nav {
			display:none !important;
		}
	<?php } ?>
	
	</style>
    
    <?php
		
		/* THIS CREATES THE FRONT-END USER OPTIONS FORM */
		$tppobp->tppo_form();
		
	?>
    
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

function gpress_user_signup() {
	global $bp, $current_user, $bp_settings_updated, $pass_error, $tppo, $tppobp;
	
	/*
		NEED TO DO THIS DUE TO CUSTOM MARKERS NOT WORKING IN WIDGETS WHEN VIEWED FROM GEO-SETTINGS PAGE
		SEEMS THAT SELECTS.JS FROM TPPO CONFLICTS WITH CUSTOM MARKERS IN WIDGETS
		THIS MEANS THAT WE CANNOT USE THE DROP DOWN FIELD TYPE IN GEO-SETTINGS
	*/
	
	function gpress_tppo_use_selects($use) {
		return false;
	}
	add_filter('tppo_use_selects', 'gpress_tppo_use_selects');
	$use_ui_in_theme = $tppo->get_tppo('use_ui_in_theme', 'blogs');
	if(empty($use_ui_in_theme)) {
		$use_ui_in_theme = 'yes';
	}
	
	?>
	
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo GPRESS_URL; ?>/gpress-core/css/geo-settings.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo GPRESS_URL; ?>/gpress-bp/css/tppobp.css" />
    
    <?php if($use_ui_in_theme == 'yes') { ?>
	    <script type="text/javascript" src="<?php echo GPRESS_URL; ?>/gpress-admin/js/ui-customised.js"></script>
    <?php } ?>
    
    <div id="gmaps-location-section">
    
	    <h4><?php echo __('Your Location:', 'gpress'); ?></h4>
        
		<?php /* COLLECT STYLE OPTIONS */
        
        $geo_setting_styles = $tppo->get_tppo('geo_setting_styles', 'sitewide');
        
        /* CHECK TO SEE WHICH GEO-SETTINGS ARE NEEDED */
        global $tppo;
        $user_rights = $tppo->get_tppo('user_rights', 'sitewide');
        if(empty($user_rights)) {
            $user_rights = 'individual';
        }
    
        $default_primary_bg = '#EEE';
        $default_primary_border = '#DDD';
        $default_primary_color = '#999';
        $default_primary_bg_hover = '#EEE';
        $default_primary_border_hover = '#CCC';
        $default_primary_color_hover = '#666';
        $default_secondary_bg = '#FFF';
        $default_secondary_border = '#DDD';
        $default_secondary_color = '#666';
        $default_secondary_bg_hover = '#EEE';
        $default_secondary_border_hover = '#DDD';
        $default_secondary_color_hover = '#999';
        
        $primary_bg = $geo_setting_styles['primary_bg']['hex'];
        $primary_border = $geo_setting_styles['primary_border']['hex'];
        $primary_color = $geo_setting_styles['primary_color']['hex'];
        $primary_bg_hover = $geo_setting_styles['primary_bg_hover']['hex'];
        $primary_border_hover = $geo_setting_styles['primary_border_hover']['hex'];
        $primary_color_hover = $geo_setting_styles['primary_color_hover']['hex'];
        $secondary_bg = $geo_setting_styles['secondary_bg']['hex'];
        $secondary_border = $geo_setting_styles['secondary_border']['hex'];
        $secondary_color = $geo_setting_styles['secondary_color']['hex'];
        $secondary_bg_hover = $geo_setting_styles['secondary_bg_hover']['hex'];
        $secondary_border_hover = $geo_setting_styles['secondary_border_hover']['hex'];
        $secondary_color_hover = $geo_setting_styles['secondary_color_hover']['hex'];
        
        if(empty($primary_bg)) {
            $primary_bg = $default_primary_bg;
        }
        if(empty($primary_border)) {
            $primary_border = $default_primary_border;
        }
        if(empty($primary_color)) {
            $primary_color = $default_primary_color;
        }
        if(empty($primary_bg_hover)) {
            $primary_bg_hover = $default_primary_bg_hover;
        }
        if(empty($primary_border_hover)) {
            $primary_border_hover = $default_primary_border_hover;
        }
        if(empty($primary_color_hover)) {
            $primary_color_hover = $default_primary_color_hover;
        }
        if(empty($secondary_bg)) {
            $secondary_bg = $default_secondary_bg;
        }
        if(empty($secondary_border)) {
            $secondary_border = $default_secondary_border;
        }
        if(empty($secondary_color)) {
            $secondary_color = $default_secondary_color;
        }
        if(empty($secondary_bg_hover)) {
            $secondary_bg_hover = $default_secondary_bg_hover;
        }
        if(empty($secondary_border_hover)) {
            $secondary_border_hover = $default_secondary_border_hover;
        }
        if(empty($secondary_color_hover)) {
            $secondary_color_hover = $default_secondary_color_hover;
        }
        
        ?>
        
        <style>
        
        /* SECONDARY BG = <?php echo $secondary_bg; ?> */
        form.standard-form .tpp_form_container .ui-state-default, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-default {
            background:<?php echo $secondary_bg; ?> !important;
        }
        
        /* SECONDARY BORDER = <?php echo $secondary_border; ?> */
        form.standard-form .tpp_form_container #mapCanvas_user_bp_location, 
        form.standard-form .tpp_form_container #search_address_user_bp_location, 
        form.standard-form .tpp_form_container #search_address_user_bp_location:hover, 
        form.standard-form .tpp_form_container .ui-state-default, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-default {
            border-color:<?php echo $secondary_border; ?> !important;
        }
        
        /* SECONDARY COLOR = <?php echo $secondary_color; ?> */
        form.standard-form .tpp_form_container .ui-state-default a, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-default a {
            color:<?php echo $secondary_color; ?> !important;
        }
        
        /* SECONDARY BG HOVER = <?php echo $secondary_bg_hover; ?> */
        form.standard-form .tpp_form_container .ui-state-default:hover, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-default:hover {
            background:<?php echo $secondary_bg_hover; ?> !important;
        }
        
        /* SECONDARY BORDER HOVER = <?php echo $secondary_border_hover; ?> */
        form.standard-form .tpp_form_container .ui-state-default:hover, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-default:hover {
            border-color:<?php echo $secondary_border_hover; ?> !important;
        }
        
        /* SECONDARY COLOR HOVER = <?php echo $secondary_color_hover; ?> */
        form.standard-form .tpp_form_container .ui-state-default a:hover, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-default a:hover,
        form.standard-form .tpp_form_container .ui-state-default:hover a, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-default:hover a {
            color:<?php echo $secondary_color_hover; ?> !important;
        }
        
        /* PRIMARY BG = <?php echo $primary_bg; ?> */
        form.standard-form .tpp_form_container .ui-state-active, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-active, 
        form.standard-form .tpp_form_container .right-container .form-container .button input.submit, 
        form.standard-form .tpp_form_container .right-container .form-container input[type="button"] {
            background:<?php echo $primary_bg; ?> !important;
        }
        
        /* PRIMARY BORDER = <?php echo $primary_border; ?> */
        form.standard-form .tpp_form_container .ui-tabs .ui-tabs-nav, 
        form.standard-form .tpp_form_container .ui-state-active, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-active, 
        form.standard-form .tpp_form_container .right-container .form-container .button input.submit, 
        form.standard-form .tpp_form_container .right-container .form-container input[type="button"] {
            border-color:<?php echo $primary_border; ?> !important;
        }
        
        /* PRIMARY COLOR = <?php echo $primary_color; ?> */
        form.standard-form .tpp_form_container .ui-state-active a, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-active a, 
        form.standard-form .tpp_form_container .right-container .form-container .button input.submit, 
        form.standard-form .tpp_form_container .right-container .form-container input[type="button"] {
            color:<?php echo $primary_color; ?> !important;
        }
        
        /* PRIMARY BG HOVER = <?php echo $primary_bg_hover; ?> */
        form.standard-form .tpp_form_container .ui-state-active:hover, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-active:hover, 
        form.standard-form .tpp_form_container .right-container .form-container .button input.submit:hover, 
        form.standard-form .tpp_form_container .right-container .form-container input[type="button"]:hover {
            background:<?php echo $primary_bg_hover; ?> !important;
        }
        
        /* PRIMARY BORDER HOVER = <?php echo $primary_border_hover; ?> */
        form.standard-form .tpp_form_container .ui-state-active:hover, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-active:hover, 
        form.standard-form .tpp_form_container .right-container .form-container .button input.submit:hover, 
        form.standard-form .tpp_form_container .right-container .form-container input[type="button"]:hover {
            border-color:<?php echo $primary_border_hover; ?> !important;
        }
        
        /* PRIMARY COLOR HOVER = <?php echo $primary_color_hover; ?> */
        form.standard-form .tpp_form_container .ui-state-active a:hover, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-active a:hover, 
        form.standard-form .tpp_form_container .ui-state-active:hover a, 
        form.standard-form .tpp_form_container .ui-widget-content .ui-state-active:hover a, 
        form.standard-form .tpp_form_container .right-container .form-container .button input.submit:hover, 
        form.standard-form .tpp_form_container .right-container .form-container input[type="button"]:hover {
            color:<?php echo $primary_color_hover; ?> !important;
        }
        
        form.standard-form .tpp_form_container ul.ui-tabs-nav, 
		form.standard-form .tpp_form_container div.button, 
		form.standard-form .tpp_form_container .sub_tab_intro, 
		form.standard-form .tpp_form_container .right-container .form-container ul.form-list li label.general-label {
            display:none !important;
        }
		form.standard-form .tpp_form_container div.second_half, 
		.tpp_form_container .right-container .form-container ul.form-list li span.help-text, 
		.tpp_form_container .right-container .form-container ul.form-list, 
		form.standard-form .tpp_form_container {
			margin:0 !important;
			margin-bottom:0 !important;
		}
		#gmaps-location-section {
			clear:both;
			width:100%;
			padding:15px 0 0;
		}
        
        </style>
        
        <?php
            
            /* THIS CREATES THE FRONT-END USER OPTIONS FORM */
            $tppobp->tppo_form();
            
        ?>
        
    </div>
    
	<?php
}

function gpress_signup_usermeta($usermeta) {
	$geodata['gpress_users_latlng'] = $_POST['tppo']['users']['0']['user_bp_location']['latlng'];
	$geodata['gpress_users_address'] = $_POST['tppo']['users']['0']['user_bp_location']['address'];
	$result = array_merge($geodata,$usermeta);	
	return $result;
}

function gpress_activate_user( $user_id, $password, $meta){
	global $tppobp;
	// COLLECT TEMP SETTINGS FROM USER META
	$gpress_latlng = $meta['gpress_users_latlng'];
	$gpress_address = $meta['gpress_users_address'];
	$geodata = array();
	$geodata['latlng'] = $gpress_latlng;
	$geodata['address'] = $gpress_address;
	// NOW NEED TO PUT THIS INFO INTO TPPO
	$tppobp->updateTPPOdata(array('value'=>$geodata), 'users', $user_id, 'user_bp_location');
}

?>