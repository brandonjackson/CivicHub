<?php

function gpress_init() {
	
	include ( GPRESS_DIR . '/gpress-core/gpress-init.php' );
	
	global $tppo;
	$use_places = $tppo->get_tppo('use_places', 'blogs');
	$place_control = $tppo->get_tppo('place_control', 'sitewide');
	if(empty($use_geopost)) {
		$use_geopost = 'enabled';
	}
	if(empty($place_control)) {
		$place_control = 'default';
	}
	if($place_control == 'master') {
		$use_places = $tppo->get_tppo('use_places', 'blogs', 1);
		if(empty($use_places)) {
			$use_places = 'enabled';
		}
	}elseif($place_control == 'alone') {
		global $blog_id;
		if($blog_id !== 1) {
			$use_places = 'disabled';
		}
	}
	$gpress_places = $tppo->get_tppo('gpress_places', 'sitewide');
	if(empty($gpress_places)) {
		$gpress_places = 'default';
	}
	if($gpress_places == 'hide') {
		if(is_super_admin()) {
			$use_places = $use_geopost;
		}else{
			$use_places = 'disabled';				
		}
	}
	
	$places_plural_name = __( 'Places', 'gpress' );
	$places_singular_name = __( 'Place', 'gpress' );
	$places_add_new_sentence = __( 'Add New Place', 'gpress' );
	$places_taxonomy = __( 'place', 'gpress' );
	$places_description = __( 'Places are represented as a marker on a map...', 'gpress' );
	
	if($use_places == 'enabled') {
		
		$labels_places = array(
			'name' 					=> _x($places_plural_name, 'post type general name'),
			'singular_name' 		=> _x($places_singular_name, 'post type singular name'),
			'add_new' 				=> _x($places_add_new_sentence, $places_taxonomy),
			'add_new_item' 			=> __('Add New Place', 'gpress'),
			'edit_item' 			=> __('Edit Place', 'gpress'),
			'new_item' 				=> __('New Place', 'gpress'),
			'view_item' 			=> __('View Place', 'gpress'),
			'search_items' 			=> __('Search Places', 'gpress'),
			'not_found' 			=> __('No places found', 'gpress'),
			'not_found_in_trash'	=> __('No places found in Trash', 'gpress'), 
			'parent_item_colon' 	=> ''
		);
		$supports_places = array(
			'title',
			//'editor',
			'author',
			'thumbnail',
			//'excerpt',
			'trackbacks',
			'custom-fields',
			'comments',
			//'revisions',
			//'page-attributes'
		);
		$args_places = array(
			'labels' 				=> $labels_places,
			'description' 			=> $places_description,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'exclude_from_search' 	=> false,
			'show_ui' 				=> true, 
			'query_var' 			=> true,
			'rewrite' 				=> true,
			'capability_type' 		=> 'post',
			'hierarchical' 			=> true,
			'menu_position' 		=> null,
			'supports' 				=> $supports_places,
			//'register_meta_box_cb'	=> 'gpress_meta_box_places',
			'menu_position'			=> 5,
			'menu_icon'				=> ''.GPRESS_URL.'/gpress-core/images/icons/admin_menu_places.png'
		);
		register_post_type($places_taxonomy,$args_places);
		flush_rewrite_rules();
	}
	
}

function gpress_updated_messages( $messages ) {
	
	$places_taxonomy = __( 'place', 'gpress' );

	$messages[$places_taxonomy] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Place updated. <a href="%s">View place</a>', 'gpress'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.', 'gpress'),
		3 => __('Custom field deleted.', 'gpress'),
		4 => __('Place updated.', 'gpress'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Place restored to revision from %s', 'gpress'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Place published. <a href="%s">View place</a>', 'gpress'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Place saved.', 'gpress'),
		8 => sprintf( __('Place submitted. <a target="_blank" href="%s">Preview place</a>', 'gpress'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Place scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview place</a>', 'gpress'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Place draft updated. <a target="_blank" href="%s">Preview place</a>', 'gpress'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);

	return $messages;
  
}

?>