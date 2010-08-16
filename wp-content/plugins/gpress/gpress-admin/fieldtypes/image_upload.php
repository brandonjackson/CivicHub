<?php
	function display_tppo_image_upload($thisfield, $tpp_id){		
		$file_name = $thisfield->value['filename'];
		$file_url = $thisfield->value['fileurl'];
		?>
		<li>
		  	<label class="general-label"><?php echo $thisfield->field_label; ?>:</label>
		  	<div class="general-input">

                <link href="<?php echo TPPO_URL; ?>/fieldtypes/image_upload/css/default.css" rel="stylesheet" type="text/css" />
                <link href="<?php echo TPPO_URL; ?>/fieldtypes/image_upload/css/uploadify.css" rel="stylesheet" type="text/css" />
                
                <script type="text/javascript" src="<?php echo TPPO_URL; ?>/fieldtypes/image_upload/scripts/swfobject.js"></script>
                <script type="text/javascript" src="<?php echo TPPO_URL; ?>/fieldtypes/image_upload/scripts/jquery.uploadify.v2.1.0.min.js"></script>
                
                <script type="text/javascript">
                jQuery(document).ready(function() {
												
					<?php if(is_multisite()) { ?>
						<?php global $blog_id; if($blog_id != 1) { ?>
							var folder = '../../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
						<?php } else { ?>
							var folder = '../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
						<?php } ?>
					<?php } else { ?>
						var folder = '../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
					<?php } ?>
				
                    jQuery("#uploadify_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>").uploadify({
                        'uploader'    	: '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/uploadify.swf',
                        'script'        : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/uploadify.php',
                        'checkScript'   : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/check.php',
                        'cancelImg'     : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/cancel.png',
                        'folder'        : folder,
                        'queueID'       : 'fileQueue_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>',
                        'auto'          : true,
						'fileDesc'		: 'Images Only (*.jpg;*.gif;*.png)',
						'fileExt'		: '*.jpg;*.gif;*.png',
						'buttonImg'		: '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/button.png',
						'rollover'		: true,
                        'multi'         : false,
						'onComplete'	: function(event,queueID,fileObj,response,data){
							var json = eval("(" + response + ')');
							if(json.error == 0){
								
								jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\[filename\\]]').val(fileObj.name);
							}else{
								
								jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\[filename\\]]').val('');
							}
							alert(json.msg);
						}
                    });
					jQuery('#remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').click(function() {
						if(confirm('Are you sure you want to remove this image...?')) {
							jQuery('input[name=tppo\\[<?php echo $thisfield->option_type?>\\]\\[<?php echo $tpp_id?>\\]\\[<?php echo $thisfield->option_name?>\\]\\[filename\\]]').val('');
							jQuery('#image_preview_filename_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').hide();
							jQuery('#remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>').hide();
							alert("Image will be removed after you SAVE CHANGES");
						}
					});
                });
                </script>
                
                <span class="help-text"><?php echo $thisfield->field_description?></span>
                <div id="fileQueue_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>"></div>
                <input type="file" name="uploadify_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" id="uploadify_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" />
                <span class="help-text" style="margin-top:8px;">To upload a new image, click the browse button above.<br />Alternatively, you could use an absolute reference below:</span>
                <input name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][fileurl]" type="text" value="<?php echo $thisfield->value['fileurl']; ?>">
                <span class="help-text" style="margin-top:8px;">This should start with http://</span>
                <span class="help-text" style="margin-top:8px;">If your image has been properly uploaded or located, it will be displayed below:</span>

                <?php if(!empty($file_url)) { ?>
                	<div style="clear:both; float:left; width:100%; margin:10px 0;">
		                <img name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>]" src="<?php echo $file_url; ?>" class="image_upload_preview">
                    </div>
                <?php } else { ?>                
					<?php if(!empty($file_name)) { ?>
                        <div style="clear:both; float:left; width:100%; margin:10px 0;">
                            <img name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>]" src="<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/uploads/<?php echo $file_name; ?>" class="image_upload_preview" id="image_preview_filename_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>">
                            <input type="button" value="REMOVE IMAGE" id="remove_<?php echo $thisfield->option_type?>_<?php echo $tpp_id?>_<?php echo $thisfield->option_name?>" style="cursor:pointer; clear:both; float:left; margin:15px 0 5px;" />
                        </div>
                    <?php } ?>
                <?php } ?>
                 
				<input name="tppo[<?php echo $thisfield->option_type?>][<?php echo $tpp_id?>][<?php echo $thisfield->option_name?>][filename]" type="hidden" value="<?php echo $thisfield->value['filename']; ?>">

			</div>
			
			<div class="clear"></div>
		</li>	
		<?php
	}
?>