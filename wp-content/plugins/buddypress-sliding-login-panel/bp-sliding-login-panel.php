<?php

require( dirname(__FILE__) . '/update-content.php' );
load_plugin_textdomain('iRLogin','wp-content/plugins/buddypress-sliding-login-panel/');

function string_limit_words($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit) {
  array_pop($words);
  echo implode(' ', $words)."..."; } else {
  echo implode(' ', $words); }
}

function scriptInstall()
{?>
<link rel="stylesheet" href="<?php echo (bloginfo("wpurl").'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/style.css" type="text/css" media="screen" />'."\n"); ?>
<?php }

add_action('wp_head','scriptInstall');
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-form');
wp_enqueue_script('slide', "/".PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/js/slide.js', array('jquery', 'jquery-form'));

function bp_slide_login_panel() {
updateHeader();
}
add_action( 'bp_before_header', 'bp_slide_login_panel' );

?>