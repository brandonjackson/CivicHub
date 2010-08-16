<?php

function gpress_shortcode($map_settings, $content = null) {
	
	global $tppo;
	$places_taxonomy = __( 'place', 'gpress' );
	$credits_for_shortcodes = $tppo->get_tppo('credits_for_shortcodes', 'blogs');
	$credits_for_foursquare = $tppo->get_tppo('credits_for_foursquare', 'blogs');
	$deactivate_foursquare = $tppo->get_tppo('deactivate_foursquare', 'blogs');
	if(empty($credits_for_shortcodes)) {
		$credits_for_shortcodes = 'enabled';
	}
	if(empty($credits_for_foursquare)) {
		$credits_for_foursquare = 'enabled';
	}
	if(empty($deactivate_foursquare)) {
		$deactivate_foursquare = 'no';
	}
	
	// gPress Place Markers
	$marker_places_icon = $tppo->get_tppo('marker_places_icon', 'blogs');
	$marker_places_shadow = $tppo->get_tppo('marker_places_shadow', 'blogs');
	$marker_places_icon_file = $marker_places_icon['filename'];
	$marker_places_icon_url = $marker_places_icon['fileurl'];
	$marker_places_shadow_file = $marker_places_shadow['filename'];
	$marker_places_shadow_url = $marker_places_shadow['fileurl'];
	if(!empty($marker_places_icon_url)) {
		$default_marker_icon_place = $marker_places_icon_url;
	}else{
		if(!empty($marker_places_icon_file)) {
			$default_marker_icon_place = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_places_icon_file;
		} else {
			$default_marker_icon_place = GPRESS_URL.'/gpress-core/images/markers/place.png';
		}
	}
	if(!empty($marker_places_shadow_url)) {
		$default_marker_shadow_place = $marker_places_shadow_url;
	}else{
		if(!empty($marker_places_shadow_file)) {
			$default_marker_shadow_place = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_places_shadow_file;
		} else {
			$default_marker_shadow_place = GPRESS_URL.'/gpress-core/images/markers/bg.png';
		}
	}
	
	// gPress Foursquare Your Markers
	$marker_4sqyou_icon = $tppo->get_tppo('marker_4sqyou_icon', 'blogs');
	$marker_4sqyou_shadow = $tppo->get_tppo('marker_4sqyou_shadow', 'blogs');
	$marker_4sqyou_icon_file = $marker_4sqyou_icon['filename'];
	$marker_4sqyou_icon_url = $marker_4sqyou_icon['fileurl'];
	$marker_4sqyou_shadow_file = $marker_4sqyou_shadow['filename'];
	$marker_4sqyou_shadow_url = $marker_4sqyou_shadow['fileurl'];
	if(!empty($marker_4sqyou_icon_url)) {
		$default_marker_icon_4sqyou = $marker_4sqyou_icon_url;
	}else{
		if(!empty($marker_4sqyou_icon_file)) {
			$default_marker_icon_4sqyou = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_4sqyou_icon_file;
		} else {
			$default_marker_icon_4sqyou = GPRESS_URL.'/gpress-core/images/markers/4sq.png';
		}
	}
	if(!empty($marker_4sqyou_shadow_url)) {
		$default_marker_shadow_4sqyou = $marker_4sqyou_shadow_url;
	}else{
		if(!empty($marker_4sqyou_shadow_file)) {
			$default_marker_shadow_4sqyou = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_4sqyou_shadow_file;
		} else {
			$default_marker_shadow_4sqyou = GPRESS_URL.'/gpress-core/images/markers/bg.png';
		}
	}
	
	// gPress Foursquare Friends Markers
	$marker_4sqfriends_icon = $tppo->get_tppo('marker_4sqfriends_icon', 'blogs');
	$marker_4sqfriends_shadow = $tppo->get_tppo('marker_4sqfriends_shadow', 'blogs');
	$marker_4sqfriends_icon_file = $marker_4sqfriends_icon['filename'];
	$marker_4sqfriends_icon_url = $marker_4sqfriends_icon['fileurl'];
	$marker_4sqfriends_shadow_file = $marker_4sqfriends_shadow['filename'];
	$marker_4sqfriends_shadow_url = $marker_4sqfriends_shadow['fileurl'];
	if(!empty($marker_4sqfriends_icon_url)) {
		$default_marker_icon_4sqfriend = $marker_4sqfriends_icon_url;
	}else{
		if(!empty($marker_4sqfriends_icon_file)) {
			$default_marker_icon_4sqfriend = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_4sqfriends_icon_file;
		} else {
			$default_marker_icon_4sqfriend = GPRESS_URL.'/gpress-core/images/markers/4sq.png';
		}
	}
	if(!empty($marker_4sqfriends_shadow_url)) {
		$default_marker_shadow_4sqfriend = $marker_4sqfriends_shadow_url;
	}else{
		if(!empty($marker_4sqfriends_shadow_file)) {
			$default_marker_shadow_4sqfriend = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_4sqfriends_shadow_file;
		} else {
			$default_marker_shadow_4sqfriend = GPRESS_URL.'/gpress-core/images/markers/bg.png';
		}
	}
	
	if(is_array($map_settings)) {
		
		// Default Shortcode Map Settings
		$map_settings_default = array(
			'map_id' 				=> '_shortcode',
			'marker_description'	=> $content,
			'four_you'				=> false,
			'four_friends'			=> false,
			'post_type' 			=> 'shortcode'
		);
		$map_settings = array_merge($map_settings_default,$map_settings);
		extract($map_settings);
		
		if($map_id == '_foursquare') {
			
			if(phpversion() < 5) { 
			
				$php_warning = __('YOU MUST HAVE PHP5+ TO USE FOURSQUARE FUNCTIONALITY', 'gpress');
				echo '<p style="font-weight:bold !important;">'.$php_warning.'</p>';
			
			} elseif($deactivate_foursquare == 'yes') {
				
				$four_warning = __('FOURSQUARE FUNCTIONALITY HAS BEEN TEMPORARILY DEACTIVATED', 'gpress');
				echo '<p style="font-weight:bold !important;">'.$four_warning.'</p>';
				
			} else {
				
				$foursquare_array_you = array();
				$foursquare_array_friends = array();
				
				global $tppo, $consumer_key, $consumer_secret, $my_oauth_token, $my_oauth_token_secret;
				
				$consumer_key = $tppo->get_tppo('foursquare_key', 'blogs');
				$consumer_secret = $tppo->get_tppo('foursquare_secret', 'blogs');
				$oauth_info = $tppo->get_tppo('foursquare_auth', 'blogs');
				$my_oauth_token = $oauth_info['oauth_token'];
				$my_oauth_token_secret = $oauth_info['oauth_secret'];
				
				require_once(GPRESS_DIR . '/gpress-admin/fieldtypes/4square/EpiCurl.php');
				require_once(GPRESS_DIR . '/gpress-admin/fieldtypes/4square/EpiOAuth.php');
				require_once(GPRESS_DIR . '/gpress-admin/fieldtypes/4square/EpiFoursquare.php');
				require_once(GPRESS_DIR . '/gpress-admin/fieldtypes/4square/tpp4sq_functions.php');
				$four_you_array = tpp4sq_user(false, false, false);
				
				$four_your_id = $four_you_array['user']['id'];
				$four_your_name = $four_you_array['user']['firstname'];
				$four_your_checkin_id = $four_you_array['user']['checkin']['id'];
				$four_your_checkin_shout = $four_you_array['user']['checkin']['shout'];
				$four_your_checkin_shout = trim_me($four_your_checkin_shout, 140, '[...]');
				if(empty($four_your_checkin_shout)) {
					$four_your_checkin_shout = ''.$four_your_name.' '.__('did not have the time or patience to write a shout-out when checking-in to this location...', 'gpress');
				}
				$four_your_checkin_display = $four_you_array['user']['checkin']['display'];
				$off_grid_text = '[off the grid]';
				$off_grid = strpos($four_your_checkin_display, $off_grid_text);
				$four_your_location_lat = $four_you_array['user']['checkin']['venue']['geolat'];
				$four_your_location_lng = $four_you_array['user']['checkin']['venue']['geolong'];
				if(empty($four_your_location_lat)) {
					$off_grid = true;
				}
				if(empty($four_your_location_lng)) {
					$off_grid = true;
				}
				$four_your_location_latlng = ''.$four_your_location_lat.', '.$four_your_location_lng.'';
				$four_your_location_title = $four_you_array['user']['checkin']['venue']['name'];
				$four_your_location_venue_id = $four_you_array['user']['checkin']['venue']['id'];
				$four_your_location_venue_url = 'http://foursquare.com/venue/'.$four_your_location_venue_id.'';
				$four_your_location_user_url = 'http://foursquare.com/user/-'.$four_your_id.'';
				$four_your_location_address = $four_you_array['user']['checkin']['venue']['address'];
				$four_your_location_city = $four_you_array['user']['checkin']['venue']['city'];
				$four_your_location_state = $four_you_array['user']['checkin']['venue']['state'];
				$got_address = true;
				if(empty($four_your_location_state)) {
					if(empty($four_your_location_city)) {
						if(empty($four_your_location_address)) {
							$four_your_location_full_address = __('was not given an address', 'gpress');
							$got_address = false;
						}
					}
				}
				if($got_address) {
					$four_your_location_full_address = '';
					if(!empty($four_your_location_address)) {
						$four_your_location_full_address .= ''.$four_your_location_address.'';
					}
					if(!empty($four_your_location_city)) {
						$four_your_location_full_address .= ', '.$four_your_location_city.'';
					}
					if(!empty($four_your_location_state)) {
						$four_your_location_full_address .= ', '.$four_your_location_state.'';
					}
				}
				$four_your_photo = $four_you_array['user']['photo'];
				
				$foursquare_array_you = array();
				$foursquare_array_you[$four_your_checkin_id]['id'] = $four_your_checkin_id;
				$foursquare_array_you[$four_your_checkin_id]['name'] = $four_your_name;
				$foursquare_array_you[$four_your_checkin_id]['shout'] = $four_your_checkin_shout;
				$foursquare_array_you[$four_your_checkin_id]['latlng'] = $four_your_location_latlng;
				$foursquare_array_you[$four_your_checkin_id]['title'] = $four_your_location_title;
	
				$foursquare_array_you[$four_your_checkin_id]['venue_url'] = $four_your_location_venue_url;
				$foursquare_array_you[$four_your_checkin_id]['user_url'] = $four_your_location_user_url;
				$foursquare_array_you[$four_your_checkin_id]['address'] = $four_your_location_full_address;
				$foursquare_array_you[$four_your_checkin_id]['photo'] = $four_your_photo;
				if($off_grid) {
					$foursquare_array_you[$four_your_checkin_id]['hidden'] = 'yes';
				}else{
					$foursquare_array_you[$four_your_checkin_id]['hidden'] = 'no';
				}
				$foursquare_array_you[$four_your_checkin_id]['is_you'] = 'yes';
				$foursquare_array_you[$four_your_checkin_id]['four_type'] = 'person';
				$foursquare_array_you[$four_your_checkin_id]['icon'] = $default_marker_icon_4sqyou;
				$foursquare_array_you[$four_your_checkin_id]['shadow'] = $default_marker_shadow_4sqyou;
				
				$foursquare_array_friends = array();
				$four_friends_array = tpp4sq_checkins(false, false, false);
				
				$foursquare_friends = $four_friends_array['checkins'];
				if(is_array($foursquare_friends)) {
					foreach($foursquare_friends as $fourkey => $friend) {
						$four_friend_id = $friend['user']['id'];
						if($four_friend_id !== $four_your_id) {
							$four_friend_name = $friend['user']['firstname'];
							$four_friend_checkin_id = $friend['id'];
							$four_friend_checkin_shout = $friend['shout'];
							$four_friend_checkin_shout = trim_me($four_friend_checkin_shout, 140, '[...]');
							if(empty($four_friend_checkin_shout)) {
								$four_friend_checkin_shout = ''.$four_friend_name.' '.__('did not have the time or patience to write a shout-out when checking-in to this location...', 'gpress');
							}
							$four_friend_checkin_display = $friend['display'];
							$off_grid_text = '[off the grid]';
							$off_grid = strpos($four_friend_checkin_display, $off_grid_text);
							$four_friend_location_lat = $friend['venue']['geolat'];
							if(empty($four_friend_location_lat)) {
								$off_grid = true;
							}
							$four_friend_location_lng = $friend['venue']['geolong'];
							if(empty($four_friend_location_lng)) {
								$off_grid = true;
							}
							$four_friend_location_latlng = ''.$four_friend_location_lat.', '.$four_friend_location_lng.'';
							$four_friend_location_title = $friend['venue']['name'];
							$four_friend_location_venue_id = $friend['venue']['id'];
							$four_friend_location_venue_url = 'http://foursquare.com/venue/'.$four_friend_location_venue_id.'';
							$four_friend_location_user_url = 'http://foursquare.com/user/-'.$four_friend_id.'';
							$four_friend_location_address = $friend['venue']['address'];
							$four_friend_location_city = $friend['venue']['city'];
							$four_friend_location_state = $friend['venue']['state'];
							$got_address = true;
							if(empty($four_friend_location_state)) {
								if(empty($four_friend_location_city)) {
									if(empty($four_friend_location_address)) {
										$four_friend_location_full_address = __('was not given an address', 'gpress');
										$got_address = false;
									}
								}
							}
							if($got_address) {
								$four_friend_location_full_address = '';
								if(!empty($four_friend_location_address)) {
									$four_friend_location_full_address .= ''.$four_friend_location_address.'';
								}
								if(!empty($four_friend_location_city)) {
									$four_friend_location_full_address .= ', '.$four_friend_location_city.'';
								}
								if(!empty($four_friend_location_state)) {
									$four_friend_location_full_address .= ', '.$four_friend_location_state.'';
								}
							}
							$four_friend_photo = $friend['user']['photo'];
							$foursquare_array_friends[$four_friend_checkin_id]['id'] = $four_friend_checkin_id;
							$foursquare_array_friends[$four_friend_checkin_id]['name'] = $four_friend_name;
							$foursquare_array_friends[$four_friend_checkin_id]['shout'] = $four_friend_checkin_shout;
							$foursquare_array_friends[$four_friend_checkin_id]['latlng'] = $four_friend_location_latlng;
							$foursquare_array_friends[$four_friend_checkin_id]['title'] = $four_friend_location_title;
							$foursquare_array_friends[$four_friend_checkin_id]['venue_url'] = $four_friend_location_venue_url;
							$foursquare_array_friends[$four_friend_checkin_id]['user_url'] = $four_friend_location_user_url;
							$foursquare_array_friends[$four_friend_checkin_id]['address'] = $four_friend_location_full_address;
							$foursquare_array_friends[$four_friend_checkin_id]['photo'] = $four_friend_photo;
							if($off_grid) {
								$foursquare_array_friends[$four_friend_checkin_id]['hidden'] = 'yes';
							}else{
								$foursquare_array_friends[$four_friend_checkin_id]['hidden'] = 'no';
							}
							$foursquare_array_friends[$four_friend_checkin_id]['is_you'] = 'no';
							$foursquare_array_friends[$four_friend_checkin_id]['four_type'] = 'person';
							$foursquare_array_friends[$four_friend_checkin_id]['icon'] = $default_marker_icon_4sqfriend;
							$foursquare_array_friends[$four_friend_checkin_id]['shadow'] = $default_marker_shadow_4sqfriend;
						}
					}
					
					$map_settings = array(
						'map_id' 				=> '_foursquare',
						'map_position'			=> $four_your_location_latlng,
						'four_you' 				=> $four_you,
						'four_you_array'		=> $foursquare_array_you,
						'four_friends'			=> $four_friends,
						'four_friends_array'	=> $foursquare_array_friends,
						'map_height' 			=> $map_height,
						'map_type' 				=> $map_type,
						'map_zoom' 				=> $map_zoom
					);
					
					ob_start();
					gpress_add_map($map_settings);
					if($credits_for_foursquare == 'enabled') {
						echo '<span style="font-size:11px; color:#999;">This map was generated using <a href="http://wordpress.org/extend/plugins/gpress/">gPress</a> and <a href="http://foursquare.com">Foursquare</a></span><p>&nbsp;</p>';
					}
					$content = ob_get_clean();
					return $content;
				
				}else{
					ob_start();
					echo '<p style="font-weight:bold !important;">There appears to be a problem with the Foursquare integration...</p>';
					$content = ob_get_clean();
					return $content;
				}
			
			}
			
		} else {
		
			if($place_id == 'all') {
				
			echo 'here?';
				$place_array = array();
				if(empty($max_places)) {
					query_posts('post_type='.$places_taxonomy.'');				
				}else{
					query_posts('post_type='.$places_taxonomy.'&showposts='.$max_places.'');
				}
				if ( have_posts() ) : while ( have_posts() ) : the_post();
					global $post;
					$meta = get_post_meta($post->ID,'_gpress_places',TRUE);
					$place_array[$post->ID]['latlng'] = $meta['latlng'];
					$place_array[$post->ID]['id'] = $post->ID;
					$place_array[$post->ID]['title'] = get_the_title();
					$place_array[$post->ID]['url'] = get_permalink();
					$place_array[$post->ID]['address'] = $meta['address'];
					$place_array[$post->ID]['icon_url'] = $meta['icon_url'];
					$place_array[$post->ID]['icon_file'] = $meta['icon_file'];
					$place_array[$post->ID]['shadow_url'] = $meta['shadow_url'];
					$place_array[$post->ID]['shadow_file'] = $meta['shadow_file'];
					$place_array[$post->ID]['default_icon'] = $default_marker_icon_place;
					$place_array[$post->ID]['default_shadow'] = $default_marker_shadow_place ;
					if(empty($map_position)) {
						$map_position = $meta['latlng'];
					}
				endwhile; else:
				endif;
				wp_reset_query();

				$map_settings = array(
					'map_id'		=> '_gpress_places',
					'map_height' 	=> $map_height,
					'map_type' 		=> $map_type,
					'map_zoom' 		=> $map_zoom,
					'map_position' 	=> $map_position,
					'marker_icon' 	=> $marker_icon,
					'marker_shadow' => $marker_shadow,
					'post_type'		=> $places_taxonomy,
					'place_id'		=> $place_array
				);
				extract($map_settings);
			}
		
			ob_start();
			gpress_add_map($map_settings);
			if($credits_for_shortcodes == 'enabled') {
				echo '<span style="font-size:11px; color:#999;">This map was generated using <a href="http://wordpress.org/extend/plugins/gpress/">gPress</a></span><p>&nbsp;</p>';
			}
			$content = ob_get_clean();
			return $content;
		
		}
		
	}
}

function gpress_shortcode_display($map_settings_display, $content = null) {
				
		if(is_array($map_settings_display)) {
			extract($map_settings_display);
		}
		
		$display_content = '<span class="gpress_shortcode_display">[gpress';
		
		if(!empty($map_id)) {
			$display_content .= ' map_id="'.$map_id.'"';	
		}
		if(!empty($four_you)) {
			$display_content .= ' four_you="'.$four_you.'"';	
		}
		if(!empty($four_friends)) {
			$display_content .= ' four_friends="'.$four_friends.'"';	
		}
		if(!empty($map_position)) {
			$display_content .= ' map_position="'.$map_position.'"';	
		}
		if(!empty($marker_title)) {
			$display_content .= ' marker_title="'.$marker_title.'"';	
		}																	
		if(!empty($map_height)) {
			$display_content .= ' map_height="'.$map_height.'"';	
		}
		if(!empty($map_type)) {
			$display_content .= ' map_type="'.$map_type.'"';	
		}
		if(!empty($map_zoom)) {
			$display_content .= ' map_zoom="'.$map_zoom.'"';	
		}
		if(!empty($marker_icon)) {
			$display_content .= ' marker_icon="'.$marker_icon.'"';	
		}
		if(!empty($marker_shadow)) {
			$display_content .= ' marker_shadow="'.$marker_shadow.'"';	
		}
		if(!empty($place_id)) {
			$display_content .= ' place_id="'.$place_id.'"';	
		}
		
		$display_content .= ']';
		
		if(!empty($content)) {
			$display_content .= ''.$content.'[/gpress]';
		}
		$display_content .= '</span>';
		
		return $display_content;

}

?>