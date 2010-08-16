<?php

function gpress_geoform($map_id, $map_type, $map_zoom, $map_position, $is_empty, $hide_inputs = false, $is_geo_settings = false) {
	
	global $post, $tppo;
 
	$meta = get_post_meta($post->ID,$map_id,TRUE);
	$gpress_post_type = $post->post_type;
	$geo_latitude = get_post_meta($post->ID,'geo_latitude',TRUE);
	$geo_longitude = get_post_meta($post->ID,'geo_longitude',TRUE);
	$geo_latlng = ''.$geo_latitude.', '.$geo_longitude.'';
	
	$this_map_height = $meta['height'];
	
	// gPress Post Markers
	$marker_posts_icon = $tppo->get_tppo('marker_posts_icon', 'blogs');
	$marker_posts_shadow = $tppo->get_tppo('marker_posts_shadow', 'blogs');
	$marker_posts_icon_file = $marker_posts_icon['filename'];
	$marker_posts_icon_url = $marker_posts_icon['fileurl'];
	$marker_posts_shadow_file = $marker_posts_shadow['filename'];
	$marker_posts_shadow_url = $marker_posts_shadow['fileurl'];
	if(!empty($marker_posts_icon_url)) {
		$default_marker_icon_post = $marker_posts_icon_url;
	}else{
		if(!empty($marker_posts_icon_file)) {
			$default_marker_icon_post = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_posts_icon_file;
		} else {
			$default_marker_icon_post = GPRESS_URL.'/gpress-core/images/markers/post.png';
		}
	}
	if(!empty($marker_posts_shadow_url)) {
		$default_marker_shadow_post = $marker_posts_shadow_url;
	}else{
		if(!empty($marker_posts_shadow_file)) {
			$default_marker_shadow_post = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_posts_shadow_file;
		} else {
			$default_marker_shadow_post = GPRESS_URL.'/gpress-core/images/markers/bg.png';
		}
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
	
	// CHECK FOR CUSTOM AD-HOC MARKERS
	$adhoc_marker_icon_url = $meta['icon_url'];
	$adhoc_marker_icon_file = $meta['icon_file'];
	$adhoc_marker_shadow_url = $meta['shadow_url'];
	$adhoc_marker_shadow_file = $meta['shadow_file'];
	if(!empty($adhoc_marker_icon_url)) {
		$adhoc_marker_icon = $adhoc_marker_icon_url;
	}else{
		if(!empty($adhoc_marker_icon_file)) {
			$adhoc_marker_icon = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$adhoc_marker_icon_file;
		}
	}
	if(!empty($adhoc_marker_shadow_url)) {
		$adhoc_marker_shadow = $adhoc_marker_shadow_url;
	}else{
		if(!empty($adhoc_marker_shadow_file)) {
			$adhoc_marker_shadow = GPRESS_URL.'/gpress-admin/fieldtypes/image_upload/uploads/'.$adhoc_marker_shadow_file;
		}
	}
	
	// Define which Markers to use
	if($gpress_post_type == 'post') {
		$default_marker_icon = $default_marker_icon_post;
		$default_marker_shadow = $default_marker_shadow_post;
	}else{
		$default_marker_icon = $default_marker_icon_place;
		$default_marker_shadow = $default_marker_shadow_place;
	}
	if(!empty($adhoc_marker_icon)) {
		$this_marker_icon = $adhoc_marker_icon;
	}else{
		$this_marker_icon = $default_marker_icon;
	}
	if(!empty($adhoc_marker_shadow)) {
		$this_marker_shadow = $adhoc_marker_shadow;
	}else{
		$this_marker_shadow = $default_marker_shadow;
	}
	// END OF MARKERS
	
	$default_map_height = $tppo->get_tppo('default_map_height', 'blogs');
	if(empty($default_map_height)) {
		$default_map_height = '450';
	}
	if(empty($this_map_height)) {
		$this_height = $default_map_height;
	}else{
		$this_height = $this_map_height;
	}
	
	if($is_geo_settings) {
		$gpress_post_type = 'bp_geo_settings';
	}

	?>
    
    	<style>
		/* THESE MAP OPTION OVERWRITE DEFAULT SETTINGS */
		#mapCanvas<?php echo $map_id; ?> {
			height:<?php echo $this_height; ?>px;
		}
		</style>
        
		<script type="text/javascript">
			var GPRESS_DIR = '<?php echo GPRESS_DIR; ?>';
			var GPRESS_URL = '<?php echo GPRESS_URL; ?>';
		</script>
        
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
        var geocoder<?php echo $map_id; ?>, map<?php echo $map_id; ?>, marker<?php echo $map_id; ?>;
		
		function geocodePosition<?php echo $map_id; ?>(pos) {
          geocoder<?php echo $map_id; ?>.geocode({
            latLng: pos
          }, function(responses) {
            if (responses && responses.length > 0) {
              updateMarkerAddress<?php echo $map_id; ?>(responses[0].formatted_address);
            } else {
              updateMarkerAddress<?php echo $map_id; ?>('Cannot determine address at this location.');
            }
		  });
        }
        
        function updateMarkerStatus<?php echo $map_id; ?>(str) {
          document.getElementById('markerStatus<?php echo $map_id; ?>').innerHTML = str;
        }
        
        function updateMarkerPosition<?php echo $map_id; ?>(latLng) {
          document.getElementById('location_value<?php echo $map_id; ?>').value = [
            latLng.lat(),
            latLng.lng()
          ].join(', ');
		}
        
        function updateMarkerAddress<?php echo $map_id; ?>(str) {
          document.getElementById('address<?php echo $map_id; ?>').value = str;
        }
        
        function initialize<?php echo $map_id; ?>() {
			
			geocoder<?php echo $map_id; ?> = new google.maps.Geocoder();
          
			<?php
			if($map_position == "") {  ?>
				if(google.loader.ClientLocation){
					var latLng = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
				}else{
					var latLng = new google.maps.LatLng(0, 0);
				}
			<?php
			} else {  
			?>
				
				<?php
				if($is_empty) {
					?>
					if(google.loader.ClientLocation){
						var latLng = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
					}else{
						var latLng = new google.maps.LatLng(0, 0);
					}
					<?php
				}else{
					?>
					
					<?php if($gpress_post_type == 'post') { ?>
						var latLng = new google.maps.LatLng(<?php echo $geo_latlng; ?>);				
					<?php } else { ?>
						<?php if($is_geo_settings) { ?>
							var latLng = new google.maps.LatLng(<?php echo $map_position; ?>);						
						<?php } else { ?>
							var latLng = new google.maps.LatLng(<?php echo $meta['latlng']; ?>);
						<?php } ?>
					<?php } ?>
					
					<?php					
				}
				?>
				
			<?php
			}
			?>
			
			var image<?php echo $map_id; ?> = new google.maps.MarkerImage('<?php echo $this_marker_icon; ?>',
				new google.maps.Size(30, 30),
				new google.maps.Point(0, 0),
				new google.maps.Point(21, 22));				
	
			var shadow<?php echo $map_id; ?> = new google.maps.MarkerImage('<?php echo $this_marker_shadow; ?>',
				new google.maps.Size(40, 40),
				new google.maps.Point(0, 0),
				new google.maps.Point(26, 27));
    
            map<?php echo $map_id; ?> = new google.maps.Map(document.getElementById('mapCanvas<?php echo $map_id; ?>'), {
                zoom: <?php echo $map_zoom ?>,
                center: latLng,
                mapTypeId: google.maps.MapTypeId.<?php echo $map_type ?>
              });
            marker<?php echo $map_id; ?> = new google.maps.Marker({
			    <?php if(!empty($this_marker_icon)) { ?>
                    icon: image<?php echo $map_id; ?>,
				<?php } ?>
			    <?php if(!empty($this_marker_shadow)) { ?>
                    shadow: shadow<?php echo $map_id; ?>,
				<?php } ?>
                position: latLng,
                title: 'Drag Me',
                map: map<?php echo $map_id; ?>,
                draggable: true
            });
            // Update current position info.
            updateMarkerPosition<?php echo $map_id; ?>(latLng);
            geocodePosition<?php echo $map_id; ?>(latLng);
            
            // Add dragging event listeners.
            google.maps.event.addListener(marker<?php echo $map_id; ?>, 'dragstart', function() {
            updateMarkerAddress<?php echo $map_id; ?>('Dragging...');
			});
            
            google.maps.event.addListener(marker<?php echo $map_id; ?>, 'drag', function() {
            updateMarkerStatus<?php echo $map_id; ?>('Dragging...');
            updateMarkerPosition<?php echo $map_id; ?>(marker<?php echo $map_id; ?>.getPosition());
            });
            
            google.maps.event.addListener(marker<?php echo $map_id; ?>, 'dragend', function() {
            updateMarkerStatus<?php echo $map_id; ?>('Drag ended');
            geocodePosition<?php echo $map_id; ?>(marker<?php echo $map_id; ?>.getPosition());
            });
			
        }
    
        function getFormattedLocation<?php echo $map_id; ?>() {
          if (google.loader.ClientLocation.address.country_code == "US" &&
            google.loader.ClientLocation.address.region) {
            return google.loader.ClientLocation.address.city + ", " 
                + google.loader.ClientLocation.address.region.toUpperCase();
          } else {
            return  google.loader.ClientLocation.address.city + ", "
                + google.loader.ClientLocation.address.country_code;
          }
        }
                
        function codeAddress<?php echo $map_id; ?>() {
          var address = document.getElementById('search_address<?php echo $map_id; ?>').value;
          
            if (geocoder<?php echo $map_id; ?>) {
              geocoder<?php echo $map_id; ?>.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map<?php echo $map_id; ?>.setCenter(results[0].geometry.location);
                    marker<?php echo $map_id; ?>.setPosition(results[0].geometry.location);
					geocodePosition<?php echo $map_id; ?>(results[0].geometry.location);
					updateMarkerPosition<?php echo $map_id; ?>(results[0].geometry.location);
                } else {
                    
                    if (status == "ZERO_RESULTS") {
                        alert("Sorry, but the address specified cannot be found...");
                    } else if (status == "OVER_QUERY_LIMIT") {
                        alert("Sorry, but you have exceeded your query limit quota...");
                    } else if (status == "REQUEST_DENIED") {
                        alert("Sorry, but for some reason, Google denied your request...");
                    } else if (status == "INVALID_REQUEST") {
                        alert("Sorry, but for some reason, something seems to have gone wrong...");
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                        
                }
              });
            }
        }
        
        jQuery(document).ready(function(){
            jQuery('#search_address<?php echo $map_id; ?>').keypress(function(event){
                                                if(event.keyCode == 13){
                                                    codeAddress<?php echo $map_id; ?>();
                                                    return false;
                                                }
                                             });
        });
		
        google.maps.event.addDomListener(window, 'load', initialize<?php echo $map_id; ?>);
		
    </script>
    
   	<div id="mapFrame<?php echo $map_id; ?>" class="map_frame gpress_mapframe">     
    	<div style="clear:both; float:left; width:100%; margin-bottom:15px;">  
        
            <div style="float:left; margin-bottom:20px; width:100%;" class="other_stuff<?php echo $map_id; ?> gpress_otherstuff">
                <input id="search_address<?php echo $map_id; ?>" name="search_address<?php echo $map_id; ?>" type="textbox" value="" style="width:65%; float:left;">
                <input type="button" value="SEARCH" onclick="codeAddress<?php echo $map_id; ?>();" style="width:30%; text-align:center; float:right; margin-left:2%;">
            </div>
                
			<div id="mapCanvas<?php echo $map_id; ?>" class="gpress_mapcanvas"></div>
					
            <div class="other_stuff<?php echo $map_id; ?> gpress_otherstuff">
				
                <?php if($is_geo_settings) { ?>
	            	<input id="location_value<?php echo $map_id; ?>" class="gpress_locationvalue" type="textbox" name="geo_settings_latlng" value="<?php if(!empty($meta['latlng'])) echo $meta['latlng']; ?>" style="width:100%; float:left; margin:25px 0 10px; <?php if($hide_inputs) { echo 'display:none;'; } ?>">                
                <?php } else { ?>            
	            	<input id="location_value<?php echo $map_id; ?>" class="gpress_locationvalue" type="textbox" name="<?php echo $map_id; ?>[latlng]" value="<?php if(!empty($meta['latlng'])) echo $meta['latlng']; ?>" style="width:100%; float:left; margin:25px 0 10px; <?php if($hide_inputs) { echo 'display:none;'; } ?>">
                <?php } ?>
                
            </div>
                    
		</div>
	</div>
	
	<div class="gpress_meta_boxes other_stuff<?php echo $map_id; ?> gpress_otherstuff">
		<div id="infoPanel<?php echo $map_id; ?>" class="gpress_infopanel">
			<div id="leftColumn<?php echo $map_id; ?>" class="gpress_leftcolumn">
				<b><?php echo __('Closest address:', 'gpress'); ?></b>
                
                <?php if($is_geo_settings) { ?>
					<textarea id="address<?php echo $map_id; ?>" class="gpress_address" name="geo_settings_closest_address" value="<?php if(!empty($meta['address'])) echo $meta['address']; ?>" class="closest_address"></textarea>                
                <?php } else { ?>
					<textarea id="address<?php echo $map_id; ?>" class="gpress_address" name="<?php echo $map_id; ?>[address]" value="<?php if(!empty($meta['address'])) echo $meta['address']; ?>" class="closest_address"></textarea>
                <?php } ?>
                
			</div>
			<div id="middleColumn<?php echo $map_id; ?>" class="gpress_middlecolumn">&nbsp;</div>
			<div id="rightColumn<?php echo $map_id; ?>" class="gpress_rightcolumn" style="<?php if($hide_inputs) { echo 'display:none;'; } ?>">
				<b><?php echo __('Marker status:', 'gpress'); ?></b>
				<div id="markerStatus<?php echo $map_id; ?>"><i><?php echo __('Click and drag the marker.', 'gpress'); ?></i></div>
			</div>
		</div>
	</div>
    
    <?php
	
}

?>