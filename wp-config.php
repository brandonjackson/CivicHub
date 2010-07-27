<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'civic_wp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '&Aa@p|Gk5nx;rV|A(-*h%-9|GV?-+xTFQFgbi`2r2+LJVxQJKi]r}Br;<R#<#2Cf');
define('SECURE_AUTH_KEY',  '{+}nw/>7b:-ln8zu>^qKHIC|.;>b2k,Y,x,43}p%>a0DS,}]F3XCY:-g|L<QX63c');
define('LOGGED_IN_KEY',    '*rE$zZM-$3BZdVtZO|+= +2>ZK0plF+Y{5L=%c<8$H+]2xU<`,QQ_+5KJ:T]Mo<O');
define('NONCE_KEY',        'AC$ydfL{K-~!DKAV:P4C.nn5a!5J3Cfw}D`qus.T.^I-yfU%oEJ<$5qjx_Hf,C~R');
define('AUTH_SALT',        'dVZ-+e@lBB:P|EU}+wy*rbs0gx*u:F|+UB7aVWO{-?e|=gwc,e$?Rt1Sp+E~0r _');
define('SECURE_AUTH_SALT', ')lX2?Yc:UuE*I};?yMX_Eh<LgUd@A*+g.|rZ2/O^_RbrXy:79b#mX}z=D}4I:*}g');
define('LOGGED_IN_SALT',   '7(39(%T*bQ-jkP/lQ[{xOC^c`jj|.:H k*zqyzpX/P%B.^N5LD[Fj25A0<Psf_$j');
define('NONCE_SALT',       'A5d0j70dFTNC=o@[Sh&UWJ+Akt8$Fc?AUTk5fqB8`U%|2|tL-yJ[61M9*[s]Fq[1');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

