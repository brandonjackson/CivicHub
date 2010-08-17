<?php
include_once("../../../../wp-blog-header.php");
$post = array(
  'post_author' => $_POST['user_id'],
  'post_content' => $_POST['description'],
  'post_date' => date("Y-m-d H:i:s"),
  'post_date_gmt' => gmdate("Y-m-d H:i:s"),
  'post_status' => 'pending',
  'post_title' => $_POST['summary'],
  'post_type' => 'idea'
);
// future things to process:
// tags_input
// post_excerpt
// post_parent
// post_name

// Run query, get post id
$post_id = wp_insert_post($post);
wp_set_object_terms( $post_id, $_POST['topic'], "topic");
wp_redirect(get_option('siteurl') . '/idea-submitted');

// Process metadata

//update_post_meta($post_id, $meta_key, $meta_value);


?>