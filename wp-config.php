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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'XYE5MUoFxNyKXj0znvjdt5FQi3mSHqZN5WAMr5kfdvAfEWDxhoFseIF0aks3y6BxE9VR30R7+qlZhjwM8hnRbg==');
define('SECURE_AUTH_KEY',  'e3envqk7p8TvG3+E3lyxG9WAe8VVym2hcR4hV9ipZpKPQS5VQ0oKrQEchNPkYqtchmJVtiqhjNwzvByFtLHmbw==');
define('LOGGED_IN_KEY',    'ZFUoJZhlVBCmlTo4Q0xBRTGXnypaaMN1BlKfqnKyKzchqI9NZEQq44Iz0ZM49yLiTBnfjuCAAhe/XyoI/BITyg==');
define('NONCE_KEY',        '3lJi2FLE1acDKPjJeW7Z5FPeFGnAnzsbe3+KFQ4PCa2Bp025BY8pVS3hRtzfl4OUiimdQVEk3V8Htq1QiVNzNw==');
define('AUTH_SALT',        'bioTx3JpdosMmuolRqjF6VsNsdMpvqBvwZhEy/86m7T0lwUPdvlk3KOl1r4V3u8iDPVKjwtREHAGtj2r6y7Xgg==');
define('SECURE_AUTH_SALT', 'fepF9QCOgZCkALwc7cXCD9hf6MXeQVLbmiM+NvNqAybhhoFma3yDWXxdFMitqZMA0hBTXVbx53n4wro0+94Rww==');
define('LOGGED_IN_SALT',   'XfJILKVlPnzH1UDTO4QhvrR2ksqlfiWoDSDwsZczLAziNfTgL77T2qRDVUo8jZp4MJPzvKBStPB2qrD55fdiUg==');
define('NONCE_SALT',       '9UEp5+vv3jkQJhfhFc3Fo3V4U7RXPJIN9W8n471T/SVSpMzIEpSSkRet2dTc591FL4s4/I3ZplXE7TDsfwYbhw==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
