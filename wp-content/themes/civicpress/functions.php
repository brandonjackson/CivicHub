<?php
/**
*	CivicPress functions file
*/
define ( 'BP_DISABLE_ADMIN_BAR', true );

/* Add custom menu support*/
if (function_exists('add_theme_support')) {
    add_theme_support('menus');
}

/* Disable Custom Header Image */
define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );

/* Custom post type: Ideas */

register_post_type('idea', array(
	'label' => __('Ideas'),
	'singular_label' => __('Idea'),
	'public' => true, // Allows it to be publicly queryable
	'show_ui' => true, // Displays the post time in the Admin Interface
	'_builtin' => false,
	'_edit_link' => 'post.php?post=%d',
	'capability_type' => 'post',
	'taxonomies'=>array('topic'),
	'hierarchical' => false,
	'rewrite' => array("slug" => "ideas", 'with_front'=>FALSE), // the slug for permalinks
	'supports' => array('title','editor','author','custom-fields', 'comments') // What can this post type do
));

register_taxonomy(
		'topic', // internal name = machine-readable taxonomy name
		'idea', // object type = post, page, link, or custom post-type
		array(
			'hierarchical' => true,
			'label' => 'Topic',	// the human-readable taxonomy name
			'query_var' => true,	// enable taxonomy-specific querying
			'rewrite' => array( 'slug' => 'ideas/topics' ),	// pretty permalinks for your taxonomy?
		)
);

register_sidebars( 1,
	array(
		'name' => 'IdeaSidebar',
		'description' => 'Widgets will be displayed in the single idea view.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);
add_action('muplugins_loaded','buddypress_config');
function buddypress_config()
{
	
}


?>