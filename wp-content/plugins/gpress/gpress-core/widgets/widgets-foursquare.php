<?php

add_action( 'widgets_init', 'gpress_foursquare_load_widgets' );

function gpress_foursquare_load_widgets() {
	register_widget( 'gpress_foursquare_WIDGET' );
}

class gpress_foursquare_WIDGET extends WP_Widget {

	function gpress_foursquare_WIDGET() {
		$widget_ops = array( 'classname' => 'gpress-foursquare', 'description' => __('Add a Foursquare map to your sidebar', 'gpress') );
		$this->WP_Widget( 'gpress-foursquare-widget', __('Foursquare Widget', 'gpress'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$name = $instance['name'];
		$type = $instance['type'];
		$zoom = $instance['zoom'];
		$you = $instance['you'];
		$friends = $instance['friends'];
		$zoom = $instance['zoom'];

		echo $before_widget;

			/* Display name from widget settings if one was input. */
			if ( $name ) {
				echo '<h3 class="widget-title">'.$instance['name'].'</h3>';
			}else{
				echo '<h3 class="widget-title">'.__('Foursquare', 'gpress').'</h3>';
			}
			
			global $tppo;
			$deactivate_foursquare = $tppo->get_tppo('deactivate_foursquare', 'blogs');
			if(empty($deactivate_foursquare)) {
				$deactivate_foursquare = 'no';
			}
					
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
						}
					}
					
					/*
					echo '<pre>';
					print_r($foursquare_array_friends);					
					print_r($four_friends_array);
					echo '</pre>'; exit;
					*/
					
					if($you == 'SHOWYOU') {
						$four_you = true;
					}else{
						$four_you = false;
					}
					if($friends == 'SHOWFRIENDS') {
						$four_friends = true;
					}else{
						$four_friends = false;
					}
					
					$map_settings = array(
						'map_id' 				=> '_foursquare_widget',
						'map_position'			=> $four_your_location_latlng,
						'four_you' 				=> $four_you,
						'four_you_array'		=> $foursquare_array_you,
						'four_friends'			=> $four_friends,
						'four_friends_array'	=> $foursquare_array_friends,
						'map_height' 			=> $height,
						'map_type' 				=> $type,
						'map_zoom' 				=> $zoom
					);
					gpress_add_map($map_settings);
				
				}else{
					echo '<p style="font-weight:bold !important;">'.__('There appears to be a problem with the Foursquare integration...', 'gpress').'</p>';
				}
			
			}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */		
		$instance['name'] = $new_instance['name'];
		$instance['type'] = $new_instance['type'];
		$instance['zoom'] = $new_instance['zoom'];
		$instance['you'] = $new_instance['you'];
		$instance['friends'] = $new_instance['friends'];
		$instance['height'] = $new_instance['height'];

		return $instance;
	}

	function form( $instance ) {

		global $tppo;
		
		$instance = wp_parse_args( (array) $instance ); 
		$default_map_height = $tppo->get_tppo('default_map_height', 'blogs');
		$randomid = mt_rand(0,2147483647);
		
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php echo __('Widget Title:', 'gpress'); ?></label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:95%;" />
		</p>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gpress_meta_sidebar_table">
          <tr>
            <td width="50%">
                <input type="radio" name="<?php echo $this->get_field_name( 'type' ); ?>" value="ROADMAP" <?php if($instance['type'] == 'ROADMAP') { ?> checked="checked" <?php } ?> /><span class="input_label">Roadmap</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'type' ); ?>" value="SATELLITE" <?php if($instance['type'] == 'SATELLITE') { ?> checked="checked" <?php } ?> /><span class="input_label">Satellite</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'type' ); ?>" value="HYBRID" <?php if($instance['type'] == 'HYBRID') { ?> checked="checked" <?php } ?> /><span class="input_label">Hybrid</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'type' ); ?>" value="TERRAIN" <?php if($instance['type'] == 'TERRAIN') { ?> checked="checked" <?php } ?> /><span class="input_label">Terrain</span><br />
            </td>
            <td width="10px" class="divider">&nbsp;</td>
            <td width="50%">
                <input type="radio" name="<?php echo $this->get_field_name( 'zoom' ); ?>" value="18" <?php if($instance['zoom'] == '18') { ?> checked="checked" <?php } ?> /><span class="input_label">Close-Up</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'zoom' ); ?>" value="13" <?php if($instance['zoom'] == '13') { ?> checked="checked" <?php } ?> /><span class="input_label">Nearby</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'zoom' ); ?>" value="10" <?php if($instance['zoom'] == '10') { ?> checked="checked" <?php } ?> /><span class="input_label">Cities</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'zoom' ); ?>" value="5" <?php if($instance['zoom'] == '5') { ?> checked="checked" <?php } ?> /><span class="input_label">Countries</span><br />
            </td>
          </tr>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gpress_meta_sidebar_table">
          <th><?php echo __('YOUR LOCATION', 'gpress'); ?></th>
          <tr>
            <td width="100%">
                <input type="radio" name="<?php echo $this->get_field_name( 'you' ); ?>" value="SHOWYOU" <?php if($instance['you'] == 'SHOWYOU') { ?> checked="checked" <?php } ?> /><span class="input_label">Show Your Location</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'you' ); ?>" value="NOYOU" <?php if($instance['you'] == 'NOYOU') { ?> checked="checked" <?php } ?> /><span class="input_label">Hide Your Location</span><br />
            </td>
            <td width="10px" class="divider">&nbsp;</td>
          </tr>
        </table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="gpress_meta_sidebar_table">
          <th><?php echo __('FRIENDS LOCATIONS', 'gpress'); ?></th>
          <tr>
            <td width="50%">
                <input type="radio" name="<?php echo $this->get_field_name( 'friends' ); ?>" value="SHOWFRIENDS" <?php if($instance['friends'] == 'SHOWFRIENDS') { ?> checked="checked" <?php } ?> /><span class="input_label">Show Friends Locations</span><br />
                <input type="radio" name="<?php echo $this->get_field_name( 'friends' ); ?>" value="NOFRIENDS" <?php if($instance['friends'] == 'NOFRIENDS') { ?> checked="checked" <?php } ?> /><span class="input_label">Hide Friends Locations</span><br />
            </td>
          </tr>
        </table>
        
        <div class="advanced_holder">
            <p><a href="#" id="advanced_settings_<?php echo $randomid; ?>"><?php echo __('Advanced Settings', 'gpress'); ?></a></p>
            <div id="gpress_advanced_hidden_<?php echo $randomid; ?>" style="display:none;">
                <span class="advanced_divider"></span>
                <label><?php echo __('Overwrite default height for this map:', 'gpress'); ?></label>
                <input type="text" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" style="width:100%;" />
            </div>
        </div>
        
        <script type="text/javascript">
		
		jQuery(document).ready(function() {
										
			jQuery("#advanced_settings_<?php echo $randomid; ?>").click(function () {
				jQuery("#gpress_advanced_hidden_<?php echo $randomid; ?>").slideToggle("fast");
			});
		
		});
		
		</script>

	<?php
	}
}

?>