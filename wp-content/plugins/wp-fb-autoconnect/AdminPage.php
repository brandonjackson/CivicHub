<?php

/*
 * Tell WP about the Admin page
 */
add_action('admin_menu', 'jfb_add_admin_page', 99);
function jfb_add_admin_page()
{ 
    add_options_page('WP-FB AutoConnect Options', 'WP-FB AutoConn', 'administrator', "wp-fb-autoconnect", 'jfb_admin_page');
}


/**
  * Link to Settings on Plugins page 
  */
add_filter('plugin_action_links', 'jfb_add_plugin_links', 10, 2);
function jfb_add_plugin_links($links, $file)
{
    if( dirname(plugin_basename( __FILE__ )) == dirname($file) )
        $links[] = '<a href="options-general.php?page=' . "wp-fb-autoconnect" .'">' . __('Settings','sitemap') . '</a>';
    return $links;
}


/*
 * Output the Admin page
 */
function jfb_admin_page()
{
    global $jfb_name, $jfb_version;
    global $opt_jfb_app_id, $opt_jfb_api_key, $opt_jfb_api_sec, $opt_jfb_email_to, $opt_jfb_delay_redir, $jfb_homepage;
    global $opt_jfb_ask_perms, $opt_jfb_req_perms, $opt_jfb_hide_button, $opt_jfb_mod_done, $opt_jfb_ask_stream, $opt_jfb_stream_content;
    global $opt_jfb_buddypress, $opt_jfb_bp_avatars, $opt_jfb_wp_avatars, $opt_jfb_valid, $opt_jfb_fulllogerr, $opt_jfb_disablenonce;
    ?>
    <div class="wrap">
     <h2>WP-FB AutoConnect Options</h2>
    <?php
    
      //Show a warning if they're using a naughty other plugin
      if( class_exists('Facebook') )
      {
          jfb_auth($jfb_name, $jfb_version, 3, "API WARNING!! Facebook already detected." );
          ?><div class="error"><p><strong>Warning:</strong> Another plugin has included the Facebook API throughout all of Wordpress.  I suggest you contact that plugin's author and ask them to include it only in pages where it's actually needed.<br /><br />Things may work fine as-is, but *if* the API version included by the other plugin is older than the one required by WP-FB AutoConnect, it's possible that the login process could fail.</p></div><?php
      }
      
      if(version_compare('5', PHP_VERSION, ">"))
      {
          ?><div class="error"><p>Sorry, but as of v1.3.0, WP-FB AutoConnect requires PHP5.</p></div><?php
          die();
      }
      
      //Update options
      if( isset($_POST['main_opts_updated']) )
      {
          //When saving the main options, make sure the key and secret are valid.
          if( !class_exists('Facebook') ) require_once('facebook-platform/validate_php5.php');
          $fbValid = jfb_validate_key($_POST[$opt_jfb_api_key], $_POST[$opt_jfb_api_sec]);
          if( $fbValid && method_exists($fbValid->api_client, 'admin_getAppProperties') ):
                $appInfo = $fbValid->api_client->admin_getAppProperties(array('app_id', 'application_name'));
                if( is_array($appInfo) )
                {
                    $appID = sprintf("%.0f", $appInfo['app_id']);
                    $message = '"' . $appInfo['application_name'] . '" (ID ' . $appID . ')'; 
                }
                else if( $appInfo->app_id )
                {   //Why does this happen? Presumably because another plugin includes a different version of the API that uses objects instead of arrays
                    $appID = sprintf("%.0f", $appInfo->app_id);
                    $message = '"' . $appInfo->application_name . '" (ID ' . $appID . ')';
                    jfb_auth($jfb_name, $jfb_version, 3, "BUG Object instead of array (appInfo = " . print_r($appInfo, true) . ")" );
                }
                else
                {
                    $message = "Key " . $_POST[$opt_jfb_api_key];
                    jfb_auth($jfb_name, $jfb_version, 3, "BUG Unknown instead of array (getAppProperties returns: " . print_r($appInfo, true) . ")" );
                    $appID = 0;
                    ?><div class="error"><p><strong>Warning:</strong> Facebook failed to retrieve your Application's properties!  The plugin is very unlikely to work until it's fixed.<br /><br />I've thus far not been able to determine the exact cause of this extremely rare problem, but my best guess is that you've made a mistake somewhere in your configuration.  If you see this warning and figure out how to fix it, please let me know <b><a href="<?php echo $jfb_homepage ?>">here</a></b> so I can clarify my setup instructions.</p></div><?php
                }
                update_option( $opt_jfb_valid, 1 );
                if( get_option($opt_jfb_api_key) != $_POST[$opt_jfb_api_key] )
                   jfb_auth($jfb_name, $jfb_version, 2, "SET: " . $message );
                ?><div class="updated"><p><strong>Main Options saved for <?php echo $message ?></strong></p></div><?php
          else:
              update_option( $opt_jfb_valid, 0 );
              $message = "ERROR: Facebook could not validate your session key and secret!  Are you sure you've entered them correctly?";
              jfb_auth($jfb_name, $jfb_version, 3, $message );
              ?><div class="updated"><p><?php echo $message ?></p></div><?php
          endif;
          
          //We'll update the options either way - but if jfb_valid isn't set, the plugin won't show a Facebook button.
          update_option( $opt_jfb_app_id, $appID);
          update_option( $opt_jfb_api_key, $_POST[$opt_jfb_api_key] );
          update_option( $opt_jfb_api_sec, $_POST[$opt_jfb_api_sec] );
          update_option( $opt_jfb_ask_perms, $_POST[$opt_jfb_ask_perms] );
          update_option( $opt_jfb_req_perms, $_POST[$opt_jfb_req_perms] );
          update_option( $opt_jfb_ask_stream, $_POST[$opt_jfb_ask_stream] );
          update_option( $opt_jfb_wp_avatars, $_POST[$opt_jfb_wp_avatars] );
          update_option( $opt_jfb_stream_content, $_POST[$opt_jfb_stream_content] );
          if( $_POST[$opt_jfb_email_to] )   update_option( $opt_jfb_email_to, get_bloginfo('admin_email') );
          else                              update_option( $opt_jfb_email_to, 0 );
      }
      if( isset($_POST['debug_opts_updated']) )
      {
          update_option( $opt_jfb_delay_redir, $_POST[$opt_jfb_delay_redir] );
          update_option( $opt_jfb_hide_button, $_POST[$opt_jfb_hide_button] );
          update_option( $opt_jfb_fulllogerr, $_POST[$opt_jfb_fulllogerr] );
          update_option( $opt_jfb_disablenonce, $_POST[$opt_jfb_disablenonce] );          
          ?><div class="updated"><p><strong><?php _e('Debug Options saved.', 'mt_trans_domain' ); ?></strong></p></div><?php
      }
      if( isset($_POST['bp_opts_updated']) )
      {
          update_option( $opt_jfb_buddypress, $_POST[$opt_jfb_buddypress] );
          update_option( $opt_jfb_bp_avatars, $_POST[$opt_jfb_bp_avatars] );
          ?><div class="updated"><p><strong><?php _e('Buddypress Options saved.', 'mt_trans_domain' ); ?></strong></p></div><?php
      }
      if( isset($_POST['mod_rewrite_update']) )
      {
          add_action('generate_rewrite_rules', 'jfb_add_rewrites');
          add_filter('mod_rewrite_rules', 'jfb_fix_rewrites');
          global $wp_rewrite;
          $wp_rewrite->flush_rules();
          update_option( $opt_jfb_mod_done, true );
          ?><div class="updated"><p><strong><?php _e('HTACCESS Updated.', 'mt_trans_domain' ); ?></strong></p></div><?php          
      }
      if( isset($_POST['remove_all_settings']) )
      {
          delete_option($opt_jfb_api_key);
          delete_option($opt_jfb_api_sec);
          delete_option($opt_jfb_email_to);
          delete_option($opt_jfb_delay_redir);
          delete_option($opt_jfb_ask_perms);
          delete_option($opt_jfb_req_perms);
          delete_option($opt_jfb_ask_stream);
          delete_option($opt_jfb_stream_content);
          delete_option($opt_jfb_hide_button);
          delete_option($opt_jfb_mod_done);
          delete_option($opt_jfb_valid);
          delete_option($opt_jfb_buddypress);
          delete_option($opt_jfb_bp_avatars);
          delete_option($opt_jfb_wp_avatars);
          delete_option($opt_jfb_fulllogerr);
          delete_option($opt_jfb_disablenonce);
          ?><div class="updated"><p><strong><?php _e('All plugin settings have been cleared.' ); ?></strong></p></div><?php
      }
    ?>
      
    To allow your users to login with their Facebook accounts, you must first setup a Facebook Application for your website:<br /><br />
    <ol>
      <li>Visit <a href="http://www.facebook.com/developers/createapp.php" target="_lnk">www.facebook.com/developers/createapp.php</a></li>
      <li>Type in a name (i.e. the name of your website).  This is the name your users will see on the Facebook login popup.</li>
      <li>Copy the API Key and Secret to the boxes below.</li>
      <li>Click the "Connect" tab (back on Facebook) and under "Connect URL" enter the URL to your website (with a trailing slash).  Note: http://example.com/ and http://www.example.com/ are <i>not</i> the same.</li>
      <li>Click the "Advanced" tab and enter your site's domain under "Email Domain" (i.e. example.com).  This is only required if you want to access your users' email addresses (optional).</li>
      <li>Click "Save Changes" (on Facebook).</li>
      <li>Click "Save" below.</li>
    </ol>
    <br />That's it!  Now you can add this plugin's <a href="<?php echo admin_url('widgets.php')?>">sidebar widget</a>, or if you're using BuddyPress, a Facebook button will be automatically added to its built-in login panel.<br /><br />
    For more complete documentation and help, visit the <a href="<?php echo $jfb_homepage?>">plugin homepage</a>.<br />
     
    <br />
    <hr />
    
    <h3>Development</h3>
    Many hours have gone into making this plugin as versatile and easy to use as possible, far beyond my own personal needs. Although I offer it to you freely, please keep in mind that each hour spent extending and supporting it was an hour that could've also gone towards income-generating work. If you find it useful, a small donation would be greatly appreciated :)
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_s-xclick" />
        <input type="hidden" name="hosted_button_id" value="T88Y2AZ53836U" />
        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
    </form>
    <hr />
    
    <h3>Main Options</h3>
    <form name="formMainOptions" method="post" action="">
        <b>Facebook:</b><br />
        <input type="text" size="40" name="<?php echo $opt_jfb_api_key?>" value="<?php echo get_option($opt_jfb_api_key) ?>" /> API Key<br />
        <input type="text" size="40" name="<?php echo $opt_jfb_api_sec?>" value="<?php echo get_option($opt_jfb_api_sec) ?>" /> API Secret<br /><br />
        <br /><b>E-Mail:</b><br />
        <input type="checkbox" name="<?php echo $opt_jfb_ask_perms?>" value="1" <?php echo get_option($opt_jfb_ask_perms)?'checked="checked"':''?> /> ASK the user for permission to get their email address<br />
        <input type="checkbox" name="<?php echo $opt_jfb_req_perms?>" value="1" <?php echo get_option($opt_jfb_req_perms)?'checked="checked"':''?> /> REQUIRE user for permission to get their email address<br />
        <br /><b>Announcement:</b><br />
		<?php add_option($opt_jfb_stream_content, "has connected to " . get_option('blogname') . " with WP-FB AutoConnect."); ?>
		<input type="checkbox" name="<?php echo $opt_jfb_ask_stream?>" value="1" <?php echo get_option($opt_jfb_ask_stream)?'checked="checked"':''?> /> Request permission to post the following announcement on users' Facebook walls when they connect for the first time:</i><br />
		<input type="text" size="100" name="<?php echo $opt_jfb_stream_content?>" value="<?php echo get_option($opt_jfb_stream_content) ?>" /><br />
		<br /><b>Wordpress Avatars:</b><br />
        <input type="checkbox" name="<?php echo $opt_jfb_wp_avatars?>" value="1" <?php echo get_option($opt_jfb_wp_avatars)?'checked="checked"':''?> /> Use Facebook profile pictures as Wordpress avatars<br />
        <br /><b>Logging:</b><br />
        <input type="checkbox" name="<?php echo $opt_jfb_email_to?>" value="1" <?php echo get_option($opt_jfb_email_to)?'checked="checked"':''?> /> Send all event logs to <i><?php echo get_bloginfo('admin_email')?></i><br />
        <input type="hidden" name="main_opts_updated" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Save" /></div>
    </form>
    <hr />
    
    <h4>Buddypress Options</h4>
    <form name="formBPOptions" method="post" action="">
        <input type="checkbox" name="<?php echo $opt_jfb_buddypress?>" value="1" <?php echo get_option($opt_jfb_buddypress)?'checked="checked"':''?> /> Include BuddyPress Filters<br />
        &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php echo $opt_jfb_bp_avatars?>" value="1" <?php echo get_option($opt_jfb_bp_avatars)?'checked="checked"':''?> /> Replace BuddyPress avatars with Facebook profile pictures<br />
        <input type="hidden" name="bp_opts_updated" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Save" /></div>
	</form>            
    <hr />
    
    <h4>Mod Rewrite Rules</h4>
    <?php
    if (get_option($opt_jfb_mod_done))
        echo "It looks like your htaccess has already been updated.  If you're having trouble with autologin links, make sure the file is writable and click the Update button again.";
    else
        echo "In order to use this plugin's autologin shortcut links (i.e. www.example.com/autologin/5), your .htaccess file needs to be updated.  Click the button below to update it now.<br /><br />Note that this is an advanced feature and won't be needed by most users; see the plugin's homepage for documentation."
    ?>
    <form name="formMainOptions" method="post" action="">
        <input type="hidden" name="mod_rewrite_update" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Update Now" /></div>
    </form>
    <hr />
    
    <h4>Debug Options</h4>
    <form name="formDebugOptions" method="post" action="">
        <input type="checkbox" name="<?php echo $opt_jfb_delay_redir?>" value="1" <?php echo get_option($opt_jfb_delay_redir)?'checked="checked"':''?> /> Delay redirect after login (Not for production sites!)<br />
        <input type="checkbox" name="<?php echo $opt_jfb_hide_button?>" value="1" <?php echo get_option($opt_jfb_hide_button)?'checked="checked"':''?> /> Hide Facebook Button<br />
        <input type="checkbox" name="<?php echo $opt_jfb_fulllogerr?>" value="1" <?php echo get_option($opt_jfb_fulllogerr)?'checked="checked"':''?> /> Show full log on error<br />
        <input type="checkbox" name="<?php echo $opt_jfb_disablenonce?>" value="1" <?php echo get_option($opt_jfb_disablenonce)?'checked="checked"':''?> /> DISABLE nonce security check<br />        
        <input type="hidden" name="debug_opts_updated" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Save" /></div>
    </form>
    <hr />
    
    <h4>Delete All Plugin Options</h4>
    <form name="formDebugOptions" method="post" action="">
        <input type="hidden" name="remove_all_settings" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Delete" /></div>
    </form>
      
   </div><?php
}


/*
 * Append our RewriteRule to htaccess so we can use links like www.example.com/autologin/123
 * This gets invoked by the generate_rewrite_rules filter when we call $wp_rewrite->flush_rules(),
 * which is triggered by the Update Now button
 */
function jfb_add_rewrites($wp_rewrite)
{
    $autologin = explode(get_bloginfo('url'), plugins_url(dirname(plugin_basename(__FILE__))));
    $autologin = trim($autologin[1] . "/_autologin.php", "/") . '?p=$1';
    $wp_rewrite->non_wp_rules = $wp_rewrite->non_wp_rules + array('autologin[/]?([0-9]*)$' => $autologin);
}

/*
 * Wordpress is HARDCODED to specify every rewriterule as [QSA,L]; the only way to get a redirect is to string-replace it.
 */
function jfb_fix_rewrites($rules)
{
    $autologin = explode(get_bloginfo('url'), plugins_url(dirname(plugin_basename(__FILE__))));
    $autologin = trim($autologin[1] . "/_autologin.php", "/") . '?p=$1';
    $rules = str_replace($autologin . ' [QSA,L]', $autologin . ' [R,L]', $rules);
    return $rules;
}



/*
 * I use this for bug-finding; you can remove it if you want, but I'd appreciate it if you didn't.
 * I'll always notify you directly if I find & fix a bug thanks to your site (along with providing the fix) :)
 */
function jfb_activate()  
{
    global $jfb_name, $jfb_version, $opt_jfb_valid, $opt_jfb_api_key;
    $msg = get_option($opt_jfb_valid)?"VALID":(!get_option($opt_jfb_api_key)||get_option($opt_jfb_api_key)==''?"NOKEY":"INVALIDKEY");
    jfb_auth($jfb_name, $jfb_version, 1, "ON: " . $msg);
}
function jfb_deactivate()
{
    global $jfb_name, $jfb_version, $opt_jfb_valid, $opt_jfb_api_key;
    $msg = get_option($opt_jfb_valid)?"VALID":(!get_option($opt_jfb_api_key)||get_option($opt_jfb_api_key)==''?"NOKEY":"INVALIDKEY"); 
    jfb_auth($jfb_name, $jfb_version, 0, "OFF: " . $msg);
}
function jfb_auth($name, $version, $event, $message=0)
{
    $AuthVer = 1;
    $data = serialize(array(
                  'plugin'      => $name,
                  'version'     => $version,
                  'wp_version'  => $GLOBALS['wp_version'],
                  'php_version' => PHP_VERSION,
                  'event'       => $event,
                  'message'     => $message,                  
                  'SERVER'      => $_SERVER));
    $args = array( 'blocking'=>false, 'body'=>array(
                            'auth_plugin' => 1,
                            'AuthVer'     => $AuthVer,
                            'hash'        => md5($AuthVer.$data),
                            'data'        => $data));
    wp_remote_post("http://auth.justin-klein.com", $args);
}

?>