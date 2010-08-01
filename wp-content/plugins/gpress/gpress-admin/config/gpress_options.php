<?php
function initialize_gpress_tppo(){
	
	global $tppo, $gpress_bp, $deactivate_foursquare;
	
	/* THEME OPTIONS INCLUDE FILE */
	require_once( GPRESS_DIR . '/gpress-admin/tpp_options-class.php' );
	
	/* ADVANCED CONFIGURATION OPTIONS */
	require_once( GPRESS_DIR . '/gpress-admin/config/gpress_options_config.php' );
	
	// THESE STORE THE INTRODUCTIONS FOR THE SUB TABS
	require_once( GPRESS_DIR . '/gpress-admin/config/gpress_options_descriptions.php' );
	
	$use_geopost = $tppo->get_tppo('use_geopost', 'blogs');
	$post_control = $tppo->get_tppo('post_control', 'sitewide');
	if(empty($use_geopost)) {
		$use_geopost = 'enabled';
	}
	if(empty($post_control)) {
		$post_control = 'default';
	}
	if($post_control == 'master') {
		$use_geopost = $tppo->get_tppo('use_geopost', 'blogs', 1);
		if(empty($use_geopost)) {
			$use_geopost = 'enabled';
		}
	}elseif($post_control == 'alone') {
		global $blog_id;
		if($blog_id !== 1) {
			$use_geopost = 'disabled';
		}
	}
	$gpress_control = $tppo->get_tppo('gpress_control', 'sitewide');
	if(empty($gpress_control)) {
		$gpress_control = 'default';
	}
	if($gpress_control == 'hide') {
		if(is_super_admin()) {
			$hide_everything = 'no';
		}else{
			$hide_everything = 'yes';				
		}
	}
	$deactivate_foursquare = $tppo->get_tppo('deactivate_foursquare', 'blogs');
	if(empty($deactivate_foursquare)) {
		$deactivate_foursquare = 'no';
	}
	
	/* LANGUAGES */
	$gpress_lang_file = $tppo->get_tppo('gpress_lang_file', 'blogs');
	$gpress_custom_lang_file = $tppo->get_tppo('gpress_custom_lang_file', 'blogs');
	if(empty($gpress_lang_file)) {
		$gpress_lang_file = 'default';
	}
	if(!empty($gpress_custom_lang_file)) {
		load_textdomain( 'gpress', GPRESS_DIR . '/gpress-lang/'.$gpress_custom_lang_file.'.mo' );
	}else{
		load_textdomain( 'gpress', GPRESS_DIR . '/gpress-lang/gpress.mo' );
	}
	
	/*
	if((!empty($gpress_custom_lang_file)) && ($gpress_lang_file == 'custom')) {
		define( 'GPRESS_LANG', $gpress_lang_file ); // => Change 'default' to 'example' to switch all references to Place(s) to Venue(s)
		if ( file_exists( GPRESS_DIR . '/gpress-lang/'.$gpress_custom_lang_file.'.mo' ) ) {
			load_textdomain( 'gpress', GPRESS_DIR . '/gpress-lang/'.$gpress_custom_lang_file.'.mo' );
		}else{
			$gpress_lang_file = apply_filters('switch_gpress_lang', $gpress_lang_file);
			define( 'GPRESS_LANG', $gpress_lang_file ); // => Change 'default' to 'example' to switch all references to Place(s) to Venue(s)
			if ( file_exists( GPRESS_DIR . '/gpress-lang/gpress-' . GPRESS_LANG . '.mo' ) ) {
				load_textdomain( 'gpress', GPRESS_DIR . '/gpress-lang/gpress-' . GPRESS_LANG . '.mo' );
			}
		}
	}else{
		$gpress_lang_file = apply_filters('switch_gpress_lang', $gpress_lang_file);
		define( 'GPRESS_LANG', $gpress_lang_file ); // => Change 'default' to 'example' to switch all references to Place(s) to Venue(s)
		if ( file_exists( GPRESS_DIR . '/gpress-lang/gpress-' . GPRESS_LANG . '.mo' ) ) {
			load_textdomain( 'gpress', GPRESS_DIR . '/gpress-lang/gpress-' . GPRESS_LANG . '.mo' );
		}
	}
	*/
	/* END OF LANGUAGES */
	
	/* ADD TOP TABS AND SIDE TABS TO THEME OPTIONS */
	  // Top Tabs
	if($hide_everything == 'yes') {
		
		$tppo->add_tab(1, __('gPress Components', 'gpress'),false);
		$tppo->add_tab(2, __('General Settings', 'gpress'),false);
		$tppo->add_tab(3, __('Advanced Settings', 'gpress'),false);
		$tppo->add_tab(4, __('Social-Media', 'gpress'),false);
		$tppo->add_tab(5, __('BuddyPress', 'gpress'), false);
		
	}else{
		
		$tppo->add_tab(1, __('gPress Components', 'gpress'));
		$tppo->add_tab(2, __('General Settings', 'gpress'));
		if(is_super_admin()) {
			$tppo->add_tab(3, __('Advanced Settings', 'gpress'));
		}else{
			$tppo->add_tab(3, __('Advanced Settings', 'gpress'),false);
		}
		$tppo->add_tab(4, __('Social-Media', 'gpress'));
		if($gpress_bp) {
			$tppo->add_tab(5, __('BuddyPress', 'gpress'));
		}else{
			$tppo->add_tab(5, __('BuddyPress', 'gpress'), false);
		}
		
	}

	  // Side Tabs
	$tppo->add_sub_tab(1, 1, __('Module Control', 'gpress'), $module_control_intro);
	if($use_geopost == 'enabled') {
		$tppo->add_sub_tab(1, 2, __('Geo-Tagged Posts', 'gpress'), $gtagged_posts_intro);
	}else{
		$tppo->add_sub_tab(1, 2, __('Geo-Tagged Posts', 'gpress'), $gtagged_posts_intro, false);
	}
	$tppo->add_sub_tab(2, 1, __('Map Settings', 'gpress'), $map_settings_intro);
	$tppo->add_sub_tab(2, 2, __('Marker Settings', 'gpress'), $marker_settings_intro);
	
	if(is_super_admin()) {
		$tppo->add_sub_tab(3, 1, __('Loop Settings', 'gpress'), $loop_settings_intro);
		$tppo->add_sub_tab(3, 2, __('Excerpt Settings', 'gpress'), $excerpt_settings_intro);
		$tppo->add_sub_tab(3, 3, __('Brand Settings', 'gpress'), $brand_settings_intro);
		$tppo->add_sub_tab(3, 4, __('Credits', 'gpress'), $credits_intro);
		$tppo->add_sub_tab(3, 5, __('MISC Settings', 'gpress'), $misc_intro);
		$tppo->add_sub_tab(3, 6, __('Language / Lingo', 'gpress'), $lang_ling_intro);
		$tppo->add_sub_tab(3, 7, __('Sitewide Options', 'gpress'), $sitewide_intro);
	}else{
		$tppo->add_sub_tab(3, 1, __('Loop Settings', 'gpress'), $loop_settings_intro,false);
		$tppo->add_sub_tab(3, 2, __('Excerpt Settings', 'gpress'), $excerpt_settings_intro,false);
		$tppo->add_sub_tab(3, 3, __('Brand Settings', 'gpress'), $brand_settings_intro,false);
		$tppo->add_sub_tab(3, 4, __('Credits', 'gpress'), $credits_intro,false);
		$tppo->add_sub_tab(3, 5, __('MISC Settings', 'gpress'), $misc_intro,false);
		$tppo->add_sub_tab(3, 6, __('Language / Lingo', 'gpress'), $lang_ling_intro,false);
		$tppo->add_sub_tab(3, 7, __('Sitewide Options', 'gpress'), $sitewide_intro,false);
	}
	if($deactivate_foursquare == 'yes') {
		$tppo->add_sub_tab(4, 1, __('Foursquare', 'gpress'), $foursquare_intro, false);
	}else{
		$tppo->add_sub_tab(4, 1, __('Foursquare', 'gpress'), $foursquare_intro);
	}
	if($gpress_bp) {
		$tppo->add_sub_tab(5, 1, __('BP Components', 'gpress'), $bpcomp_intro);
	}else{
		$tppo->add_sub_tab(5, 1, __('BP Components', 'gpress'), $bpcomp_intro, false);
	}
	/* END OF TAB INCLUSION */

	/* THESE ARE THE FUNCTIONS THAT ALLOW OUR THEME DEVLOPERS TO ADD LISTS OF OPTIONS TO OUR THEME OPTION FRAMEWORK */
	require_once( GPRESS_DIR . '/gpress-admin/config/gpress_options_lists.php' );
	
	/* THESE ARE THE FUNCTIONS THAT ALLOW OUR THEME DEVLOPERS TO EASILY CREATE THEIR OWN THEME OPTION FORMS */
	require_once( GPRESS_DIR . '/gpress-admin/config/gpress_options_fields.php' );

	// This function allows developers to add and remove options from above and then updates the array...
	$tppo->consolidate_options_db();	
	
}
add_action( 'plugins_loaded', 'initialize_gpress_tppo', 3 );

function override_tppo_config(){
	
	global $tppo;
	
	$twitter_url = $tppo->get_tppo('twitter_url', 'blogs');
	$facebook_url = $tppo->get_tppo('facebook_url', 'blogs');
	$rss_url = $tppo->get_tppo('rss_url', 'blogs');
	
	$override_config = array();
	
	if(!empty($twitter_url)) {
		$override_config['twitter_url']	= $twitter_url;
	}
	if(!empty($facebook_url)) {
		$override_config['facebook_url'] = $facebook_url;
	}
	if(!empty($rss_url)) {
		$override_config['rss_url']	= $rss_url;
	}
		
	$override_config = array_merge($tppo->config, $override_config);
	
	$tppo->initialize_tppo_config($override_config);
}
add_action( 'tppo_before_form', 'override_tppo_config' );

?>