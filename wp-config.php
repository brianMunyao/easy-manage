<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'easy-manage');

/** Database username */
define('DB_USER', 'admin');

/** Database password */
define('DB_PASSWORD', 'admin');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '{!-+So~~]-d{%0kw<4SXq.-^c_[?ty=L7RMf|LPzHRHD`}+E^xM`:X1xp0KB}}X;');
define('SECURE_AUTH_KEY',  '|hOuxLu!:!*cUqsn$Pw:qBNW#%Ds o]t[s@orqh2>Azkmpg+_Q@Jf=HTaImh-Y<L');
define('LOGGED_IN_KEY',    '*Vo5Z+e]DD+$b/N =yuX/+[zi[?h||j&Kw}@x.@AHWFGo7 `73Tc; 4ug,d.-VET');
define('NONCE_KEY',        'x_%H2r3|LC3^f#^?r7*gmTKy*Y&34>(<!<-Y;io]dkXjG%g>:*x_RRo@l-]Y8bmH');
define('AUTH_SALT',        ')U-108_uU>/hg]8M_+2r?JEEb-zLMK=K/J7%T/`8~K|h<}v-B3EU No=U28pH:P<');
define('SECURE_AUTH_SALT', ' GK%Bd)T/v+f]G<f]xX?&Q[AW~r6804/G,dOLK! edeCSWGmP]:d(?jto2S:+JyN');
define('LOGGED_IN_SALT',   'b+oVn<[s2&v>ax)L5^>57is)[,^Oq5).GJ-`}Jk.$ck=%Gmr}292MYwR1Gu:26sj');
define('NONCE_SALT',       'Y!@},i0vl>a~_ugnTuJE#g]fRj}[xR=c#E$x,vWNBNUmC#N?k4;w|x3Nlpx;5o61');

/**#@-*/

define('JWT_AUTH_SECRET_KEY', 'SIRI_YANGU');

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
