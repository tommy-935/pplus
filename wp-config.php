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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpp' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'GJUrWnGNO93u]~(sKxmx@>JBRbI+Ji#bE3+#UZG)F3lm0KE@f/9Yz#n_^[C_-t}m' );
define( 'SECURE_AUTH_KEY',  'L:REXnEaKDFJZ%+ux7tYz@j|X<,L.oa)tyxRFesWH70)PtOUMnVG(43$aHNY?HZR' );
define( 'LOGGED_IN_KEY',    ';W3LVQNB|EMkKv30D &Cvh-+.-qF<E?iQIhi{~O?:TN,;NJ^K.5l:yh&/(S71Rz.' );
define( 'NONCE_KEY',        '23QC#Zp]8m ]4d0@<fygQzUlhRox;j?$_(,sh]S&?IHk3zr8YJ;;*lYDvZ]MEY)i' );
define( 'AUTH_SALT',        'cUi(/R9=-et1T@K|vy/J&7,q~{L3>Fx/u6.f|n>3m I)4=6=aul9&5aw:_OefijI' );
define( 'SECURE_AUTH_SALT', 'Ttq2x(8)K ),Q>o:d`9C0`* ~em]E!Fm#22o4c1a62nL/zMEG)^W#jp%l[i})fhF' );
define( 'LOGGED_IN_SALT',   ')a!`#xPkBA@;(NDRuJzIvjSY>0FndP!]]a}Z3-l. #J3te5$~2m3^A,HLgFb,A>`' );
define( 'NONCE_SALT',       'A9?_L8IhjGtj($KcKBVXTQchx9gOlboCap-VpZf1CA)DW#j47}YS1Q$ jqZchdD6' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
