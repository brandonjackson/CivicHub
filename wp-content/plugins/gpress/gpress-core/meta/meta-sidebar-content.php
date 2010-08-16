<link href="<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/css/uploadify.css" rel="stylesheet" type="text/css" />
                
<script type="text/javascript" src="<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/swfobject.js"></script>
<script type="text/javascript" src="<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/jquery.uploadify.v2.1.0.min.js"></script>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="gpress_meta_sidebar_table">
  <tr>
    <td width="50%">
        <label><?php echo __('Map Type:', 'gpress'); ?></label>
        <input type="radio" name="<?php echo $gpress_map_id; ?>[type]" value="ROADMAP" <?php if(($meta['type'] == 'ROADMAP') || ($meta['type'] == '')) { ?> checked="checked" <?php } ?> /><span class="input_label">Roadmap</span><br />
        <input type="radio" name="<?php echo $gpress_map_id; ?>[type]" value="SATELLITE" <?php if($meta['type'] == 'SATELLITE') { ?> checked="checked" <?php } ?> /><span class="input_label">Satellite</span><br />
        <input type="radio" name="<?php echo $gpress_map_id; ?>[type]" value="HYBRID" <?php if($meta['type'] == 'HYBRID') { ?> checked="checked" <?php } ?> /><span class="input_label">Hybrid</span><br />
        <input type="radio" name="<?php echo $gpress_map_id; ?>[type]" value="TERRAIN" <?php if($meta['type'] == 'TERRAIN') { ?> checked="checked" <?php } ?> /><span class="input_label">Terrain</span><br />
    </td>
    <td width="10px" class="divider">&nbsp;</td>
    <td width="50%">
        <label><?php echo __('Zoom Level:', 'gpress'); ?></label>
        <input type="radio" name="<?php echo $gpress_map_id; ?>[zoom]" value="18" <?php if($meta['zoom'] == '18') { ?> checked="checked" <?php } ?> /><span class="input_label">Close-Up</span><br />
        <input type="radio" name="<?php echo $gpress_map_id; ?>[zoom]" value="13" <?php if(($meta['zoom'] == '13') || ($meta['zoom'] == '' )) { ?> checked="checked" <?php } ?> /><span class="input_label">Nearby</span><br />
        <input type="radio" name="<?php echo $gpress_map_id; ?>[zoom]" value="10" <?php if($meta['zoom'] == '10') { ?> checked="checked" <?php } ?> /><span class="input_label">Cities</span><br />
        <input type="radio" name="<?php echo $gpress_map_id; ?>[zoom]" value="5" <?php if($meta['zoom'] == '5') { ?> checked="checked" <?php } ?> /><span class="input_label">Countries</span><br />
    </td>
  </tr>
</table>

<div class="advanced_holder">
    <p><a href="#" id="advanced_settings"><?php echo __('Advanced Settings', 'gpress'); ?></a></p>
    <div id="gpress_advanced_hidden" style="display:none;">
        <span class="advanced_divider"></span>
        <label><?php echo __('Overwrite default height for this map:', 'gpress'); ?></label>
        <input type="text" name="<?php echo $gpress_map_id; ?>[height]" value="<?php if(!empty($meta['height'])) echo $meta['height']; ?>" />
        <p class="advanced_description"><?php echo __('Numbers only (defaults to', 'gpress'); ?> <?php echo $default_map_height; ?>)</p>
        
        
        <span class="advanced_divider"></span>
        <label>Custom icon URL for this map:</label>
        <input type="text" name="<?php echo $gpress_map_id; ?>[icon_url]" value="<?php if(!empty($meta['icon_url'])) echo $meta['icon_url']; ?>" />
        <p class="advanced_description">This <strong>MUST</strong> start with http://<br/>The image <strong>MUST</strong> also be 30px X 30px</p>
        <p class="advanced_description">If you want to upload an image directly, please click on the BROWSE button below:</p>
        
		<script type="text/javascript">
		
		<?php if(is_multisite()) { ?>
			<?php global $blog_id; if($blog_id != 1) { ?>
				var folder = '../../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
			<?php } else { ?>
				var folder = '../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
			<?php } ?>
		<?php } else { ?>
			var folder = '../wp-content/plugins/gpress/gpress-admin/fieldtypes/image_upload/uploads';
		<?php } ?>
		
        jQuery(document).ready(function() {
            jQuery("#gpress_marker_icon_file").uploadify({
                'uploader'    	: '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/uploadify.swf',
                'script'        : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/uploadify.php',
                'checkScript'   : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/check.php',
                'cancelImg'     : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/cancel.png',
                'folder'        : folder,
                'queueID'       : 'fileQueue_icon',
                'auto'          : true,
                'fileDesc'		: 'Images Only (*.jpg;*.gif;*.png)',
                'fileExt'		: '*.jpg;*.gif;*.png',
                'buttonImg'		: '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/button.png',
                'rollover'		: true,
                'multi'         : false,
                'onComplete'	: function(event,queueID,fileObj,response,data){
                    var json = eval("(" + response + ')');
                    if(json.error == 0){
                        jQuery('input#gpress_marker_icon_file_hidden').val(fileObj.name);
                    }else{
                        jQuery('input#gpress_marker_icon_file_hidden').val('');
                    }
                    alert(json.msg);
                }
            });
            jQuery('#remove_marker').click(function() {
                if(confirm('Are you sure you want to remove this image...?')) {
                    jQuery('input#gpress_marker_icon_file_hidden').val('');
                    jQuery('#marker_icon_preview').hide();
                    jQuery('#remove_marker').hide();
                    alert("Image will be removed after you SAVE CHANGES");
                }
            });
        });
        </script>
        
        <div id="fileQueue_icon"></div>
        <input type="file" id="gpress_marker_icon_file" style="clear:both; float:left; margin:5px;" />
        <input name="<?php echo $gpress_map_id; ?>[icon_file]" id="gpress_marker_icon_file_hidden" type="hidden" value="<?php if(!empty($meta['icon_file'])) echo $meta['icon_file']; ?>">

        <?php 
		$marker_icon = $meta['icon_url'];
		$marker_icon_file = $meta['icon_file'];
		if(!empty($marker_icon)) { ?>
            <div style="clear:both; width:100%; margin:10px 0; display:block">
            	<p class="advanced_description">Your marker will appear as follows:</p>
                <img src="<?php echo $marker_icon; ?>" id="marker_icon_preview">
            </div>
        <?php } else { ?>                
            <?php if(!empty($marker_icon_file)) { ?>
                <div style="clear:both; width:100%; margin:10px 0; display:block">
	                <p class="advanced_description">Your marker will appear as follows:</p>
                    <img src="<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/uploads/<?php echo $marker_icon_file; ?>" id="marker_icon_preview">
                    <input type="button" value="REMOVE IMAGE" id="remove_marker" style="cursor:pointer; clear:both; margin:15px 0 5px; display:block" />
                </div>
            <?php } ?>
        <?php } ?>
        
        <span class="advanced_divider"></span>
        <label>Custom shadow URL for this map:</label>
        <input type="text" name="<?php echo $gpress_map_id; ?>[shadow_url]" value="<?php if(!empty($meta['shadow_url'])) echo $meta['shadow_url']; ?>" />        
        <p class="advanced_description">This <strong>MUST</strong> start with http://<br/>The image <strong>MUST</strong> also be 40px X 40px</p>
        <p class="advanced_description">If you want to upload an image directly, please click on the BROWSE button below:</p>
                
		<script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery("#gpress_marker_shadow_file").uploadify({
                'uploader'    	: '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/uploadify.swf',
                'script'        : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/uploadify.php',
                'checkScript'   : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/scripts/check.php',
                'cancelImg'     : '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/cancel.png',
                'folder'        : folder,
                'queueID'       : 'fileQueue_shadow',
                'auto'          : true,
                'fileDesc'		: 'Images Only (*.jpg;*.gif;*.png)',
                'fileExt'		: '*.jpg;*.gif;*.png',
                'buttonImg'		: '<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/button.png',
                'rollover'		: true,
                'multi'         : false,
                'onComplete'	: function(event,queueID,fileObj,response,data){
                    alert(fileObj.name);
                    var json = eval("(" + response + ')');
                    if(json.error == 0){
                        jQuery('input#gpress_marker_shadow_file_hidden').val(fileObj.name);
                    }else{
                        jQuery('input#gpress_marker_shadow_file_hidden').val('');
                    }
                    alert(json.msg);
                }
            });
            jQuery('#remove_shadow').click(function() {
                if(confirm('Are you sure you want to remove this image...?')) {
                    jQuery('input#gpress_marker_shadow_file_hidden').val('');
                    jQuery('#marker_shadow_preview').hide();
                    jQuery('#remove_shadow').hide();
                    alert("Image will be removed after you SAVE CHANGES");
                }
            });
        });
        </script>
        
        <div id="fileQueue_shadow"></div>
        <input type="file" id="gpress_marker_shadow_file" style="clear:both; float:left; margin:5px; display:block" />
        <input name="<?php echo $gpress_map_id; ?>[shadow_file]" id="gpress_marker_shadow_file_hidden" type="hidden" value="<?php if(!empty($meta['shadow_file'])) echo $meta['shadow_file']; ?>">

        <?php 
		$marker_shadow = $meta['shadow_url'];
		$marker_shadow_file = $meta['shadow_file'];
		if(!empty($marker_shadow)) { ?>
            <div style="clear:both; width:100%; margin:10px 0; display:block">
	            <p class="advanced_description">Your shadow will appear as follows:</p>
                <img src="<?php echo $marker_shadow; ?>" id="marker_shadow_preview">
            </div>
        <?php } else { ?>                
            <?php if(!empty($marker_shadow_file)) { ?>
                <div style="clear:both; width:100%; margin:10px 0;">
	                <p class="advanced_description">Your shadow will appear as follows:</p>
                    <img src="<?php echo GPRESS_URL; ?>/gpress-admin/fieldtypes/image_upload/uploads/<?php echo $marker_shadow_file; ?>" id="marker_shadow_preview">
                    <input type="button" value="REMOVE IMAGE" id="remove_shadow" style="cursor:pointer; clear:both; margin:15px 0 5px; display:block" />
                </div>
            <?php } ?>
        <?php } ?>
        
        <?php
		
		if(!empty($marker_shadow_file)) {
			if(!empty($marker_shadow)) {
				$full_marker_shadow = $marker_shadow;
			}else{
				$full_marker_shadow = GPRESS_URL .'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_shadow_file;
			}
		}else{
			if(!empty($marker_shadow)) {
				$full_marker_shadow = $marker_shadow;
			}
		}
		
		if(!empty($marker_icon_file)) {
			if(!empty($marker_icon)) {
				$full_marker_icon = $marker_icon;
			}else{
				$full_marker_icon = GPRESS_URL .'/gpress-admin/fieldtypes/image_upload/uploads/'.$marker_icon_file;
			}
		}else{
			if(!empty($marker_icon)) {
				$full_marker_icon = $marker_icon;
			}
		}
		
		?>
        
        <?php if(!empty($full_marker_shadow)) { ?>
        	<?php if(!empty($full_marker_icon)) { ?>
            	<style>
				#full_marker_preview_bg {
					height:42px;
					width:42px;
					background:url("<?php echo $full_marker_shadow; ?>") no-repeat top left;
					margin:0 0 0 5px;
				}
				#full_marker_preview {
					float:left;
					height:42px;
					width:42px;
					background:url("<?php echo $full_marker_icon; ?>") no-repeat top left;
					margin:5px;
				}
				</style>
                <p class="advanced_description"><br /><br />When combined &amp seen on maps, the marker &amp shadow should appear as follows:</p>
            	<div id="full_marker_preview_bg">
                	<div id="full_marker_preview"></div>
                </div>
            <?php } ?>
        <?php } ?>
        
    </div>
</div>