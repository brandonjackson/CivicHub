<div class="gpress_places_meta_control">
	
    <?php 

	global $post, $tppo;
	
	$gpress_map_id = '_gpress_posts';
	$meta = get_post_meta($post->ID,'_gpress_posts',TRUE);

	$this_map_type = $meta['type'];
	$this_map_zoom = $meta['zoom'];
	
	$default_map_height = $tppo->get_tppo('default_map_height', 'blogs');
	if(empty($default_map_height)) {
		$default_map_height = '450';
	}
	
	if(empty($this_map_type)) {
		$default_map_type = $tppo->get_tppo('default_map_type', 'blogs');
		$meta['type'] = $default_map_type;
	}
	if(empty($this_map_zoom)) {
		$default_map_zoom = $tppo->get_tppo('default_map_zoom', 'blogs');
		$meta['zoom'] = $default_map_zoom;
	}
	
	include( GPRESS_DIR . '/gpress-core/meta/meta-sidebar-content.php');

	?>
 
</div>