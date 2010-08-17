<?php
/*
Plugin Name: BuddyPress Sliding Login Panel
Description: Adds a sliding login panel with account center and user menu to BuddyPress.
Author: Sarah Gooding
Author URI: http://untame.net
Plugin URI: http://buddypress.org/community/groups/buddypress-sliding-login-panel
Version: 1.0

License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

*/

function bp_sliding_login_panel_init() {
	require( dirname( __FILE__ ) . '/bp-sliding-login-panel.php' );
}
add_action( 'bp_init', 'bp_sliding_login_panel_init' );
?>