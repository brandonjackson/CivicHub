<?php

/*
Plugin Name: gPress
Plugin URI: http://pressbuddies.com/projects/geopress/
Description: gPress adds new geo-relevant layers to WordPress, allowing you to create your own location-based services...
Version: 0.2.4.3.1
Requires at least: WordPress 3.0 / BuddyPress 1.2.5.2
Tested up to: WordPress 3.0.1 / BuddyPress 1.2.5.2
License: GNU/GPL 2
Author: PressBuddies
Author URI: http://pressbuddies.com/
Site Wide Only: true
*/

define( 'GPRESS_VERSION', '0.2.4.3.1' );
define( 'GPRESS_DIR', WP_PLUGIN_DIR . '/gpress' );
define( 'GPRESS_URL', plugins_url( $path = '/gpress' ) );

/* THE FOLLOWING DEFINITIONS SCAN FOR CUSTOM FILES IN CUSTOM FOLDERS */
/* IT IS WITHIN THESE CUSTOM FILES THAT YOU SHOULD ADD YOUR OWN CODE */
define( 'GPRESS_CS_PHP_DIR', WP_PLUGIN_DIR . '/gpress/custom/custom.php' );
define( 'GPRESS_CS_PHP_URL', plugins_url( $path = '/gpress/custom/custom.php' ) );
define( 'GPRESS_CS_CSS_DIR', WP_PLUGIN_DIR . '/gpress/custom/custom.css' );
define( 'GPRESS_CS_CSS_URL', plugins_url( $path = '/gpress/custom/custom.css' ) );
define( 'GPRESS_CS_JS_DIR', WP_PLUGIN_DIR . '/gpress/custom/custom.js' );
define( 'GPRESS_CS_JS_URL', plugins_url( $path = '/gpress/custom/custom.js' ) );
/* END OF CUSTOM FILE DEFINITIONS */
		
// THIS GETS LOADED AND USED AT THE BOTTOM OF THIS PAGE
function load_after_buddypress() {

	// Secondly, we need to check WP version to ensure it only works with WP 3.0+
	$current_wp_version = get_bloginfo('version');
	if($current_wp_version < 3) {
		
		add_action('admin_notices', 'gpress_admin_message');
		function gpress_admin_message() {
			$admin_message = __('Please note that gPress requires WordPress 3.0 or higher', 'gpress');
			echo '<div id="message_gpress" class="error"><p style="display:block; text-align:center; font-weight:bold;">'.$admin_message.'</p></div>';
		}		
		
	}else{
		
		add_theme_support( 'post-thumbnails' );
		
		add_action(	'init', 'gpress_init' );
		add_action( 'init', 'gpress_taxonomies', 0 );
		add_action( 'init', 'gpress_tinymce' );
		add_action( 'admin_menu', 'remove_meta_boxes' );
		add_action( 'admin_menu', 'gpress_meta_normal_init' );
		add_action( 'admin_menu', 'gpress_meta_sidebar_init' );
		
		add_filter(	'post_updated_messages', 'gpress_updated_messages' );
		add_filter( 'the_content', 'gpress_content_filter' );
		add_filter( 'pre_get_posts', 'gpress_get_posts' );

		// CORE FUNCTIONS
		include( GPRESS_DIR . '/gpress-core/gpress-functions.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-tinymce.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-maps.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-content-filter.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-excerpt-filter.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-shortcodes.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-geoform.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-meta-boxes.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-taxonomy.php' );
		include( GPRESS_DIR . '/gpress-core/gpress-post-types.php' );
		include( GPRESS_DIR . '/gpress-core/widgets/widgets-fav-place.php' );
		include( GPRESS_DIR . '/gpress-core/widgets/widgets-recent-places.php' );
		include( GPRESS_DIR . '/gpress-core/widgets/widgets-foursquare.php' );
		
		// SHORTCODES
		add_shortcode( 'gpress', 'gpress_shortcode' );
		add_shortcode( 'gpress_display', 'gpress_shortcode_display' );
		
		// LOAD AFTER PLUGINS
		function gpress_init_after_plugins() {
			
			// OPTIONS
			global $tppo, $tppobp, $current_user;
		    get_currentuserinfo();
			$user_id = $current_user->ID;
			$user_rights = $tppo->get_tppo('user_rights', 'sitewide');
			$remove_from_excerpt = $tppo->get_tppo('remove_from_excerpt', 'blogs');
			$default_use_bp_profile = $tppo->get_tppo('default_use_bp_profile', 'sitewide');
			$default_use_bp_profile_address = $tppo->get_tppo('default_use_bp_profile_address', 'sitewide');
			if (defined('BP_VERSION') || did_action('bp_init')) {
				$location_at_signup = $tppo->get_tppo('location_at_signup', 'sitewide');
				$user_bp_profile = $tppobp->get_tppo('user_bp_profile', 'users', $user_id);
				$user_bp_profile_address = $tppobp->get_tppo('user_bp_profile_address', 'users', $user_id);
				if(empty($location_at_signup)) {
					$location_at_signup = 'enabled';
				}if($location_at_signup == 'enabled') {
					add_filter( 'bp_signup_usermeta' , 'gpress_signup_usermeta' );
					add_action( 'bp_before_registration_submit_buttons', 'gpress_user_signup', 1 );
					add_action( 'wpmu_activate_user', 'gpress_activate_user', 10, 3 );
				}
			}
			if(empty($user_rights)) {
				$user_rights = 'individual';
			}
			if(empty($remove_from_excerpt)) {
				$remove_from_excerpt = 'no';
			}
			if(empty($default_use_bp_profile)) {
				$use_bp_profile = 'ABOVE';
			}else{
				$use_bp_profile = $default_use_bp_profile;
			}
			if(empty($default_use_bp_profile_address)) {
				$use_bp_profile_address = 'enabled';
			}else{
				$use_bp_profile_address = $default_use_bp_profile_address;
			}
			if($user_rights == 'individual') {
				if(empty($user_bp_profile)) {
					$use_bp_profile = $default_use_bp_profile;
				}else{
					$use_bp_profile = $user_bp_profile;
				}
				if(empty($user_bp_profile_address)) {
					$use_bp_profile_address = $default_use_bp_profile_address;
				}else{
					$use_bp_profile_address = $user_bp_profile_address;
				}
			}
			
			if($remove_from_excerpt == 'no') {
				add_filter( 'the_excerpt', 'gpress_excerpt_filter' );
			}
			if($use_bp_profile == 'ABOVE') {
				add_action( 'bp_before_profile_loop_content', 'gpress_bp_profile' );
			}
			if($use_bp_profile == 'BELOW') {
				add_action( 'bp_after_profile_loop_content', 'gpress_bp_profile' );
			}
			if($use_bp_profile_address == 'enabled') {
				add_action( 'bp_profile_header_meta', 'gpress_bp_profile_address' );
			}
			
			// LAST THING TO LOAD IS CUSTOM SCRIPT
			if(file_exists(GPRESS_CS_PHP_DIR)) {
				include(GPRESS_CS_PHP_DIR);
			}
			
		}
		add_action('wp_head', 'gpress_init_after_plugins');	
		
	}

}

// THIS CHECKS FOR BUDDYPRESS
global $gpress_bp;
if (defined('BP_VERSION') || did_action('bp_init')) {
	$gpress_bp = true;
	include( GPRESS_DIR . '/gpress-bp/gpress-bp-functions.php' );
	add_action( 'bp_before_member_activity_post_form', 'gpress_bp_activity' );
	add_action( 'bp_init', 'load_after_buddypress' );
} else {
	$gpress_bp = false;
	load_after_buddypress();
}

// THIS LOADS CERTAIN OPTIONS AS EARLY AS POSSIBLE
function load_admin_options() {
	// CORE ADMIN FUNCTIONS
	include( GPRESS_DIR . '/gpress-admin/config/gpress_options.php');
	// ONLY IF BUDDYPRESS IS ACTIVE
	if (defined('BP_VERSION') || did_action('bp_init')) {
		/* CHECK TO SEE IF GEO-SETTINGS IS NEEDED */
		global $tppo;
		$user_rights = $tppo->get_tppo('user_rights', 'sitewide');
		$default_use_bp_profile = $tppo->get_tppo('default_use_bp_profile', 'sitewide');
		if(empty($user_rights)) {
			$user_rights = 'individual';
		}
		if(empty($default_use_bp_profile)) {
			$default_use_bp_profile = 'ABOVE';
		}
		if($user_rights == 'individual') {
			add_action( 'wp', 'gpress_add_new_settings_nav', 2 );
			add_action( 'admin_menu', 'gpress_add_new_settings_nav', 2 );
		}else{
			if($default_use_bp_profile != 'NONE') {
				add_action( 'wp', 'gpress_add_new_settings_nav', 2 );
				add_action( 'admin_menu', 'gpress_add_new_settings_nav', 2 );
			}
		}
		include( GPRESS_DIR . '/gpress-bp/user-admin/gpress_bp_options.php');
	}
	// LOADED REGARDLESS OF BUDDYPRESS AT STAGE OF SETTING CURRENT USER
	include( GPRESS_DIR . '/gpress-core/gpress-rss.php' );
}
add_action( 'set_current_user', 'load_admin_options' );

?>