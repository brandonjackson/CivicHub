<?php
	
global $tppo, $tppobp;

/* CHECK TO SEE WHICH GEO-SETTINGS ARE NEEDED */
global $tppo;
$user_rights = $tppo->get_tppo('user_rights', 'sitewide');
if(empty($user_rights)) {
	$user_rights = 'individual';
}

/* ADVANCED CONFIGURATION OPTIONS */
require_once( GPRESS_DIR . '/gpress-bp/user-admin/gpress_bp_options_config.php' );

// THESE STORE THE INTRODUCTIONS FOR THE SUB TABS
require_once( GPRESS_DIR . '/gpress-bp/user-admin/gpress_bp_options_descriptions.php' );

/* ADD TOP TABS AND SIDE TABS TO THEME OPTIONS */

  // Top Tabs
$tppobp->add_tab(1, __('Your Location', 'gpress'),true);
if($user_rights == 'individual') {
	$tppobp->add_tab(2, __('Your Settings', 'gpress'),true);
}else{
	$tppobp->add_tab(2, __('Your Settings', 'gpress'),false);
}

  // Side Tabs
$tppobp->add_sub_tab(1, 1, __('Your Location', 'gpress'), $bp_your_location_intro, true);
if($user_rights == 'individual') {
	$tppobp->add_sub_tab(2, 1, __('Your Setting', 'gpress'), $bp_your_settings_intro, true);
}else{
	$tppobp->add_sub_tab(2, 1, __('Your Setting', 'gpress'), $bp_your_settings_intro, false);
}

/* END OF TAB INCLUSION */

/* THESE ARE THE FUNCTIONS THAT ALLOW OUR THEME DEVLOPERS TO ADD LISTS OF OPTIONS TO OUR THEME OPTION FRAMEWORK */
require_once( GPRESS_DIR . '/gpress-bp/user-admin/gpress_bp_options_lists.php' );

/* THESE ARE THE FUNCTIONS THAT ALLOW OUR THEME DEVLOPERS TO EASILY CREATE THEIR OWN THEME OPTION FORMS */
require_once( GPRESS_DIR . '/gpress-bp/user-admin/gpress_bp_options_fields.php' );

// This function allows developers to add and remove options from above and then updates the array...
$tppobp->consolidate_options_db();	

?>