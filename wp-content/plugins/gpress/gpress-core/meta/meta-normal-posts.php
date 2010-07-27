<div class="gpress_places_meta_control">

	<?php
    
	global $post, $tppo;
	
    $this_map_id = '_gpress_posts';
	$meta = get_post_meta($post->ID,'_gpress_posts',TRUE);
	
	$gpress_post_lat = get_post_meta($post->ID,'geo_latitude',TRUE);
	$gpress_post_lng = get_post_meta($post->ID,'geo_longitude',TRUE);
	
	if(empty($gpress_post_lat)) {
		$empty_latlng = true;
	}else{
		if(empty($gpress_post_lng)) {
			$empty_latlng = true;	
		}
	}
	
	$this_map_position = ''.$gpress_post_lat.', '.$gpress_post_lng.'';
	
    $gpress_meta = get_post_meta($post->ID,'_gpress_posts',TRUE);
	$this_map_type = $gpress_meta['type'];
    $this_map_zoom = $gpress_meta['zoom'];
	
	if(empty($this_map_type)) {
		$default_map_type = $tppo->get_tppo('default_map_type', 'blogs');
		$this_map_type = $default_map_type;
	}
	if(empty($this_map_zoom)) {
		$default_map_zoom = $tppo->get_tppo('default_map_zoom', 'blogs');
		$this_map_zoom = $default_map_zoom;
	}
    
    gpress_geoform($this_map_id, $this_map_type, $this_map_zoom, $this_map_position, $empty_latlng);
	
	$short_url = get_bloginfo('url').'/?p='.$post->ID.'';
	$short_url_length = strlen($short_url) +3;
	$remaining_twitter_characters = 140 - $short_url_length;
    
    ?>
 
	<p>&nbsp;</p>
 
</div>