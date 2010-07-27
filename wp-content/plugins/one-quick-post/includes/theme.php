<?php
//////////////TEMPLATES////////////////////
function oqp_enqueue_url($file){
	// split template name at the slashes
	
	$stylesheet_path = get_stylesheet_directory_uri();
	$suffix = explode($stylesheet_path,$file);	
	
	$suffix_str=$suffix[1];
	
	$file_path_to_check = ONEQUICKPOST_PLUGIN_DIR . '/themes'.$suffix_str;
	$file_url_to_return = ONEQUICKPOST_PLUGIN_URL . '/themes'.$suffix_str;

	if ( file_exists($file)) {
		return $file;
	}elseif ( file_exists($file_path_to_check)) {
		return $file_url_to_return;
	}
}
add_filter( 'oqp_enqueue_url', 'oqp_enqueue_url' );

/**
 * Check if template exists in style path, then check custom plugin location (code snippet from MrMaz)
 *
 * @param array $template_names
 * @param boolean $load Auto load template if set to true
 * @return string
 */
function oqp_locate_template( $template_names, $load = false ) {

	if ( !is_array( $template_names ) )
		return '';

	$located = '';
	foreach($template_names as $template_name) {

		// split template name at the slashes
		$paths = explode( '/', $template_name );

		// only filter templates names that match our unique starting path
		if ( !empty( $paths[0] ) && 'oqp' == $paths[0] ) {


			$style_path = STYLESHEETPATH . '/' . $template_name;
			$plugin_path = ONEQUICKPOST_PLUGIN_DIR . "/themes/{$template_name}";

			if ( file_exists( $style_path )) {
				$located = $style_path;
				break;
			} else if ( file_exists( $plugin_path ) ) {
				$located = $plugin_path;
				break;
			}
		}
	}

	if ($load && '' != $located)
		load_template($located);

	return $located;
}

/**
 * Filter located BP template (code snippet from MrMaz)
 *
 * @see bp_core_load_template()
 * @param string $located_template
 * @param array $template_names
 * @return string
 */
function oqp_filter_template( $located_template, $template_names ) {

	// template already located, skip
	if ( !empty( $located_template ) )
		return $located_template;

	// only filter for our component
	if ( $bp->current_component == $bp->quickpress->slug ) {
		return oqp_locate_template( $template_names );
	}

	return '';
}
add_filter( 'bp_located_template', 'oqp_filter_template', 10, 2 );

/**
 * Use this only inside of screen functions, etc (code snippet from MrMaz)
 *
 * @param string $template
 */
function oqp_load_template( $template ) {
	bp_core_load_template( $template );
}

function oqp_get_template_html($file) {

	$template_names[]=$file;

	$filename = oqp_locate_template($template_names,false);


    if (is_file($filename)) {

        ob_start();
		
        include $filename;
		
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }
    return false;
}
/////////////////////
function oqp_js_pictures($the_blog_id,$form_id,$post_id) {
	global $blog_id;
	
	if (($the_blog_id) && ($the_blog_id!=$blog_id))
		$siteurl = get_blog_option($the_blog_id,'siteurl');
	else
		$siteurl = get_option('siteurl');


	$js[] = '<script type="text/javascript">';
		$js[] = 'var oqp_form_'.$form_id.'_js=new Object();';
		$js[] = 'oqp_form_'.$form_id.'_js.tb_pathToImage="'.$siteurl.'/wp-includes/js/thickbox/loadingAnimation.gif";';
		$js[] = 'oqp_form_'.$form_id.'_js.tb_closeImage="'.$siteurl.'/wp-includes/js/thickbox/tb-close.png";';
	$js[] = '</script>';
	return implode("\n",$js);
}

function oqp_js_autocomplete($the_blog_id,$form_id,$form_field_id,$taxonomy_name) {
	global $blog_id;
	
	if (($the_blog_id) && ($the_blog_id!=$blog_id))
		$wpurl = get_blog_option($the_blog_id,'siteurl');
	else
		$wpurl = get_bloginfo('wpurl');


	$js[] = '<script type="text/javascript">';
	$js[] = '//<![CDATA[';
	$js[] = 'jQuery(document).ready( function() {';
	$js[] = '	jQuery("#'.$form_id.' #'.$form_field_id.'").autocomplete("'.$wpurl.'/wp-admin/admin-ajax.php?action=ajax-tag-search&tax='.$taxonomy_name.'", {';
	$js[] = '		width: jQuery(this).width,';
	$js[] = '		multiple: true,';
	$js[] = '		matchContains: true,';
	$js[] = '		minChars: 3,';
	$js[] = '	});';
	$js[] = '});';
	$js[] = '//]]>';
	$js[] = '</script>';
	return implode("\n",$js);
}


?>