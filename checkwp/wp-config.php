<?php
define('WP_CACHE', true);
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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u200265274_abuqe' );

/** MySQL database username */
define( 'DB_USER', 'u200265274_ahyne' );

/** MySQL database password */
define( 'DB_PASSWORD', 'uVyjyquhaB' );

/** MySQL hostname */
define( 'DB_HOST', 'mysql' );

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
define( 'AUTH_KEY',          '`$8;kFE!(F;#R`Iig,oic<h$aiOt:F)zi.TxTiYTH^6bl&q=10]> 0X-%~}<<,#a' );
define( 'SECURE_AUTH_KEY',   '3|6&)nFn&vx2$9#j/@~ZCFNzb|6g_R_2h5>@{;h2jmq=7>f<=a^6MV*;Lu+ujZA:' );
define( 'LOGGED_IN_KEY',     'Y<b`%@_/rt7?O?;iV;ASc8])QxU686!Y;(h;/=C/`?aae[L5a@u?y7xEZLD4KA|z' );
define( 'NONCE_KEY',         ']zrf9^PePZ:.!;y3^0B{~q^:#@ll,F/kf&_Jx4hj.zPr{+SZD3k62r|xG*6<$k`p' );
define( 'AUTH_SALT',         'XN^9:&=G-x<- 4Z=xd<b,bXbHbetM-PZt;$CmA,s}cEKMug:<6?DTVCxrI]vPvCC' );
define( 'SECURE_AUTH_SALT',  'N`^h2bS!l]*BlQ(E]<9m6FavAV8aiu5SRmH*(nl7#Lt5IuRrV- |>Bq{-9HM7<z/' );
define( 'LOGGED_IN_SALT',    'B)$)@,%3]A#*i,.S%Ab.~X`:n&7>$aQt>RRR(Q5e_lp<P0rSYt$i$LinU<XHUck=' );
define( 'NONCE_SALT',        'FAJIi!7]Khai5>=X4z=ivk0;51yr_GnvfOI`DT9Z,S&s,h082VkA.`:s6s1(C-w9' );
define( 'WP_CACHE_KEY_SALT', 'uG%SqnYcO!hFXhaV/IIhx0)G7hX_GSi7Tm$;;j0.=p.BUvnWd5K=xWFy_.0lWbO(' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
