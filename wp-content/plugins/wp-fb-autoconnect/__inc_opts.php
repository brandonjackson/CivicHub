<?php

//General Info
global $jfb_name, $jfb_version, $jfb_homepage;
$jfb_name       = "WP-FB AutoConnect";
$jfb_version    = "1.3.2";
$jfb_homepage   = "http://www.justin-klein.com/projects/wp-fb-autoconnect";


//Database options
global $opt_jfb_app_id, $opt_jfb_api_key, $opt_jfb_api_sec, $opt_jfb_email_to, $opt_jfb_delay_redir, $opt_jfb_ask_perms, $opt_jfb_ask_stream, $opt_jfb_stream_content;
global $opt_jfb_req_perms, $opt_jfb_hide_button, $opt_jfb_mod_done, $opt_jfb_valid;
global $opt_jfb_buddypress, $opt_jfb_bp_avatars, $opt_jfb_wp_avatars, $opt_jfb_fulllogerr, $opt_jfb_disablenonce;
$opt_jfb_app_id     = "jfb_app_id";
$opt_jfb_api_key    = "jfb_api_key";
$opt_jfb_api_sec    = "jfb_api_sec";
$opt_jfb_email_to   = "jfb_email_to";
$opt_jfb_delay_redir= "jfb_delay_redirect";
$opt_jfb_ask_perms  = "jfb_ask_permissions";
$opt_jfb_req_perms  = "jfb_req_permissions";
$opt_jfb_ask_stream = "jfb_ask_stream";
$opt_jfb_stream_content = "jfb_stream_content";
$opt_jfb_hide_button= "jfb_hide_button";
$opt_jfb_mod_done   = "jfb_modrewrite_done";
$opt_jfb_valid      = "jfb_session_valid";
$opt_jfb_buddypress = "jfb_include_buddypress";
$opt_jfb_fulllogerr = "jfb_full_log_on_error";
$opt_jfb_disablenonce="jfb_disablenonce";
$opt_jfb_bp_avatars = "jfb_bp_avatars";
$opt_jfb_wp_avatars = "jfb_wp_avatars";


//Shouldn't ever need to change these
global $jfb_nonce_name, $jfb_uid_meta_name, $jfb_js_callbackfunc, $jfb_default_email;
$jfb_nonce_name     = "ahe4t50q4efy0";
$jfb_uid_meta_name  = "facebook_uid";
$jfb_js_callbackfunc= "jfb_js_login_callback";
$jfb_default_email  = '@unknown.com';


//Error reporting function
function j_die($msg)
{
    j_mail("Facebook Login Error", $msg);
    global $jfb_log, $opt_jfb_fulllogerr;
    if( isset($jfb_log) && get_option($opt_jfb_fulllogerr) )
        $msg .= "<pre>---LOG:---\n" . $jfb_log . "</pre>";
    die($msg);
}

//Log reporting function
function j_mail($subj, $msg='')
{
    global $opt_jfb_email_to, $jfb_log;
    $email_to = get_option($opt_jfb_email_to);
    if( isset($email_to) && $email_to )
    {
        if( $msg )            $msg .= "\n\n";
        if( isset($jfb_log) ) $msg .= "---LOG:---\n" . $jfb_log;
        $msg .= "\n---REQUEST:---\n" . print_r($_REQUEST, true);
        mail($email_to, $subj, $msg);
    }
}

?>