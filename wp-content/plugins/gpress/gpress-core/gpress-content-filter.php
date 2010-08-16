<?php

function gpress_content_filter($content) {
	
	global $tppo, $post;
	$add_map = false;
	$places_taxonomy = __( 'place', 'gpress' );
	
	// Existing Post Info (if editing post)
	$gpress_post_type = $post->post_type;
	$gpress_post_id = $post->ID;
	$gpress_post_title = $post->post_title;	
	//echo '$gpress_post_type = '.$gpress_post_type.''; exit;

	// gPress Options
	$credits_for_posts = $tppo->get_tppo('credits_for_posts', 'blogs');
	$credits_for_places = $tppo->get_tppo('credits_for_places', 'blogs');
	$default_map_height = $tppo->get_tppo('default_map_height', 'blogs');
	$default_map_type = $tppo->get_tppo('default_map_type', 'blogs');
	$default_map_zoom = $tppo->get_tppo('default_map_zoom', 'blogs');
	$remove_from_content = $tppo->get_tppo('remove_from_content', 'blogs');
	
	// Default Settings
	$gpress_default_map_height = '450';
	if(!empty($default_map_height)) {
		$gpress_default_map_height = $default_map_height;
	}
	$gpress_default_map_type = 'ROADMAP';
	if(!empty($default_map_type)) {
		$gpress_default_map_type = $default_map_type;
	}
	$gpress_default_map_zoom = '13';
	if(!empty($default_map_zoom)) {
		$gpress_default_map_zoom = $default_map_zoom;
	}
	if(empty($remove_from_short_content)) {
		$remove_from_short_content = 'no';
	}
	
	// POSTS
	if($gpress_post_type == 'post') {
		
		$gpress_map_id = '_gpress_posts';
		$geo_public = get_post_meta($gpress_post_id,'geo_public',TRUE);
		$geo_latitude = get_post_meta($gpress_post_id,'geo_latitude',TRUE);
		$geo_longitude = get_post_meta($gpress_post_id,'geo_longitude',TRUE);
		$gpress_map_position = ''.$geo_latitude.', '.$geo_longitude.'';
		
		if($geo_public == 1) {
			$add_map = true;
		}
		
	}
	
	// PLACES
	if($gpress_post_type == $places_taxonomy) {
		
		$gpress_map_id = '_gpress_places';
		$meta = get_post_meta($gpress_post_id,$gpress_map_id,TRUE);
		$gpress_map_position = $meta['latlng'];
		$gpress_map_type = $meta['type'];
		$gpress_map_zoom = $meta['zoom'];
		
		$add_map = true;
		
	}
	
	// Final check for empty fields...
	if(empty($gpress_map_type)) {
		$gpress_map_type = $gpress_default_map_type;
	}
	if(empty($gpress_map_zoom)) {
		$gpress_map_zoom = $gpress_default_map_zoom;
	}
	
	// ADD MAP
	if($add_map) {
		
		// Map Settings for Places
		$map_settings = array(
			'map_id' 		=> $gpress_map_id,
			'map_height' 	=> $gpress_map_height,
			'map_type' 		=> $gpress_map_type,
			'map_zoom' 		=> $gpress_map_zoom,
			'map_position' 	=> $gpress_map_position,
			'post_type' 	=> $gpress_post_type,
			'post_id' 		=> $gpress_post_id,
			'widget_id' 	=> false,
			'place_id' 		=> $gpress_post_id,
			'marker_icon' 	=> $gpress_icon_url,
			'marker_shadow' => $gpress_shadow_url,
			'marker_title' 	=> $gpress_post_title,
			'marker_url' 	=> false
		);
	}	
	
	$show_map = true;
	
	if((!is_single()) && (!is_page())) {
		if($remove_from_content == 'yes') {
			$show_map = false;
		}
	}
	
	if(is_feed()) {
		$show_map = false;
	}
	
	if($show_map) {
	
		// DISPLAY MAP AND CREDITS
		if($gpress_post_type == $places_taxonomy) {
			ob_start();
			gpress_add_map($map_settings);
			if($credits_for_places == 'enabled') {
				echo '<span style="font-size:11px; color:#999;">This map was generated using <a href="http://wordpress.org/extend/plugins/gpress/">gPress</a></span><p>&nbsp;</p>';
			}
			$content = ob_get_clean();
			return $content;
		}elseif($gpress_post_type == 'post') {
			if($geo_public == 1) {
				ob_start();
				echo $content;
				gpress_add_map($map_settings);
				if($credits_for_posts == 'enabled') {
					echo '<span style="font-size:11px; color:#999;">This map was auto-generated using <a href="http://wordpress.org/extend/plugins/gpress/">gPress</a> with Automattic\'s <a href="http://blackberry.wordpress.org">BlackBerry</a>, <a href="http://android.wordpress.org">Android</a> or <a href="http://iphone.wordpress.org">iPhone</a> applications...</span><p>&nbsp;</p>';
				}
				$content = ob_get_clean();
				return $content;
			}else{
				return $content;
			}
		}else{
			return $content;	
		}
	
	} else {
		return $content;
	}
}

?>