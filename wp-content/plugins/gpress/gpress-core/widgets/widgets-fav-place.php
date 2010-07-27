<?php

add_action( 'widgets_init', 'gpress_fav_place_load_widgets' );

function gpress_fav_place_load_widgets() {
	register_widget( 'GPRESS_FAV_PLACE_WIDGET' );
}

class GPRESS_FAV_PLACE_WIDGET extends WP_Widget {

	function GPRESS_FAV_PLACE_WIDGET() {
		$widget_ops = array( 'classname' => 'gpress-fav-place', 'description' => __('Pick your favorite place to show on map', 'gpress') );
		$this->WP_Widget( 'gpress-fav-place-widget', __('Favorite Place', 'gpress'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$name = $instance['name'];
		$placeid = $instance['placeid'];
		$type = $instance['type'];
		$zoom = $instance['zoom'];
		$height = $instance['height'];
		$icon = $instance['icon'];
		$shadow = $instance['shadow'];
		
		if(empty($placeid)) {
			$gpress_this_place_title = __('Favorite Place', 'gpress');
		}else{
			query_posts('post_type=place&p='.$placeid.'');
			if ( have_posts() ) : while ( have_posts() ) : the_post();
			$gpress_this_place_title = single_post_title('', FALSE);
			endwhile; else:
			endif;
			wp_reset_query();
		}

		echo $before_widget;

			/* Display name from widget settings if one was input. */
			if ( $name ) {
				echo '<h3 class="widget-title">'.$instance['name'].'</h3>';
			}else{
				echo '<h3 class="widget-title">'.$gpress_this_place_title.'</h3>';
			}
			
			if(empty($placeid)) {
				echo __('<p>You will first need to add a place ID within this widget\'s options in order for it to be displayed...</p>', 'gpress');
			}else {
			
				$gpress_widget_places_map_id = '_gpress_places';
				$gpress_widget_places_map_type = 'ROADMAP';
				$gpress_widget_places_map_zoom = '13';
				$gpress_widget_places_map_position = $this->number;
				$gpress_widget_places_place_id = $placeid;
				$gpress_widget_places_place_title = '_gpress_widgets_places';
				$gpress_widget_post_type = 'widget';
				
				$gpress_map_height = $instance['height'];
				$gpress_icon_url = $instance['icon'];
				$gpress_shadow_url = $instance['shadow'];
				
				$this_map_type = $instance['type'];
				$this_map_zoom = $instance['zoom'];
				
				if(!empty($this_map_type)) {
					$gpress_widget_places_map_type = $this_map_type;
				}
				
				if(!empty($this_map_zoom)) {
					$gpress_widget_places_map_zoom = $this_map_zoom;
				}
				
				$map_settings = array(
					'map_id' 		=> $gpress_widget_places_map_id,
					'map_height' 	=> $gpress_map_height,
					'map_type' 		=> $gpress_widget_places_map_type,
					'map_zoom' 		=> $gpress_widget_places_map_zoom,
					'map_position' 	=> $gpress_widget_places_map_position,
					'post_type' 	=> $gpress_widget_post_type,
					'post_id' 		=> false,
					'widget_id' 	=> $this->number,
					'place_id' 		=> $gpress_widget_places_place_id,
					'marker_icon' 	=> $gpress_icon_url,
					'marker_shadow' => $gpress_shadow_url,
					'marker_title' 	=> $gpress_widget_places_place_title,
					'marker_url' 	=> false
				);
				
				gpress_add_map($map_settings);	
				
				//gpress_add_map($gpress_widget_places_map_id, $gpress_widget_places_map_type, $gpress_widget_places_map_zoom, $gpress_widget_places_map_position, $gpress_widget_places_place_id, $gpress_widget_places_place_title, $gpress_widget_post_type, $gpress_map_height, $gpress_icon_url, $gpress_shadow_url);	
			
			}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */		
		$instance['name'] = $new_instance['name'];
		$instance['placeid'] = $new_instance['placeid'];
		$instance['type'] = $new_instance['type'];
		$instance['zoom'] = $new_instance['zoom'];
		$instance['height'] = $new_instance['height'];
		$instance['icon'] = $new_instance['icon'];
		$instance['shadow'] = $new_instance['shadow'];

		return $instance;
	}

	function form( $instance ) {

		global $tppo;
		
		$instance = wp_parse_args( (array) $instance ); 
		$default_map_height = $tppo->get_tppo('default_map_height', 'blogs');
		
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php echo __('Widget Title:', 'gpress'); ?></label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:95%;" />
		</p>
        
        <p>
			<label for="<?php echo $this->get_field_id( 'placeid' ); ?>"><?php echo __('Place ID:', 'gpress'); ?></label>
			<input id="<?php echo $this->get_field_id( 'placeid' ); ?>" name="<?php echo $this->get_field_name( 'placeid' ); ?>" value="<?php echo $instance['placeid']; ?>" style="width:95%;" />
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
        
        <div class="advanced_holder">
            <p><a href="#" id="advanced_settings_<?php echo $this->get_field_id( 'placeid' ); ?>"><?php echo __('Advanced Settings', 'gpress'); ?></a></p>
            <div id="gpress_advanced_hidden_<?php echo $this->get_field_id( 'placeid' ); ?>" style="display:none;">
                <span class="advanced_divider"></span>
                <label><?php echo __('Overwrite default height for this map:', 'gpress'); ?></label>
                <input type="text" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" style="width:100%;" />
                <p class="advanced_description"><?php echo __('Numbers only (defaults to', 'gpress'); ?> <?php echo $default_map_height; ?>)</p>
                <span class="advanced_divider"></span>
                <label><?php echo __('Custom icon URL for this map:', 'gpress'); ?></label>
                <input type="text" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="<?php echo $instance['icon']; ?>" style="width:100%;" />
                <p class="advanced_description"><?php echo __('This imaage should be 42px X 43px', 'gpress'); ?></p>
                <span class="advanced_divider"></span>
                <label><?php echo __('Custom shadow URL for this map:', 'gpress'); ?></label>
                <input type="text" name="<?php echo $this->get_field_name( 'shadow' ); ?>" value="<?php echo $instance['shadow']; ?>" style="width:100%;" />
                <p class="advanced_description"><?php echo __('This imaage should be 42px X 43px', 'gpress'); ?></p>
            </div>
        </div>
        
        <script type="text/javascript">
		
		jQuery(document).ready(function() {
										
			jQuery("#advanced_settings_<?php echo $this->get_field_id( 'placeid' ); ?>").click(function () {
				jQuery("#gpress_advanced_hidden_<?php echo $this->get_field_id( 'placeid' ); ?>").slideToggle("fast");
			});
		
		});
		
		</script>

	<?php
	}
}

?>