<?php

global $tppo;

/* THESE ARE THE FUNCTIONS THAT ALLOW OUR THEME DEVLOPERS TO EASILY CREATE THEIR OWN THEME OPTION FORMS */
	// Module Control - 1/1
$tppo->add_option('use_geopost', 'blogs', 1, 1, 1, 'radio_button', __('Geo-Tagged Posts', 'gpress'), '', 'enabled', $enabled_list);
$tppo->add_option('use_georss', 'blogs', 1, 1, 2, 'radio_button',  __('geoRSS', 'gpress'), '', 'enabled', $enabled_list);
$tppo->add_option('use_places', 'blogs', 1, 1, 3, 'radio_button',  __('Places', 'gpress'), '', 'enabled', $enabled_list);
	// Geo-Tagged Posts - 1/2
$tppo->add_option('force_geopost', 'blogs', 1, 2, 1, 'radio_button',  __('Force Geo-Tagged Posting Capability', 'gpress'), '', 'disabled', $enabled_list);
	// Map Settings - 2/1
$tppo->add_option('default_map_height', 'blogs', 2, 1, 1, 'text_input',  __('Default Map Height', 'gpress'), __('This is a numbers only field (defaults to 450)', 'gpress'), '450');
$tppo->add_option('default_map_type', 'blogs', 2, 1, 2, 'radio_button',  __('Default Map Type', 'gpress'), '', 'ROADMAP', $default_map_type_list);
$tppo->add_option('default_map_zoom', 'blogs', 2, 1, 3, 'radio_button',  __('Default Map Zoom', 'gpress'), '', '13', $default_map_zoom_list);
	// Map Settings - 2/2
$tppo->add_option('marker_posts_icon', 'blogs', 2, 2, 1, 'text_input',  __('Marker icon URL for Geo-Tageed Posts', 'gpress'), __('This should start with http://', 'gpress'), '');
$tppo->add_option('marker_posts_shadow', 'blogs', 2, 2, 2, 'text_input',  __('Marker shadow URL for Geo-Tageed Posts', 'gpress'), __('This should start with http://', 'gpress'), '');
$tppo->add_option('marker_places_icon', 'blogs', 2, 2, 3, 'text_input',  __('Marker icon URL for Place Post Types', 'gpress'), __('This should start with http://', 'gpress'), '');
$tppo->add_option('marker_places_shadow', 'blogs', 2, 2, 4, 'text_input',  __('Marker shadow URL for Place Post Types', 'gpress'), __('This should start with http://', 'gpress'), '');
$tppo->add_option('marker_favwidget_icon', 'blogs', 2, 2, 5, 'text_input',  __('Marker icon URL for Favorite Place Widgets', 'gpress'), __('This should start with http://', 'gpress'), '');
$tppo->add_option('marker_favwidget_shadow', 'blogs', 2, 2, 6, 'text_input',  __('Marker shadow URL for Favorite Place Widgets', 'gpress'), __('This should start with http://', 'gpress'), '');
	// Loop Settings - 3/1
$tppo->add_option('home_loop', 'blogs', 3, 1, 1, 'radio_button',  __('Homepage Loop', 'gpress'), __('This controls the query_post for the homepage...', 'gpress'), 'BOTH', $home_loop_list);
$tppo->add_option('home_loop_method', 'blogs', 3, 1, 2, 'radio_button',  __('Home Loop Method', 'gpress'), __('There are two methods for getting your homepage loop to accept the new custom post types, we can either inject a query_posts into the header or directly manipulate the query with the pre_get_posts method. Unofrtunately, neither are perfect and both have problems with other plugins or themes that also use these methods, so we have provided you with the choice...', 'gpress'), 'query', $home_loop_method_list);
	// Excerpt Settings - 3/2
$tppo->add_option('remove_from_content', 'blogs', 3, 2, 1, 'radio_button',  __('Remove Maps from Themes Homepage Content Loop', 'gpress'), '', 'no', $no_yes_list);
$tppo->add_option('remove_from_excerpt', 'blogs', 3, 2, 2, 'radio_button',  __('Remove Maps<br />from Excerpts', 'gpress'), '', 'no', $no_yes_list);
	// Brand Settings - 3/3
$tppo->add_option('twitter_url', 'blogs', 3, 3, 1, 'text_input',  __('Twitter URL', 'gpress'), __('Linked to the Twitter icon seen on gPress Options page', 'gpress'), '');
$tppo->add_option('facebook_url', 'blogs', 3, 3, 2, 'text_input',  __('Facebook URL', 'gpress'), __('Linked to the Facebook icon seen on gPress Options page', 'gpress'), '');
$tppo->add_option('rss_url', 'blogs', 3, 3, 3, 'text_input',  __('RSS URL', 'gpress'), __('Linked to the RSS icon seen on gPress Options page', 'gpress'), '');
	// Brand Settings - 3/4
$tppo->add_option('credits_for_posts', 'blogs', 3, 4, 1, 'radio_button',  __('Show credits on geo-tagged posts', 'gpress'), '', 'enabled', $enabled_list);
$tppo->add_option('credits_for_places', 'blogs', 3, 4, 2, 'radio_button',  __('Show credits on place post type maps', 'gpress'), '', 'enabled', $enabled_list);
$tppo->add_option('credits_for_shortcodes', 'blogs', 3, 4, 3, 'radio_button',  __('Show credits on shortcode maps', 'gpress'), '', 'enabled', $enabled_list);
$tppo->add_option('credits_for_foursquare', 'blogs', 3, 4, 4, 'radio_button',  __('Show credits on Foursquare maps', 'gpress'), '', 'enabled', $enabled_list);
	// MISC Settings - 3/5
$tppo->add_option('use_js_in_theme', 'blogs', 3, 5, 1, 'radio_button',  __('Use jQuery 1.4.2 in Theme', 'gpress'), __('In emergencies, you may want to remove the gPress jQuery 1.4.2 from your theme', 'gpress'), 'yes', $yes_no_list);
$tppo->add_option('deactivate_foursquare', 'blogs', 3, 5, 2, 'radio_button',  __('Deactivate Foursquare', 'gpress'), __('The Foursquare oAuth processs has several requirements not all servers support, and failure to connect, or when their API happens to be down means you cannot access your site. If this happens, use this feature to deactivate all Foursquare activity, then refresh this page...', 'gpress'), 'no', $no_yes_list);
	// Language / Lingo Settings - 3/6
$tppo->add_option('gpress_lang_file', 'blogs', 3, 6, 1, 'radio_button',  __('Which Language or Lingo to use? ', 'gpress'), '', 'default', $lang_ling_list);
$tppo->add_option('gpress_custom_lang_file', 'blogs', 3, 6, 2, 'text_input',  __('Name of custom MO ', 'gpress'), __('This file should be located in "gpress/gpress-lang" such as "gpress-default", which is the default setting', 'gpress'), '');
	// Foursquare Settings - 4/1
$tppo->add_option('foursquare_auth', 'blogs', 4, 1, 1, '4sq',  __('Foursquare Auth', 'gpress'), '', '');
$tppo->add_option('foursquare_key', 'blogs', 4, 1, 2, 'text_input',  __('Foursquare Key', 'gpress'), '', '');
$tppo->add_option('foursquare_secret', 'blogs', 4, 1, 3, 'text_input',  __('Foursquare Secret', 'gpress'), '', '');
	// BP Components - 5/1
$tppo->add_option('use_bp_profile', 'blogs', 5, 1, 1, 'radio_button',  __('Show User Locations<br />on BuddyPress Profiles', 'gpress'), '', 'ABOVE', $bp_user_profile_list);
$tppo->add_option('use_bp_profile_address', 'blogs', 5, 1, 2, 'radio_button',  __('Show Closest Address<br />on User\'s Profile Page', 'gpress'), '', 'enabled', $enabled_list);
$tppo->add_option('use_bp_activity', 'blogs', 5, 1, 3, 'radio_button',  __('Show Geo-Tagged Posts on User\'s Activity Page', 'gpress'), '', 'enabled', $enabled_list);
/* END OF FIELD FUNCTIONS */

?>