<?php
	
global $tppo;

/* ADVANCED CONFIGURATION OPTIONS */
$config_array = array(
	'name' => 'gpress_options_array',
	'icon_url' => GPRESS_URL . '/gpress-core/images/icons/admin_menu_options.png',
	'tab_name' => 'gPress Options',
	'page_title' => 'gPress Options',
	'debug_view' => false,
	'clear_array' => false,
	'brand_name' => 'gPress<br />Managed by PressBuddies',
	'brand_url' => 'http://ur1.my/geo',
	'twitter_url' => 'http://twitter.com/pressbuddies',
	'facebook_url' => 'http://www.facebook.com/pages/PressBuddies/111911188850005',
	'rss_url' => 'http://feeds.feedburner.com/pressbuddies'
);

$tppo = new TPPOptions($config_array);
/* END OF ADVANCED CONFIGURATION OPTIONS */

?>