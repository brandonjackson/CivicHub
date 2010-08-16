<?php

global $tppobp;

/* THESE ARE THE FUNCTIONS THAT ALLOW OUR THEME DEVLOPERS TO EASILY CREATE THEIR OWN THEME OPTION FORMS */

	// Your Location - 1/1
$tppobp->add_option('user_bp_location', 'users', 1, 1, 1, 'gmap',  __('Your Location', 'gpress'), '', '');

	// Settings - 2/1
$tppobp->add_option('user_bp_profile', 'users', 2, 1, 1, 'radio_button',  __('Show Your Locations on Your Profile', 'gpress'), '', 'ABOVE', $bp_users_profile_list);
$tppobp->add_option('user_bp_profile_address', 'users', 2, 1, 2, 'radio_button',  __('Show Your Closest Address on Your Profile Page', 'gpress'), '', 'enabled', $bp_enabled_list);
$tppobp->add_option('user_bp_activity', 'users', 2, 1, 3, 'radio_button',  __('Show Your Geo-Tagged Posts on Your Activity Page', 'gpress'), '', 'enabled', $bp_enabled_list);

/* END OF FIELD FUNCTIONS */

?>