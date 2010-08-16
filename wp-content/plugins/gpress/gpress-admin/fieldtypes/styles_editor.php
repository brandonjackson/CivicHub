<?php
	function display_tppo_styles_editor($thisfield, $tpp_id){	
		$file_url = $thisfield->value['fileurl'];
		?>
		<li>
		  	<label class="general-label"><?php echo $thisfield->field_label; ?>:</label>
		  	<div class="general-input">
            
                <link href="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/css/colorpicker.css" rel="stylesheet" type="text/css" />
                <link href="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/css/default.css" rel="stylesheet" type="text/css" />
                <link href="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/css/styles.css" rel="stylesheet" type="text/css" />
                <link href="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/css/uploadify.css" rel="stylesheet" type="text/css" />

                <script type="text/javascript" src="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/js/colorpicker.js"></script>
                <script type="text/javascript" src="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/js/eye.js"></script>
                <script type="text/javascript" src="<?php echo TPPO_URL; ?>/fieldtypes/bg_editor/js/utils.js"></script>
                <script type="text/javascript" src="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/scripts/swfobject.js"></script>
                <script type="text/javascript" src="<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/scripts/jquery.uploadify.v2.1.0.min.js"></script>
                
                <script type="text/javascript">
                jQuery(document).ready(function() {
												
					/* COLOR PICKER */
					jQuery('#colorpickerHolder_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').ColorPicker({
						
						onChange: function(data){
							
							HSBToRGB = function (hsb) {
								var rgb = {};
								var h = Math.round(hsb.h);
								var s = Math.round(hsb.s*255/100);
								var v = Math.round(hsb.b*255/100);
								if(s == 0) {
									rgb.r = rgb.g = rgb.b = v;
								} else {
									var t1 = v;
									var t2 = (255-s)*v/255;
									var t3 = (t1-t2)*(h%60)/60;
									if(h==360) h = 0;
									if(h<60) {rgb.r=t1;	rgb.b=t2; rgb.g=t2+t3}
									else if(h<120) {rgb.g=t1; rgb.b=t2;	rgb.r=t1-t3}
									else if(h<180) {rgb.g=t1; rgb.r=t2;	rgb.b=t2+t3}
									else if(h<240) {rgb.b=t1; rgb.r=t2;	rgb.g=t1-t3}
									else if(h<300) {rgb.b=t1; rgb.g=t2;	rgb.r=t2+t3}
									else if(h<360) {rgb.r=t1; rgb.g=t2;	rgb.b=t1-t3}
									else {rgb.r=0; rgb.g=0;	rgb.b=0}
								}
								return {r:Math.round(rgb.r), g:Math.round(rgb.g), b:Math.round(rgb.b)};
							},
											
							RGBToHex = function (rgb) {
								var hex = [
									rgb.r.toString(16),
									rgb.g.toString(16),
									rgb.b.toString(16)
								];
								jQuery.each(hex, function (nr, val) {
									if (val.length == 1) {
										hex[nr] = '0' + val;
									}
								});
								return hex.join('');
							},
											
							HSBToHex = function (hsb) {
								return RGBToHex(HSBToRGB(hsb));
							},
			
							b = data.b;
							h = data.h;
							s = data.s;
							
							hex = '#'+HSBToHex(data);
							
							var selectedSelector = jQuery('#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val();
							jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[hex\\]]').val(hex);
			
						},
						flat: true
					});
					
					<?php if(is_multisite()) { ?>
						<?php global $blog_id; if($blog_id != 1) { ?>
							var folder = '../../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
						<?php } else { ?>
							var folder = '../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
						<?php } ?>
					<?php } else { ?>
						var folder = '../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
					<?php } ?>
					
					/* IMAGE UPLOADER */
                    jQuery("#uploadify_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>").uploadify({
                        'uploader'       : '<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/scripts/uploadify.swf',
                        'script'         : '<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/scripts/uploadify.php',
                        'checkScript'    : '<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/scripts/check.php',
                        'cancelImg'      : '<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/cancel.png',
                        'folder'         : folder,
                        'queueID'        : 'fileQueue',
                        'auto'           : true,
						'fileDesc'		 : 'Images Only (*.jpg;*.gif;*.png)',
						'fileExt'		 : '*.jpg;*.gif;*.png',
						'buttonImg'		: '<?php echo TPPO_URL; ?>/fieldtypes/styles_editor/button.png',
						'rollover'		: true,
                        'multi'          : false,
						'onComplete'	 : function(event,queueID,fileObj,response,data){
							var json = eval("(" + response + ')');
							if(json.error == 0){
								var selectedSelector = jQuery('#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val();
								jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[filename\\]]').val(fileObj.name);
							}else{
								var selectedSelector = jQuery('#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val();
								jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[filename\\]]').val('');
							}
							alert(json.msg);
						}
                    });
					
					/* AJAX SWITCHER */
					jQuery(function(){
						
						jQuery("select#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>").change(function(){
							var this_selector_settings = jQuery(this.options[this.selectedIndex]).attr('title').split("|");
							var selectedSelector = jQuery('#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val();
							
							//alert(jQuery('#file_url_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val());
							
							// Colorbox enabled
							if(this_selector_settings[0] == "1"){
								jQuery('#color_picker_div').removeClass('hidden');
								jQuery('#color_picker_div').addClass('visible');
								var hex = jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[hex\\]]').val();
								
								if(!hex)
									hex = '#000000';
								
								jQuery('#colorpickerHolder_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').ColorPickerSetColor(hex);
							}else{
								jQuery('#color_picker_div').removeClass('visible');
								jQuery('#color_picker_div').addClass('hidden');								
							}
							// File Upload enabled
							if(this_selector_settings[1] == "1"){
								jQuery('#image_upload_div').removeClass('hidden');
								jQuery('#image_upload_div').addClass('visible');
								var filename = jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[filename\\]]').val();
								var fileurl = jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[fileurl\\]]').val();
								jQuery('#file_url_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val(fileurl);
								if(fileurl != ""){
										jQuery('#imagebox_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').show();
										jQuery('#image_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').show();
										jQuery('#image_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').attr('src',fileurl);
										jQuery('#remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').hide();
								}else{
									if(filename != ""){
										jQuery('#imagebox_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').show();
										jQuery('#image_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').show();
										jQuery('#image_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').attr('src','<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/styles_editor/uploads/'+filename);
										jQuery('#remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').show();										
									}else{
										jQuery('#imagebox_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').hide();
									}
								}
							}else{
								jQuery('#image_upload_div').removeClass('visible');
								jQuery('#image_upload_div').addClass('hidden');
							}
							// Other Stuffs enabled
							if(this_selector_settings[2] == "1"){
								jQuery('#bg_editor_div').removeClass('hidden');
								jQuery('#bg_editor_div').addClass('visible');
								jQuery('input:radio[name^=radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>]').each(function(){
									var type = jQuery(this).attr('name').split('_').pop();
									var value = jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\['+type+'\\]]').val();
									if(this.value == value){
										this.checked = true;
									}else{
										this.checked = false;	
									}
									jQuery(this).trigger('updateState');
								});															
							}else{
								jQuery('#bg_editor_div').removeClass('visible');
								jQuery('#bg_editor_div').addClass('hidden');	
							}
							
							jQuery('#help_text_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').html(jQuery('#help_text_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_'+selectedSelector).html());
							
						}).trigger('change');
						
						jQuery('#file_url_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').blur(function(){
							var selectedSelector = jQuery('#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val();
							jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[fileurl\\]]').val(this.value);		   
						});
						
						jQuery('input:radio[name^=radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>]').change(function(){
							var selectedSelector = jQuery('#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val();
							var type = jQuery(this).attr('name').split('_').pop();
							jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\['+type+'\\]]').val(this.value);

						});
						
						jQuery('#remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').click(function() {
							if(confirm('Are you sure you want to remove this image...?')) {
								var selectedSelector = jQuery('#style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').val();
								jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\['+selectedSelector+'\\]\\[filename\\]]').val('');
								jQuery('#image_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').hide();
								jQuery('#remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').hide();
								alert("Image will be removed after you SAVE CHANGES");
							}
						});
						
						jQuery('#reset_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').click(function(){
							if(confirm('Are you sure you want to reset everything back to default...?')) {
								jQuery('input[name^=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]]').each(function(){
									var thename = this.name.substr(4).replace(/\[/g,"\\[").replace(/\]/g,"\\]");
									jQuery(this).val(jQuery('input[name=tppodefault'+thename+']').val());
								});
								alert("Styles will be re-set after you SAVE CHANGES");
							}
						});
						
					})
					
                });
                </script>
                
                <?php
					$options = "";
					$count = 0;
					$shownfirst = false;
					
					if(is_array($thisfield->linked_options)){
						foreach($thisfield->linked_options as $style_selector_name => $style_selector){

							if(strpos($style_selector_name,"_divider") === false){
								$count ++;
								
								$options_ar = array('hex'=>'','filename'=>'','fileurl'=>'','usebg'=>'','bgreap'=>'','hozpos'=>'','vertpos'=>'');
								foreach($options_ar as $optionsname => $opt){
									if(isset($thisfield->value[$style_selector_name][$optionsname])){
										if(!empty($thisfield->value[$style_selector_name][$optionsname])){
											$options_ar[$optionsname] = $thisfield->value[$style_selector_name][$optionsname];
										}else{
											
											if($thisfield->empty_value === false){
											
											}elseif($thisfield->empty_value === true){
												if(isset($thisfield->default_value[$style_selector_name][$optionsname]))
													$options_ar[$optionsname] = $thisfield->default_value[$style_selector_name][$optionsname];
											}elseif(is_array($thisfield->empty_value)){
												if(isset($thisfield->empty_value[$style_selector_name][$optionsname]))
													$options_ar[$optionsname] = $thisfield->empty_value[$style_selector_name][$optionsname];
											}
										}
									}else{
										if(isset($thisfield->default_value[$style_selector_name][$optionsname]))
											$options_ar[$optionsname] = $thisfield->default_value[$style_selector_name][$optionsname];
									}
								}
						?>
								<input type="hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][hex]" 		value="<?php echo $options_ar['hex']; ?>"  />
								<input type="hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][filename]" value="<?php echo $options_ar['filename']; ?>"  />
								<input type="hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][fileurl]" 	value="<?php echo $options_ar['fileurl']; ?>" />
								<input type="hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][usebg]" 	value="<?php echo $options_ar['usebg']; ?>"  />
								<input type="hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][bgreap]" 	value="<?php echo $options_ar['bgreap']; ?>"  />
								<input type="hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][hozpos]" 	value="<?php echo $options_ar['hozpos']; ?>"  />
								<input type="hidden" name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][vertpos]" 	value="<?php echo $options_ar['vertpos']; ?>"  />
								
                                <input type="hidden" name="tppodefault[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][hex]" 		value="<?php echo $thisfield->default_value[$style_selector_name]['hex']; ?>"  />
								<input type="hidden" name="tppodefault[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][filename]" value="<?php echo $thisfield->default_value[$style_selector_name]['filename']; ?>"  />
								<input type="hidden" name="tppodefault[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][fileurl]" 	value="<?php echo $thisfield->default_value[$style_selector_name]['fileurl']; ?>" />
								<input type="hidden" name="tppodefault[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][usebg]" 	value="<?php echo $thisfield->default_value[$style_selector_name]['usebg']; ?>"  />
								<input type="hidden" name="tppodefault[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][bgreap]" 	value="<?php echo $thisfield->default_value[$style_selector_name]['bgreap']; ?>"  />
								<input type="hidden" name="tppodefault[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][hozpos]" 	value="<?php echo $thisfield->default_value[$style_selector_name]['hozpos']; ?>"  />
								<input type="hidden" name="tppodefault[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][<?php echo $style_selector_name?>][vertpos]" 	value="<?php echo $thisfield->default_value[$style_selector_name]['vertpos']; ?>"  />
								
								<div id="help_text_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_<?php echo $style_selector_name?>" style="display:none"><?php echo $style_selector['description']?></div>
						<?php		
							}else{
								$options_ar = array('hex'=>'','filename'=>'','fileurl'=>'','usebg'=>'','bgreap'=>'','hozpos'=>'','vertpos'=>'');
								foreach($options_ar as $optionsname => $opt){
									?>
									<div id="help_text_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_<?php echo $style_selector_name?>" style="display:none"><?php echo $style_selector['description']?></div>
                                    <?php									
								}
							}

							if($count == 1 && !$shownfirst){
								$shownfirst = true;
								$options .= "<option value=\"".$style_selector_name."\" title=\"".$style_selector['display']."\" selected>".$style_selector['title']."</option>";
							}else{
								$options .= "<option value=\"".$style_selector_name."\" title=\"".$style_selector['display']."\">".$style_selector['title']."</option>";
							}
						}
					}
				?>
                
                <select id="style_selector_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" class="style_selector">
	                <?php echo $options; ?>
                </select>
                
                <span id="help_text_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" class="help-text"></span>
                
                <div id="color_picker_div" class="style_editor_divs hidden">
                    <p id="colorpickerHolder_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" style="clear:both; display:block;"></p>                  
				</div>
                           
                <div id="image_upload_div" class="style_editor_divs hidden" style="margin-top:-25px;">     

                    <div id="fileQueue"></div>
                    <input type="file" name="uploadify_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" id="uploadify_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" />

                	<span class="help-text" style="margin-top:8px;">To upload a new image, click the browse button above.<br />Alternatively, you could use an absolute reference below:</span>
                    <input autocomplete="off" type="text" name="file_url_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" id="file_url_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" />
                	<span class="help-text">This should start with http://</span>
                    <span class="help-text" style="margin-top:8px;">If your image has been properly uploaded or located, it will be displayed below:</span>
                    
                    <div style="clear:both; float:left; width:100%; margin:10px 0;" id="imagebox_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>">
                        <img src="" class="image_upload_preview" id="image_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>">
	                    <input type="button" value="REMOVE IMAGE" id="remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" style="cursor:pointer; clear:both; float:left; margin:15px 0 5px;" />
                    </div>
                    

                </div>
            
                <div id="bg_editor_div" class="style_editor_divs hidden">
                <table style="width:100%;">
                	<tr>
                	<td width="45%">

                        <label class="bg_editor">Use of Background:</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg_color" value="color" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg_color">Color Only</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg_image" value="image" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg_image">Image Only</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg_both" value="both" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_usebg_both">Color and Image</label>
                        
                        <label class="bg_editor">Background Repeat:</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap_repeatx" value="repeatx" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap_repeatx">Repeat X</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap_repeaty" value="repeaty" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap_repeaty">Repeat Y</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap_none" value="none" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_bgreap_none">No Repeat</label>
              		
                    </td>
                    <td width="15%">&nbsp;</td>
                    <td width="40%">
                    
                    	<label class="bg_editor">Horizontal Position:</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos_top" value="top" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos_top">TOP</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos_middle" value="middle" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos_middle">MIDDLE</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos_bottom" value="bottom" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_hozpos_bottom">BOTTOM</label>
                        
                        <label class="bg_editor">Vertical Position:</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos_left" value="left" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos_left">LEFT</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos_middle" value="middle" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos_middle">MIDDLE</label>
                        <input type="radio" alt="<?php echo $thisfield->option_name?>_cb" name="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos" id="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos_right" value="right" onfocus="if(this.blur)this.blur()" />
                        <label for="radio_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>_vertpos_right">RIGHT</label>
                        
                    </td>
                    </tr>
                </table>
                </div>
                <input type="button" value="RESET TO DEFAULTS" id="reset_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" style="cursor:pointer; clear:both; float:left; margin:15px 0 5px;" />
			</div>
			<div class="clear"></div>
		</li>	
		<?php
	}
?>