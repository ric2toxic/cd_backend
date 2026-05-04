<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wholesalebox_blog');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '9@4I%KJq00EGV;>*N={Sb_^ 0D-U|EpF.n+Uo0QJO01p4l@t|E5DX17);cJ*Q?e5');
define('SECURE_AUTH_KEY',  'V7O 26mGF^@pGSgE>ylvW*fLwP!U|AcJQkWu(+i-s4*?|KB*_c*ZdIBd}IimJ`vr');
define('LOGGED_IN_KEY',    'E6#05f,CA:p%+:B-~tA=<P_<{5?(H>u}!e!jul/q#TU;1v`I>F0V^W+F8SgSs>uz');
define('NONCE_KEY',        '$aPYG#weJVf-_FjDLV!W=#*o^#{=Qw%^C,(D00C#upV{K=|QM7A%T{.2 b,7T X=');
define('AUTH_SALT',        'Y( >HUYVv3cn.^wy9g5`Nybs^,DdZ]`H[e::UDZ;+q(q%i*W_SE_8+FxCn/4zL!p');
define('SECURE_AUTH_SALT', 'rgf-F,1Vr -O2T{K>j6&WuB-6*VRcPLl38fWZE,2*ziJ9Y[Fb8%iPeb?XfOEC.r_');
define('LOGGED_IN_SALT',   '=c@bh5S1q83wo;Q2gL&|RM=47auzL|$8dV^MGmn?IM~6z5s.I8zqu5)E34~.{V;_');
define('NONCE_SALT',       'p, xU)Y}se~VWY+y[i?KKXv!O1z-cihmPTLv1s6(3-q^C2-BT~-;q)1VKftgnJ*u');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
