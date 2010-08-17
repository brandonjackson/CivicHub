<?php
/* Plugin Name: WP-FB-AutoConnect
 * Description: A LoginLogout widget with Facebook Connect button, offering hassle-free login for your readers.  Also provides a good starting point for coders looking to add more customized Facebook integration to their blogs.
 * Author: Justin Klein
 * Version: 1.3.2
 * Author URI: http://www.justin-klein.com/
 * Plugin URI: http://www.justin-klein.com/projects/wp-fb-autoconnect
 */


/*
 * Copyright 2010 Justin Klein (email: justin@justin-klein.com)
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


require_once("__inc_opts.php");
require_once("AdminPage.php");
require_once("Widget.php");


/**********************************************************************/
/*******************************GENERAL********************************/
/**********************************************************************/

/*
 * Output a Facebook Connect Button.  Note that the button will not function until you've called 
 * jfb_output_facebook_init().  I use document.write() because the button isn't XHTML valid.
 */
function jfb_output_facebook_btn()
{
    global $jfb_name, $jfb_version, $jfb_js_callbackfunc, $opt_jfb_valid;
    echo "<!-- $jfb_name v$jfb_version -->\n";
    if( !get_option($opt_jfb_valid) )
    {
        echo "<!--WARNING: Invalid or Unset Facebook API Key-->";
        return;
    }
    ?>
    <script type="text/javascript">//<!--
    document.write('<span id="fbLoginButton"><fb:login-button v="2" size="small" onlogin="<?php echo $jfb_js_callbackfunc?>();">Login with Facebook</fb:login-button></span>');
    //--></script>
    <?php
}


/*
 * As an alternative to jfb_output_facebook_btn, this will setup an event to automatically popup the
 * Facebook Connect dialog as soon as the page finishes loading (as if they clicked the button manually) 
 */
function jfb_output_facebook_instapopup( $callbackName=0 )
{
    global $jfb_js_callbackfunc;
    if( !$callbackName ) $callbackName = $jfb_js_callbackfunc;
    ?>
    <script type="text/javascript">//<!--
    function showPopup()
    {
        FB.ensureInit( function(){FB.Connect.requireSession(<?php echo $callbackName?>);}); 
    }
    window.onload = showPopup;
    //--></script>
    <?php
}


/*
 * Output the JS to init the Facebook API, which will also setup a <fb:login-button> if present. 
 */
function jfb_output_facebook_init()
{
    global $opt_jfb_app_id, $opt_jfb_api_key, $opt_jfb_valid;
    if( !get_option($opt_jfb_valid) ) return;
    $xd_receiver = plugins_url(dirname(plugin_basename(__FILE__))) . "/facebook-platform/xd_receiver.htm";
    ?>
    <script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"></script>
    <script type="text/javascript">//<!--
    FB.init("<?php echo get_option($opt_jfb_api_key)?>","<?php echo $xd_receiver?>");
    //--></script>
    <?php  
}



/*
 * Output the JS callback function that'll handle FB logins
 */
function jfb_output_facebook_callback($redirectTo=0, $callbackName=0)
{
     global $opt_jfb_ask_perms, $opt_jfb_req_perms, $opt_jfb_valid, $jfb_nonce_name, $jfb_js_callbackfunc, $opt_jfb_ask_stream;
     if( !get_option($opt_jfb_valid) ) return;
     if( !$redirectTo )  $redirectTo = htmlspecialchars($_SERVER['REQUEST_URI']);
     if( !$callbackName )$callbackName = $jfb_js_callbackfunc;
     $process_logon = plugins_url(dirname(plugin_basename(__FILE__))) . "/_process_login.php";
 ?>
    <form name="<?php echo $callbackName ?>_form" action="<?php echo $process_logon?>" method="post">
      <input type="hidden" name="redirectTo" value="<?php echo $redirectTo?>" />
      <?php wp_nonce_field ($jfb_nonce_name) ?>   
    </form>
    <script type="text/javascript">//<!--
    function <?php echo $callbackName ?>()
    {
        //Make sure we have a valid session
        if (!FB.Facebook.apiClient.get_session())
        { alert('Facebook failed to log you in!'); return; }

        <?php 
        //Optionally request permissions to get their real email and to publish to their wall before redirecting to the logon script.
        $ask_for_email_permission = get_option($opt_jfb_ask_perms) || get_option($opt_jfb_req_perms);
        if( $ask_for_email_permission && get_option($opt_jfb_ask_stream) )                     //Ask for both
            echo "FB.Connect.showPermissionDialog('email,publish_stream', function(reply){\n";
        else if( $ask_for_email_permission )                                                   //Ask for email only
            echo "FB.Connect.showPermissionDialog('email', function(reply){\n";
        else if( get_option($opt_jfb_ask_stream) )                                             //Ask for publish only
            echo "FB.Connect.showPermissionDialog('publish_stream', function(reply){\n";
        
        //If we REQUIRE their email address, make sure they approved it before redirecting to the logon script            
        if( get_option($opt_jfb_req_perms) )
        { 
            echo "            FB.Facebook.apiClient.users_hasAppPermission('email', function (emailCheck){\n". 
		         "                 if(emailCheck) document." . $callbackName . "_form.submit();\n" .
                 "                 else           alert('Sorry, this site requires an e-mail address to log you in.');\n" .
                 "            });\n";
        }
        
        //Otherwise, just redirect them (no matter if they approve or not)
        else
            echo "            document." . $callbackName . "_form.submit();\n";
            
        //Close the showPermissionDialog({...}); if it was opened.
        if( $ask_for_email_permission || get_option($opt_jfb_ask_stream) )
            echo "        });\n";
        ?>
    }
    //--></script><?php
}



/**
  * Include the FB class in the <html> tag (only when not already logged in)
  * So stupid IE will render the button correctly
  */
add_filter('language_attributes', 'jfb_output_fb_namespace');
function jfb_output_fb_namespace()
{
    global $current_user;
    if( isset($current_user) && $current_user->ID != 0 ) return;
    echo 'xmlns:fb="http://www.facebook.com/2008/fbml"';
}


/**********************************************************************/
/*******************************AVATARS********************************/
/**********************************************************************/


/**
  * If enabled, hook into get_avatar() and replace it with a WORDPRESS profile thumbnail.
  * NOTE: BuddyPress avatars are handled below.
  */
if( get_option($opt_jfb_wp_avatars) ) add_filter('get_avatar', 'jfb_wp_avatar', 10, 5);
function jfb_wp_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    //First, get the userid
	if (is_numeric($id_or_email))	    
	    $user_id = $id_or_email;
	else if(is_object($id_or_email) && !empty($id_or_email->user_id))
	   $user_id = $id_or_email->user_id;

	//If we couldn't get the userID, just return default behavior (email-based gravatar, etc)
	if(!isset($user_id) || !$user_id) return $avatar;

	//Now that we have a userID, let's see if we have their facebook profile pic stored in usermeta
	$fb_img = get_usermeta($user_id, 'facebook_avatar_thumb');
	
	//If so, replace the avatar! Otherwise, fallback on what WP core already gave us.
	if($fb_img) $avatar = "<img alt='fb_avatar' src='$fb_img' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
    return $avatar;
}



/**********************************************************************/
/*******************BUDDYPRESS (previously in BuddyPress.php)**********/
/**********************************************************************/

/*
 * If this is BuddyPress, switch ON the option to include bp filters by default
 */
global $opt_jfb_buddypress;
add_action( 'bp_init', 'jfb_turn_on_bp' );
function jfb_turn_on_bp()
{
    add_option($opt_jfb_buddypress, 1);
    add_option($opt_jfb_bp_avatars, 1);
}


if( get_option($opt_jfb_buddypress) )
{
    /*
     * If enabled, hook into bp_core_fetch_avatar and replace the BuddyPress avatar with the Facebook profile picture.
     * For WORDPRESS avatar handling, see above. 
     */
    if( get_option($opt_jfb_bp_avatars) ) add_filter( 'bp_core_fetch_avatar', 'jfb_get_facebook_avatar', 10, 4 );    
    function jfb_get_facebook_avatar($avatar, $params='')
    {
        //First, get the userid
    	global $comment;
    	if (is_object($comment))	$user_id = $comment->user_id;
    	if (is_object($params)) 	$user_id = $params->user_id;
    	if (is_array($params))
    	{
    		if ($params['object']=='user')
    			$user_id = $params['item_id'];
    	}
    
    	//Then see if we have a Facebook avatar for that user
    	if( $params['type'] == 'full' && get_usermeta($user_id, 'facebook_avatar_full'))
    		return '<img alt="avatar" src="' . get_usermeta($user_id, 'facebook_avatar_full') . '" class="avatar" />';
        else if( get_usermeta($user_id, 'facebook_avatar_thumb') )
    	    return '<img alt="avatar" src="' . get_usermeta($user_id, 'facebook_avatar_thumb') . '" class="avatar" />';
    	else
            return $avatar;
    }
    
    
    /*
     * Add a Facebook Login button to the Buddypress sidebar login widget
     * NOTE: If you use this, you mustn't also use the built-in widget - just one or the other!
     */
    add_action( 'bp_after_sidebar_login_form', 'jfb_bp_add_fb_login_button' );
    function jfb_bp_add_fb_login_button()
    {
      if ( !is_user_logged_in() )
      {
          echo "<p></p>";
          jfb_output_facebook_btn();
          jfb_output_facebook_init();
          jfb_output_facebook_callback();
      }
    }
    
    
    /*
     * Modify the userdata for BuddyPress by changing login names from the default FB_xxxxxx
     * to something prettier for BP's social link system
     */
    add_filter( 'wpfb_insert_user', 'jfp_bp_modify_userdata', 10, 2 );
    function jfp_bp_modify_userdata( $wp_userdata, $fb_userdata )
    {
        $counter = 1;
        $name = str_replace( ' ', '', $fb_userdata['first_name'] . $fb_userdata['last_name'] );
        if ( username_exists( $name ) )
        {
            do
            {
                $username = $name;
                $counter++;
                $username = $username . $counter;
            } while ( username_exists( $username ) );
        }
        else
        {
            $username = $name;
        }
        $username = strtolower( sanitize_user($username) );
    
        $wp_userdata['user_login']   = $username;
        $wp_userdata['user_nicename']= $username;
        return $wp_userdata;
    }
}


/**********************************************************************/
/***************************Error Reporting****************************/
/**********************************************************************/

register_activation_hook(__FILE__, 'jfb_activate');
register_deactivation_hook(__FILE__, 'jfb_deactivate');

?>