<?php
	
	function display_tppo_gmap($thisfield, $tpp_id){
		
		$use_default = false;
		$default_location = $thisfield->linked_options['default_latlng'];
		$latlng = $thisfield->value['latlng'];
		if(empty($latlng)) {
			if(!empty($default_location)) {
				$use_default = true;
			}
		}
			
		?>
		<li>
        
		  	<label class="general-label"><?php echo $thisfield->field_label?>:</label>
		  	<div class="general-input">
            
            	<script type="text/javascript" src="<?php echo TPPO_URL?>/fieldtypes/gmap/gmap.js"></script>
				
				<script type="text/javascript">
					
				
					var geocoder_<?php echo $thisfield->option_name; ?>, map_<?php echo $thisfield->option_name; ?>, marker_<?php echo $thisfield->option_name; ?>;
					var ci_<?php echo $thisfield->option_name; ?>;
					
					function geocodePosition_<?php echo $thisfield->option_name; ?>(pos) {
						geocoder_<?php echo $thisfield->option_name; ?>.geocode({
						latLng: pos
						}, function(responses) {
							if (responses && responses.length > 0) {
								updateMarkerAddress_<?php echo $thisfield->option_name; ?>(responses[0].formatted_address);
							} else {
								updateMarkerAddress_<?php echo $thisfield->option_name; ?>('Cannot determine address at this location.');
							}
						});
					}
					
					function updateMarkerStatus_<?php echo $thisfield->option_name; ?>(str) {
					  	document.getElementById('markerStatus_<?php echo $thisfield->option_name; ?>').innerHTML = str;
					}
					
					function updateMarkerPosition_<?php echo $thisfield->option_name; ?>(latLng) {
					  	document.getElementById('<?php echo $thisfield->option_name; ?>_location_value').value = [
							latLng.lat(),
							latLng.lng()
					  	].join(', ');
					  	document.getElementById('<?php echo $thisfield->option_name; ?>_lat').value = latLng.lat();
					  	document.getElementById('<?php echo $thisfield->option_name; ?>_lng').value = latLng.lng();
					}
					
					function updateMarkerAddress_<?php echo $thisfield->option_name; ?>(str) {
					  	document.getElementById('address_<?php echo $thisfield->option_name; ?>').value = str;
					}
					
					function initialize_<?php echo $thisfield->option_name; ?>() {
						geocoder_<?php echo $thisfield->option_name; ?> = new google.maps.Geocoder();
						
						if(google.loader.ClientLocation){
							var lat = google.loader.ClientLocation.latitude;
							var lng = google.loader.ClientLocation.longitude;
						}else{
							var lat = 0;
							var lng = 0;
						}
						var location_value = jQuery('#<?php echo $thisfield->option_name; ?>_location_value').val();
						if(location_value != ""){
							var temp = location_value.split(", ");
							lat = temp[0];
							lng = temp[1];
						}
						
						<?php if($use_default) { ?>
							var latLng_form = new google.maps.LatLng(<?php echo $default_location; ?>);
						<?php } else { ?>
							var latLng_form = new google.maps.LatLng(lat, lng);
						<?php } ?>
						
						map_<?php echo $thisfield->option_name; ?> = new google.maps.Map(document.getElementById('mapCanvas_<?php echo $thisfield->option_name; ?>'), {
							zoom: 13,
							center: latLng_form,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});
						marker_<?php echo $thisfield->option_name; ?> = new google.maps.Marker({
							position: latLng_form,
							title: 'Drag Me',
							map: map_<?php echo $thisfield->option_name; ?>,
							draggable: true
						});
						  
						// Update current position info.
						updateMarkerPosition_<?php echo $thisfield->option_name; ?>(latLng_form);
						geocodePosition_<?php echo $thisfield->option_name; ?>(latLng_form);
						
						// Add dragging event listeners.
						google.maps.event.addListener(marker_<?php echo $thisfield->option_name; ?>, 'dragstart', function() {
							updateMarkerAddress_<?php echo $thisfield->option_name; ?>('Dragging...');
						});
						
						google.maps.event.addListener(marker_<?php echo $thisfield->option_name; ?>, 'drag', function() {
							updateMarkerStatus_<?php echo $thisfield->option_name; ?>('Dragging...');
							updateMarkerPosition_<?php echo $thisfield->option_name; ?>(marker_<?php echo $thisfield->option_name; ?>.getPosition());
						});
						
						google.maps.event.addListener(marker_<?php echo $thisfield->option_name; ?>, 'dragend', function() {
							updateMarkerStatus_<?php echo $thisfield->option_name; ?>('Drag ended');
							geocodePosition_<?php echo $thisfield->option_name; ?>(marker_<?php echo $thisfield->option_name; ?>.getPosition());
							
							
						});
					}
				
					function getFormattedLocation_<?php echo $thisfield->option_name; ?>() {
						if (google.loader.ClientLocation.address.country_code == "US" && google.loader.ClientLocation.address.region) {
							return google.loader.ClientLocation.address.city + ", " + google.loader.ClientLocation.address.region.toUpperCase();
						} else {
							return  google.loader.ClientLocation.address.city + ", " + google.loader.ClientLocation.address.country_code;
						}
					}
							
					function codeAddress_<?php echo $thisfield->option_name; ?>() {
						var address = document.getElementById('search_address_<?php echo $thisfield->option_name; ?>').value;
						
						if (geocoder_<?php echo $thisfield->option_name; ?>) {
						  	geocoder_<?php echo $thisfield->option_name; ?>.geocode( { 'address': address}, function(results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									map_<?php echo $thisfield->option_name; ?>.setCenter(results[0].geometry.location);
									marker_<?php echo $thisfield->option_name; ?>.setPosition(results[0].geometry.location);
									geocodePosition_<?php echo $thisfield->option_name; ?>(results[0].geometry.location);
									updateMarkerPosition_<?php echo $thisfield->option_name; ?>(results[0].geometry.location);
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
						jQuery('#search_address_<?php echo $thisfield->option_name; ?>').keypress(function(event){
															if(event.keyCode == 13){
																codeAddress_<?php echo $thisfield->option_name; ?>();
																return false;
															}
														 });
					});
					
					
					
				</script>
			
				<style type="text/css">
					#mapCanvas_<?php echo $thisfield->option_name; ?> { width: 100%; height: 350px; float: left; background:#FFF; border:1px solid #CCC; }
					#infoPanel_<?php echo $thisfield->option_name; ?> { clear:both; float:left; width:100%; margin:10px 0 25px; }
					#leftColumn_<?php echo $thisfield->option_name; ?> { float:left; width:57%; }
					#rightColumn_<?php echo $thisfield->option_name; ?> { float:left; width:37%; }
					#middleColumn_<?php echo $thisfield->option_name; ?> { float:left; width:6%; }
					#rightColumn_<?php echo $thisfield->option_name; ?> { text-align:right; }
					#rightColumn_<?php echo $thisfield->option_name; ?> div { font-size:11px; }
					textarea#address_<?php echo $thisfield->option_name; ?> { width:100%; height:auto; border:none; font-size:11px; font-style:italic; padding:0; margin:0; background:#E6E6E7;}
					div.second_half { clear:both; float:left !important; width:100% !important; margin:10px 0; }
					div.second_half select { border:1px solid #B2B2B2; min-width:133px; }
					div.second_half input { background:#FFF; }
					div.second_half input[type="textbox"] { -moz-border-radius-bottomleft:5px; -moz-border-radius-bottomright:5px; -moz-border-radius-topleft:5px; -moz-border-radius-topright:5px; border:1px solid #B2B2B2 !important; display:block; padding:5px; }
					div.second_half input:hover, div.second_half input:focus { border: 1px solid #F05B01 !important; }
				</style>
				
			</div>
			
            <div class="general-input second_half">	
            <span class="help-text" style="clear:both; float:left;width:100%;margin:-5px 0 20px;"><?php echo $thisfield->field_description; ?></span> 
				<div id="mapFrame_<?php echo $thisfield->option_name; ?>">
					<div style="float:left; margin-bottom:20px; width:100%;">
						<input id="search_address_<?php echo $thisfield->option_name; ?>" name="search_address_<?php echo $thisfield->option_name; ?>" type="textbox" value="" style="width:64%; float:left;">
						<input type="button" value="SEARCH" onclick="codeAddress_<?php echo $thisfield->option_name; ?>();" style="width:30%; text-align:center; float:right; margin-left:2%; cursor:pointer;">
					</div>
					<div class="tppo_mapCanvas" id="mapCanvas_<?php echo $thisfield->option_name; ?>"></div>
					<input id="<?php echo $thisfield->option_name; ?>_location_value" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][latlng]" type="textbox" readonly="readonly" value="<?php echo $thisfield->value['latlng']; ?>" style="width:100%; float:left; margin:25px 0 10px; border:1px">
					<input type="hidden" id="<?php echo $thisfield->option_name; ?>_lat" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][lat]"  value="<?php echo $thisfield->value['lat']; ?>"/>
					<input type="hidden" id="<?php echo $thisfield->option_name; ?>_lng" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][lng]"  value="<?php echo $thisfield->value['lng']; ?>"/>
					
					<div id="infoPanel_<?php echo $thisfield->option_name; ?>">
						<div id="leftColumn_<?php echo $thisfield->option_name; ?>">
							<b>Closest address:</b>
							<textarea id="address_<?php echo $thisfield->option_name; ?>" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][address]"><?php echo $thisfield->value['address']; ?></textarea>
						</div>
						<div id="middleColumn_<?php echo $thisfield->option_name; ?>">&nbsp;</div>
						<div id="rightColumn_<?php echo $thisfield->option_name; ?>">
							<b>Marker status:</b>
							<div id="markerStatus_<?php echo $thisfield->option_name; ?>"><i>Click and drag the marker.</i></div>
						</div>
					</div>
					
				</div>
			</div>
			
			<div class="clear"></div>

		</li>	
		<?php
	}
?>