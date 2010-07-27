<?php

/* THESE ARE MISCELLANEOUS FUNCTIONS USED THROUGHT THE PLUGIN */

function trim_me($src, $limit, $added_text) {
	$clean_text = strip_tags($src);
	$src = str_replace("'", "", "$clean_text");
	$src_length = strlen($src);
	if($src_length > $limit) {
		$excerpt = substr($src,0,$limit);
		$pretty_excerpt = ''.$excerpt.' '.$added_text.'';
		if(empty($added_text)) {
			return $excerpt;
		}else{
			return $pretty_excerpt;
		}
	}else{
		return $src;	
	}
}

function get_users_blog_posts( $user_id = 1, $num_per_blog = 1, $orderby = 'date', $sort = 'post_date_gmt', $gpress = true ) {
	$posts = array();
	$i = 0;
	$blogs = get_blogs_of_user($user_id);
	if(is_array($blogs)) {
		// MS SPECIFIC
		foreach ( $blogs as $key => $blog ):
			$blog_id = $blog->userblog_id;
			switch_to_blog($blog_id);
				$get_posts = get_posts('orderby='.$orderby.'&numberposts='.$num_per_blog);
				foreach($get_posts as $key => $the_post) {
					$post_id = $the_post->ID;
					$posts[$i]['blog_id'] = $blog_id;
					$posts[$i]['post_id'] = $post_id;
					$posts[$i]['post_date'] = $the_post->post_date;
					$posts[$i]['post_title'] = $the_post->post_title;
					$posts[$i]['post_url'] = $the_post->guid;
					$posts[$i]['post_type'] = $the_post->post_type;
					$geo_latlng = ''.get_post_meta($post_id,'geo_latitude',TRUE).', '.get_post_meta($post_id,'geo_longitude',TRUE).'';
					$posts[$i]['geo_public'] = get_post_meta($post_id,'geo_public',TRUE);
					$posts[$i]['geo_latlng'] = $geo_latlng;
					$i++;
				}
			restore_current_blog();
		endforeach;
		return $posts;
	}else{
		// STANDARD WP
		$get_posts = get_posts('orderby='.$orderby.'&numberposts='.$num_per_blog);
		foreach($get_posts as $key => $the_post) {
			$post_id = $the_post->ID;
			$posts[$i]['blog_id'] = $blog_id;
			$posts[$i]['post_id'] = $post_id;
			$posts[$i]['post_date'] = $the_post->post_date;
			$posts[$i]['post_title'] = $the_post->post_title;
			$posts[$i]['post_url'] = $the_post->guid;
			$posts[$i]['post_type'] = $the_post->post_type;
			$geo_latlng = ''.get_post_meta($post_id,'geo_latitude',TRUE).', '.get_post_meta($post_id,'geo_longitude',TRUE).'';
			$posts[$i]['geo_public'] = get_post_meta($post_id,'geo_public',TRUE);
			$posts[$i]['geo_latlng'] = $geo_latlng;
			$i++;
		}
		return $posts;
	}
}

function gpress_get_posts( $query ) {
	
	global $tppo;
	$places_taxonomy = __( 'place', 'gpress' );
	$home_loop = $tppo->get_tppo('home_loop', 'blogs');
	$home_loop_method = $tppo->get_tppo('home_loop_method', 'blogs');
	if(empty($home_loop)) {
		$home_loop = 'BOTH';
	}
	if(empty($home_loop_method)) {
		$home_loop_method = 'query';
	}
	
	if($home_loop_method == 'query') {
			
		//print_r($query);
		if($home_loop == 'BOTH') {
			if(!is_single()) {
				if ( $query->is_home ) {
					if(empty($query->query)) {
						$query->set( 'post_type', array('post', $places_taxonomy) );
					}
				}		
			}
		}elseif($home_loop == 'PLACES') {
			if(!is_single()) {
				if ( $query->is_home ) {
					if(empty($query->query)) {
						$query->set( 'post_type', $places_taxonomy );
					}
				}
			}
		}else{
			// DO NOTHING
		}
	
	}
	
	return $query;

}

?>