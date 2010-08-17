<?php
/** This file offers an example of a more advanced way to automate Facebook Logins.
 *  Please make sure you understand the "How It Works" section on the plugin homepage first.
 *  
 *  The purpose of this script is to allow us to direct-link to any post/page with a url like 
 *  www.example.com/autologin/4781, where:
 * ->If the user is requesting a post they can already access, they'll be taken straight there (as if they visited it directly).
 * ->If they CAN'T access it (i.e. it's private and they're not logged in), it'll automatically popup a 
 *   Facebook login window, process the login, then redirect them to the post.
 *
 * Note that we make use of 'generate_rewrite_rules' to add a redirect to .htaccess (via the plugin's admin panel).
 * The rewrite is: www.example.com/autologin/4781 --> www.example.com/.../_autologin.php?p=4781
 */

//Include our options and the Wordpress core
require_once("__inc_opts.php");
require_once("__inc_wp.php");


//Show a 404 if no post ID was specified
if( !isset($_REQUEST['p']) )
{
    j_mail("Facebook Autologin Error", "Missing Post ID");
    if( $template = get_404_template() )
    { include(get_404_template()); exit; }
    else
    { die("REDIRECTOR Missing ID"); }
}


//Show a 404 if a post ID was specified, but it doesn't exist
$post = get_post($_REQUEST['p']);
if( !$post )
{
    j_mail("Facebook Autologin Error", "Invalid Post ID (" . $_REQUEST['p'] . ")");
    if( $template = get_404_template() )
    { include(get_404_template()); exit; }
    else
    { die("Error: Invalid ID"); }
}


//If the post exists and the user can access it, redirect them immediately.
//NOTE: This assumes that WP_Query is filtered not to return posts which are inaccessible
//to the user, as is the case with the Role-Scoper plugin.
if( query_posts('page_id='.$post->ID) || query_posts('p='.$post->ID) )
{
    $user = wp_get_current_user();
    $log = "Redirecting immediately (Post already accessible)\n";
    $log .= "Post: " . $post->ID . ", User: " . ($user->ID?$user->user_login:"Anonymous");
    j_mail("Facebook Autologin", $log);
    header("Location: " . get_permalink($post->ID));
    exit;
}


//If we got here, the post exists but the user can't access it. Take them to special page 
//and popup a Facebook Connect dialog.  When they click OK it'll redirect them through 
//process_login.php just as if they clicked "Login with Facebook" manually.
//NOTE: The following is a sample template, but you'll almost certainly have to customize it to work
//with your theme and show what you want.  The only important thing is to call jfb_output_facebook_init(),
//jfb_output_facebook_callback(get_permalink($post->ID)), and jfb_output_facebook_instapopup().
//Those three functions will be sufficient to show the Facebook popup and setup the login redirect.
j_mail("Facebook Autologin", "Post " . $_REQUEST['p'] . " requires login: Showing Facebook prompt (Referrer: " . $_SERVER['HTTP_REFERER'] .")");
    
/******************Template for "AutoLogin" Page******************/
    get_header();
    get_sidebar();    
    echo '<div id="main-col"><div id="content" class="post">';
    
    //Output Facebook Javascript to immediately show a login prompt to the user
    //Note: In my case, the FB api has already been initialized via my sidebar widget, so I don't need to
    //call jfb_output_facebook_init() again - I just create another login callback, this time which
    //redirects to the destination post instead of back ot the current page.
    //jfb_output_facebook_init();
    jfb_output_facebook_callback(get_permalink($post->ID), "autologin_callback");
    jfb_output_facebook_instapopup("autologin_callback");
    
    //Show the user a message.  Since the post is private and the user isn't yet logged in, you can
    //use your discression as to what information you reveal about the destination post.
    if( function_exists('gallery_pg_id') && $post->post_parent == gallery_pg_id() )
        echo "<h2>$post->post_title</h2><br />This photo album is private and requires login.  A Facebook Connect window should popup in a moment.<br /><br />";
    else
        echo "<h2>Login Required</h2><br />This post is private and requires login.  A Facebook Connect window should popup in a moment.<br /><br />";
    
    //If the destination post has a thumbnail, show it (sizing it down if it's too wide)
    if( has_post_thumbnail($post->ID) )
    {
        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
        $maxWd = 590;
        if( $thumb[1] > $maxWd )
        {
            $thumb[2] = $maxWd*($thumb[2]/$thumb[1]);
            $thumb[1] = $maxWd;
        }
        echo '<div style="text-align:center;"><img alt="preview" src="'.$thumb[0].'" height="'.$thumb[2].'" width="'.$thumb[1].'" /></div>';
    }
    
    echo "</div></div>";
    get_footer();
/******************Template for "AutoLogin" Page******************/
?>