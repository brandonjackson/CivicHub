<div class="gpress_places_meta_control">

	<?php
	
	global $post, $tppo;
    
    $this_map_id = '_gpress_places';
	$meta = get_post_meta($post->ID,'_gpress_places',TRUE);
	
	$this_map_position = $meta['latlng'];
    $this_map_type = $meta['type'];
    $this_map_zoom = $meta['zoom'];
	
	if(empty($this_map_type)) {
		$default_map_type = $tppo->get_tppo('default_map_type', 'blogs');
		$this_map_type = $default_map_type;
	}
	if(empty($this_map_zoom)) {
		$default_map_zoom = $tppo->get_tppo('default_map_zoom', 'blogs');
		$this_map_zoom = $default_map_zoom;
	}
    
    gpress_geoform($this_map_id, $this_map_type, $this_map_zoom, $this_map_position, false);
	
	global $post;
	$short_url = get_bloginfo('url').'/?p='.$post->ID.'';
	$short_url_length = strlen($short_url) +3;
	$remaining_twitter_characters = 140 - $short_url_length;
	
	$src_text = $meta['description'];
	$description = trim_me($src_text, 140, '[...]');
    
    ?>
 
	<label><?php echo __('Description <span>(PLAIN TEXT ONLY - ALL HTML WILL BE REMOVED)</span>', 'gpress'); ?></label>
 
	<p>
		<textarea id="textarea<?php echo $this_map_id; ?>" name="<?php echo $this_map_id; ?>[description]" rows="3"><?php if(!empty($description)) echo $description; ?></textarea>
		<span><?php echo __('You have <span id="charactersLeft">140</span> characters remaining (should you wish to send to Twitter too - coming soon!)', 'gpress'); ?></span>
	</p>
    
	<script type="text/javascript">

	jQuery(document).ready(function() {
		jQuery('#textarea<?php echo $this_map_id; ?>').bind('textchange', function (event, previousText) {
			jQuery('#charactersLeft').html( <?php echo $remaining_twitter_characters; ?> - parseInt(jQuery(this).val().length) );
		});
	});
	
	</script>
 
</div>