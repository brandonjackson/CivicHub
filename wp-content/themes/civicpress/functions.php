<?php
/**
*	CivicPress functions file
*/

/* Add custom theme support*/
if (function_exists('add_theme_support')) {
    add_theme_support('menus');
}

/* Disable Custom Header Image */
define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );
?>