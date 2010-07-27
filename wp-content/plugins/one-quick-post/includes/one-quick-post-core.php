<?php

//TO FIX :
//problem when using quotes in an input field.  Need to convert the content into html chars.
//missing taxonomies message must appear on page load, not only on submit

require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/classes.php');

function oqp_wp() {
	$options = get_option('oqp_options');
	
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/form-template.php');
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/terms-template.php');
	
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/theme.php');
	if ((class_exists('siCaptcha')) && ($options['captcha'])) {
		require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/si-captcha.php');
	}
}
function oqp_admin() {
	if (!is_admin) return false;
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/admin-settings.php');
}


add_action('wp','oqp_wp',1);
add_action('init','oqp_admin');


if (!function_exists('bp_core_setup_message') && !is_admin()) {
//if ( !defined( 'BP_VERSION' ) && !did_action( 'bp_init' ) && !is_admin() ) {
	//loads duplicated core messages functions from BP if BP is not enabled (so we can use the same function with or without BP).
	//!is_admin to avoir having FATAL ERROR when activating BP after OQP.
	require_once( ONEQUICKPOST_PLUGIN_DIR . '/includes/bp-core-messages.php'); 
}
//require 'oqp-gallery.php';


 


//if the poster is a guest; filter guest email
function oqp_notification_mail_to($email,$post) {

	if (!oqp_user_is_dummy($post->post_author)) return $email;

	$email = get_post_meta($post->ID,'oqp_guest_email', true);

	return $email;
}



function oqp_get_blogs_of_user($user_id) {

	$blogs = get_blogs_of_user($user_id);
	
	return $blogs;

}

function oqp_is_multiste() {

	if ( function_exists( 'is_multisite' ) )
		return is_multisite();

	if ( !function_exists( 'wpmu_signup_blog' ) )
		return false;

	return true;
}

function oqp_can_user_post() {
	if(is_user_logged_in())
		return true;
		
	if (oqp_get_dummy_user())
		return true;
		
	return false;
}

function oqp_get_dummy_user() {
	global $oqp;
	$user_id = $oqp->options['guest_poster'];
	
	if (!$user_id) return false;
	
	$user = new WP_User($user_id);

	return $user;
}

function oqp_user_is_dummy($user_id) {
	$dummy = oqp_get_dummy_user();
	
	if (!$dummy) return false;
	
	if ($dummy->ID==$user_id) return true;
	
	return false;
	
}

function oqp_post_get_guest_name($post_id) {
	return get_post_meta($post_id,'oqp_guest_name', true);
}
function oqp_post_get_guest_email($post_id) {
	return get_post_meta($post_id,'oqp_guest_email', true);
}

function oqp_user_can_for_blog($cap,$user_id=false,$blog_id=false) {
	global $current_user;

	if ((!$user_id) || ($user_id==$current_user->id)) {
		$user=$current_user;
	}else {
		$user = new WP_User($user_id);
	}

	//TO FIX TO CHECK
	//if ((oqp_is_multiste()) && ($blog_id)) {
		//return $user->has_cap($cap);
	//}else {
		return $user->has_cap($cap);
	//}


}

function oqp_get_post_tags($blog_id=false) {
	global $post;
	
	if (oqp_is_multiste())
		switch_to_blog($blog_id);
		
	$post_tags = wp_get_post_tags($post->ID);
	
	if (oqp_is_multiste())
		restore_current_blog();
	
	return $post_tags;
}

function oqp_taxonomy_is_hierarchical($tax_slug) {
	$tax_obj = get_taxonomy( $tax_slug);
	return (bool)$tax_obj->hierarchical;
}

function oqp_save_post() {
	global $oqp_form;
	global $oqp;
	global $blog_id;
	

	if ($_POST['oqp-action']!='oqp-save') return false;
	if ($_POST['oqp-form-id']!=$oqp_form->args['form_id']) return false; //be sure we handle the good form (if there are several OQP forms on the page)
	if ($_POST['oqp-switch-blog-id']) return false; //only switching the blog
	
	

	//post id - (for edition)
	$edit_post_id = $oqp_form->post->ID;

	//USER ID
	if(is_user_logged_in()) {
		global $current_user;
		$user = $current_user;	
		
	}elseif($oqp_form->args['guest_posting']) {
		$user = oqp_get_dummy_user();
	}
	$user_id=$user->id;
	
	
	
	
	//SWITCH BLOG before saving if needed
	if (oqp_is_multiste()) {
	
		if ($_REQUEST['oqp-blog-id']) { //form posted
			$oqp_blog_id=$_REQUEST['oqp-blog-id'];
		}else {
			$oqp_blog_id=$oqp_form->args['blog_id'];
		}
			
		if ($blog_id==$oqp_blog_id)
			unset ($oqp_blog_id);
		
			
		switch_to_blog($oqp_blog_id);
		
	}
	
	//form url
	$form_url = oqp_url_add_args($oqp_form->args['form_url'],array('oqp-blog-id'=>$oqp_blog_id));

	//edit url
	if ($oqp_form->post->ID) {
		$edit_post_url = oqp_post_get_edit_link($oqp_form->post->ID,$oqp_form->key,$oqp_blog_id);
	}else{
		$edit_post_url = $form_url;
	}
	
	$edit_post_url = apply_filters('oqp_save_post_edit_post_url',$edit_post_url,$oqp_form->post->ID,$oqp_form->key,$blog_id);


	if ($oqp_form->is_guest) { //user is not logged, check if guest posting is enabled
		if (oqp_user_can_for_blog('edit_posts',$oqp_form->user_id,$oqp_blog_id)) {
			$dummy_name = $_POST['oqp_dummy_name'];
			$dummy_email = $_POST['oqp_dummy_email'];
		}else {
			bp_core_add_message(__('You are not allowed to post without being logged.','oqp'), 'error' );
			bp_core_redirect($form_url);
		}
	}
	
	$post=array();


	
	//TITLE + DESC
	if ($oqp_form->args['title']) {
		$sent_title = trim($_POST['oqp_title']);
		if (!$sent_title) {
			bp_core_add_message(__('Please enter a title.','oqp'), 'error' );
			bp_core_redirect( $edit_post_url );
		}
		$post['post_title']=$sent_title;
	}
	
	if ($oqp_form->args['desc']) {
		$sent_desc = trim($_POST['oqp_desc']);
		if (!$sent_desc) {
			bp_core_add_message(__('Please enter a description.','oqp'), 'error' );
			bp_core_redirect( $edit_post_url );
		}
		$post['post_content']=$sent_desc;
	}

	
	//GUEST USER
	if ($oqp_form->is_guest) {
		$sent_dummy_name = trim($_POST['oqp_dummy_name']);
		$sent_dummy_email = trim($_POST['oqp_dummy_email']);
		
		if (!$sent_dummy_name)
			$missing_datas_msgs[]=__('Please enter your name','oqp');
		
		if (!is_email($sent_dummy_email))
			$missing_datas_msgs[]=__('Please enter a valid email address.');
	}
	
	//TAXONOMIES
	if (!empty($oqp_form->args['taxonomies'])) {
		foreach ($oqp_form->args['taxonomies'] as $tax_slug=>$tax_settings) {
			
			unset($value);
			
			$value = $_POST['oqp_'.$tax_slug];
			
			//check if the taxonomy value can be empty
			//TO FIX can be empty if no taxonomies existing (hierarchical) ?
			if (!$value) {
				if ($tax_settings['required']) {
					$tax_obj=get_taxonomy($tax_slug);
					$missing_datas_msgs[]=sprintf(__('You have to choose a value for the %s','oqp'),''.$tax_obj->label.'');
				}
			}
			
				
			$taxonomies[$tax_slug]['selected']=$value;

		}
	}


	//POST STATUS
	//first, save as draft.
	//we'll publish it once the metas are saved
	//so we can have the metas to be used in the transition hooks

	if ((oqp_user_can_for_blog('publish_posts',$oqp_form->user_id,$oqp_blog_id)) && (!$oqp_form->is_guest))
		$post_status_final='publish';
	else
		$post_status_final='pending';
		
	$post_status_final=apply_filters('oqp_save_post_post_status_final',$post_status_final);

	if ((!$oqp_form->post->ID) || (!empty($missing_datas_msgs))) { //new post | not valid
		$post_status='draft';
	}else {
		$post_status = $post_status_final;
	}

	$post_status=apply_filters('oqp_save_post_post_status',$post_status);

	//

	$post['post_author'] =  $oqp_form->user_id;
	$post['post_status'] =  $post_status;
	$post['post_type'] =  $oqp_form->args['post_type'];


	//check we can edit the post
	if ($oqp_form->post->ID) {
		if (($oqp_form->post->post_type!=$oqp_form->args['post_type'])) {
			bp_core_add_message(__('Error while trying to get this post','oqp'), 'error' );
			bp_core_redirect($edit_post_url);	
		}
		
		$post['ID'] = $oqp_form->post->ID;
	}
	
	do_action('oqp_save_post_validate',$edit_post_url,$post);


	//SAVE the post
	if ($oqp_form->post) {
		$saved_post_id = wp_update_post( $post );		
	}else{ // new post
		$saved_post_id = wp_insert_post( $post );
		
		update_post_meta($saved_post_id, 'oqp_post_from', $oqp_form->args['form_url']);
		if ($oqp_form->is_guest) { 
			//generate a key to allow guest to edit their posts (mail link)
			$key = substr( md5( uniqid( microtime() ) ), 0, 8);
			update_post_meta($saved_post_id, 'oqp_guest_key', $key);
		}
	}
	
	if (!$saved_post_id) {
		bp_core_add_message(__('Error while trying to save this post','oqp'), 'error' );
		bp_core_redirect($edit_post_url);
	}

	$edit_post_url = oqp_post_get_edit_link($saved_post_id,$key,$oqp_blog_id);
	$edit_post_url = apply_filters('oqp_save_post_edit_post_url',$edit_post_url,$saved_post_id,$key,$oqp_blog_id);

	//TO FIX : only if datas are different
	//save guest name + email
	if ($oqp_form->is_guest) {
		update_post_meta($saved_post_id, 'oqp_guest_name', $dummy_name);
		update_post_meta($saved_post_id, 'oqp_guest_email', $dummy_email);
	}

	//now the post metas have been saved; 
	//re-update the post so we can hook our notifications functions
	$resave_post['ID']=$saved_post_id;
	$resave_post['post_status']=$post_status_final;

	wp_update_post( $resave_post );

	
	//SAVE TAXONOMIES
	if ($taxonomies) {
		foreach($taxonomies as $tax_slug=>$tax_settings) {

			if ((!$tax_settings['selected']) && (!$edit_post_id)) continue; //new post & tax has no value

			$taxonomy_value=apply_filters('oqp_save_post_taxonomy_'.$tax_slug,$tax_settings['selected']);

			wp_set_post_terms( $saved_post_id,$taxonomy_value, $tax_slug);
		}
	}
	
	
	//post datas incomplete
	if (!empty($missing_datas_msgs)) {
		bp_core_add_message($missing_datas_msgs[0], 'error' );
		bp_core_redirect($edit_post_url);
	}

	
	
	//RETRIEVE FRESHLY SAVED POST INFO
	$oqp_post=get_post($saved_post_id); //retrieve saved information
	
	$oqp_form->post=$oqp_post;
	
	do_action('oqp_saved_post',$oqp_form->post);
	
	//REDIRECT TO FORM EDITION

	$message_saved = sprintf(__('Your post %s has been saved.','oqp'),'"'.$oqp_form->post->post_title.'"');
	if ($oqp_form->post->post_status=='pending')
	$message_saved .= __('It is now awaiting moderation.','oqp');
	
	bp_core_add_message($message_saved);

	bp_core_redirect($edit_post_url);
	
	//we don't need to restore the blog as we redirect


}

function oqp_get_the_post($post_id,$blog_id=false) {
	if ((oqp_is_multiste()) && ($blog_id)) {
		
		switch_to_blog($blog_id);
		
		$the_post=get_post($post_id);
		
		restore_current_blog();
		
	}else {
		$the_post=get_post($post_id);
	}
	return $the_post;	
}

function oqp_user_can_edit_the_post() {//user_id must not be the dummy user
	global $oqp_form;

	if (!$oqp_form->post) return false;
	if (!$oqp_form->is_guest) { //user is not a guest
		if ($oqp_post->post_author==$oqp_form->user_id) { //user is the post author 
			return true;
		} else { //user is not the post author 
				
			if (oqp_user_can_for_blog('edit_others_posts',$oqp_form->user_id,$oqp_form->args['blog_id'])) {
				return true;
			}
		}
	}else { //user is a guest
		if ($oqp_form->key) { //there is a key in the URL
			//TO FIX TO CHECK SWITCH BLOG ?
			$post_key = get_post_meta($oqp_form->post->ID,'oqp_guest_key', true);

			if ($post_key==$oqp_form->key) {
				return true;
			}
		}
	}
	
	return false;
	
}

function oqp_url_add_args($url,$args) {
	//check we have vars
	$url_split=explode('?',$url);
	
	if (count($url_split)>1) { //url = /?...
		$separator='&';
	} else {
		$url = rtrim($url, " /"); //remove trailing slash if any
		$separator='/?';
	}
	
		
		
	$link = $url;
	
	if ($args) {
		$ref_args_str=http_build_query($args);
		$link.=$separator.$ref_args_str;
	}
	
	return $link;
}

function oqp_post_get_link($post_id,$blog_id=false) {

	$the_post = oqp_get_the_post($post_id,$blog_id);

	
	$link = $the_post->guid;
	
	return apply_filters('oqp_post_get_link',$link,$the_post,$args);
}

function oqp_post_get_edit_link($post_id,$key=false,$oqp_blog_id=false) {

	$the_post = oqp_get_the_post($post_id,$oqp_blog_id);

	$oqp_from_url = get_post_meta($the_post->ID, 'oqp_post_from',true);
	
	if (!$key)
		$key = get_post_meta($the_post->ID,'oqp_guest_key', true);
	
	if (!$oqp_from_url) {
		$url = get_edit_post_link( $post_id );
	}else {
		$args['oqp-post-id']=$post_id;
		
		if ($oqp_blog_id)
			$args['oqp-blog-id']=$oqp_blog_id;
		
		if (oqp_user_is_dummy($the_post->post_author)) {
			if ($key)
				$args['oqp-key']=$key;
		}
		
		$url = oqp_url_add_args($oqp_from_url,$args);
	}
	
	return apply_filters('oqp_post_get_edit_link',$url,$post,$args);
}

function oqp_post_get_delete_link($post_id,$key=false,$oqp_blog_id=false) {

	$the_post = oqp_get_the_post($post_id,$blog_id);

	$oqp_from_url = get_post_meta($the_post->ID, 'oqp_post_from',true);
	
	if (!$key)
		$key = get_post_meta($the_post->ID,'oqp_guest_key', true);
	
	if (!$oqp_from_url) {
		$url = get_delete_post_link( $post_id );
	}else {
		$args['oqp-action']='delete';
		$args['oqp-post-id']=$post_id;
		
		if ($oqp_blog_id)
			$args['oqp-blog-id']=$oqp_blog_id;
		
		if (oqp_user_is_dummy($the_post->post_author)) {
			if ($key)
				$args['oqp-key']=$key;
		}
		
		$url = oqp_url_add_args($oqp_from_url,$args);
	}
	
	return apply_filters('oqp_post_get_delete_link',$url,$post,$args);
}


//filtering guest poster info

function oqp_guest_the_author($display_name) {
	global $authordata;

	if (!oqp_user_is_dummy($authordata->ID)) return $display_name;
	
	global $post;
	
	$guest_name=oqp_post_get_guest_name($post->ID);
	
	return $guest_name.' ('.__('Guest','oqp').')';

}

add_filter('the_author', 'oqp_guest_the_author');

//filters author link
//adds a variable to be able to filter guest posts loop based on guest email
function oqp_author_link($link, $author_id, $author_nicename) {
	
	if (!oqp_user_is_dummy($author_id)) return $link;

	
	global $post;
	
	$link_split=explode('?',$link);
	
	if (count($link_split)>1)
		$separator='&';
	else
		$separator='?';
		
	$dummy_email = get_post_meta($post->ID,'oqp_guest_email', true);
	$encoded_dummy_email=oqp_simple_encode($dummy_email,'oqp-dummy-email');
	
	$link = $link.$separator.'oqp_gkey='.$encoded_dummy_email;
		
	return apply_filters('oqp_author_link',$link,$author_id, $author_nicename);
	
}

add_filter('author_link', 'oqp_author_link',11,3 );

//add guest email key to query so we can retrieve it after
function oqp_guest_email_add_query_vars($aVars) {
    $aVars[] = "oqp_gkey";
    return $aVars;
}
add_filter('query_vars', 'oqp_guest_email_add_query_vars');

//simple function to encode guest email (not sniffing it from url)
function oqp_simple_encode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
}
//simple function to decode guest email
function oqp_simple_decode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}


//filtering guest loop through email

/*
//If we look for the ads of the guest user; 
//Rather filter by email
*/
function oqp_guest_pre_get_posts($query) {

	$dummy = oqp_get_dummy_user();
	if (!$dummy) return $query; //guest posting disabled
	
	if ($query->query_vars['author_name'] !=$dummy->user_nicename) return $query; //author is not the dummy user

	$encoded_dummy_email=$query->query_vars['oqp_gkey'];

	if (!$encoded_dummy_email) return $query; //no key to analyze

	$dummy_email = oqp_simple_decode($encoded_dummy_email,'oqp-dummy-email');
	global $wp_query;
	
	$wp_query->set('meta_key', 'oqp_guest_email');
	$wp_query->set('meta_value', $dummy_email);
	
	//query_posts('author='.$dummy->ID.'meta_key=oqp_guest_email&meta_value='.$dummy_email.'&post_type=yclad');
	
}
add_filter('pre_get_posts', 'oqp_guest_pre_get_posts');


function oqp_form_user_message() {

	if ($_REQUEST['oqp-action']=='edit') return false;

	global $oqp_form;

	extract($oqp_form->args);

	
	if ($oqp_form->is_guest) {
		if (oqp_user_can_for_blog('edit_posts',$oqp_form->user_id,$blog_id)) {
			$message = __("As you are not logged; you have to give us your name and email.",'oqp');
		}else {
			$message = __("You must be logged to send this form.",'oqp');
		}
	}
	
	if ($message) {
		?>
		<div id="message" class="info">
			<p>
			<?php echo $message;?>
			</p>
		</div>
		<?php		
	}
	
}
add_action('oqp_creation_form_before_fields','oqp_form_user_message');

function oqp_populate_post() {
	global $oqp;
	global $oqp_form;
	global $oqp_errors;
	global $current_user;
	global $post;

	$oqp_form->post=oqp_get_the_post($oqp_form->args['post_id'],$oqp_form->args['blog_id']);
	
	if (!$oqp_form->post) {
		bp_core_add_message(__('This post do not exists.','oqp'), 'error' );
		bp_core_redirect($oqp_form->args['form_url']);
	}

	//check the user can edit this post
	if (!oqp_user_can_edit_the_post()) {
		bp_core_add_message(__('You are not allowed to edit this post.','oqp'), 'error' );
		bp_core_redirect( oqp_post_get_link($oqp_form->post->ID,$oqp_form->args['blog_id']) );
		//return false;
	}

	//check we can edit a published post
	if ($oqp_post->post_status=='publish') {
		if (!oqp_user_can_for_blog('edit_published_posts',$oqp_form->user_id,$oqp_form->args['blog_id'])) { //user_id is the guest user ID or the current user id
			bp_core_add_message(__('You are not allowed to edit a published post.','oqp'), 'error' );
			bp_core_redirect( oqp_post_get_link($oqp_form->post->ID,$oqp_form->args['blog_id']) );
		}
	}
	
	//get the selected taxonomies

	if ($oqp_form->args['taxonomies']) {
		$oqp_form->oqp_block_arg_string_to_array('taxonomies');
		foreach($oqp_form->args['taxonomies'] as $tax_slug=>$tax_settings) {
			$oqp_form->args['taxonomies'][$tax_slug]['selected']='';
			$tax_list = oqp_get_the_terms_list($tax_slug,$oqp_form->post->ID,$oqp_form->args['blog_id']); //$cats = the taxonomy, see oqp_block args.
			if (!is_array($tax_list)) { //no taxonomy error
				$oqp_form->args['taxonomies'][$tax_slug]['selected']=strip_tags($tax_list);
			}
		}
	}
	/*
	//get the selected custom metas
	if ($oqp_form->args['metas']) {
		$oqp_form->oqp_block_arg_string_to_array('metas');

		foreach($oqp_form->args['metas'] as $meta_slug=>$meta_settings) {

		}
	}
	*/
	
}


function oqp_destroy_vars() {
	global $oqp_form;

	unset ($oqp_form->post);
	unset ($oqp_form->args);

}




function oqp_form_init() {
	add_action( 'oqp_creation_form_after_fields', 'oqp_destroy_vars',3);
	add_action( 'oqp_before_template', 'oqp_save_post', 3 );

}
add_action('wp','oqp_form_init');





?>