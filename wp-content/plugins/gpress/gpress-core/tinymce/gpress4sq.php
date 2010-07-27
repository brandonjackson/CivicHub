<?php

// FINDING WP
include('../../../../../wp-load.php');

// ONCE WP LOADED
require_once(ABSPATH.'/wp-admin/admin.php');
if(!current_user_can('edit_posts')) die;
do_action('admin_init');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<title><?php echo __('gPress Foursquare TinyMCE Editor', 'gpress'); ?></title>
    
	<?php 
	include( GPRESS_DIR. '/gpress-core/tinymce/scripts.php' ); 
	?>
    
	<script language="javascript" type="text/javascript">
	
	var _self = tinyMCEPopup;
	
	function insertTag () {
		
		var tag = '';

		var fouryou = jQuery("input[name='four_you']:checked").val();
		var fourfriends = jQuery("input[name='four_friends']:checked").val();
		
		if(fouryou) {
			var four_you = ' four_you="true"';
		}
		if(fourfriends) {
			var four_friends = ' four_friends="true"';
		}
		
		tag = '[gpress map_id="_foursquare"'+four_you+''+four_friends+']';
		
		if(window.tinyMCE) {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tag);
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
				
	}
	
	function closePopup () {
		tinyMCEPopup.close();
	}
		
	</script>
	
</head>
<body>

<div id="wpwrap">
    <form onsubmit="insertTag();return false;" action="#" id="gpress_tinymce">
    
		<?php include( GPRESS_DIR. '/gpress-core/tinymce/foursquare.php' ); ?>
            
        <div class="mceActionPanel">
            <div style="float: left">
                <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="closePopup()"/>
            </div>
    
            <div style="float: right">
                <input type="button" id="insert" name="insert" value="{#insert}" onclick="insertTag()" />
            </div>
        </div>
        
    </form>
</div>

</body>
</html>