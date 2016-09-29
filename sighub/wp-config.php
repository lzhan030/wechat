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
define('FORCE_SSL_ADMIN', true);

define('WP_DEBUG_DISPLAY', false);
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wechat');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '12sh99faA');

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
define('AUTH_KEY',         '+-h1qmgB:]+N@z6m;;I;$3>@&y66;*HOy>|wH={er`#rYh8T}a[-LM+~He{3`wS+');
define('SECURE_AUTH_KEY',  'VcZ#i@pIo*GxA=|yd^bQJV-V+e=YVmo9N w-0g[2T@]VkD`BM10dOTr,MZ]ri:cV');
define('LOGGED_IN_KEY',    '-!pcZ|$z[bI-}&#`H8xnsErO#ALDu.3[En+`{+(+e/5@>%MXF=<8PJ@qAo`lat`>');
define('NONCE_KEY',        'U7es=inDc~?1%9PB.upEOkI24D]<80Rccr7X:n=C2G>-9kTfFa&%wVGo!GH.13KG');
define('AUTH_SALT',        'W.8HD :8k=5XqI} 4g5fe6~[<Sd`3F9a5H_sJ!@0S@5F@_D@&Ls|~w m?hu%E(`M');
define('SECURE_AUTH_SALT', '+pp6#VL+g&|{QgkIID0ZpE%hY=KK#Y70-i^SRSlln9%6ENvD]{J*{GMa>GH%Zzfp');
define('LOGGED_IN_SALT',   'q=l%zitii(-SiRNDj qzR]_ZLUhy_T/we},T}PJAD/TxK4sG*`(ZAb/0qyuDV=AL');
define('NONCE_SALT',       '!|URcA7G{Jt9y)T)!n-87?qTSCuOXz&P5]Wx}/q+t$hzu+zOL23%}l8dD]kwn-{u');

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
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'zh_CN');


if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    $_SERVER['HTTPS'] = 'on';
	
/**#@+
 * OPENID有效期设置。
 *
 * @since 2.6.0
 */
define('OPENID_EXPIRE_TIME',  180);

/**#@+
 * 微支付最大金额设置。
 *
 * @since 2.6.0
 */
define('WEPAY_MAX_TOTAL_FEE',  15000);

/**#@+
 * 定期查询订单间隔设置1小时。
 *
 * @since 2.6.0
 */
define('ORDER_QUERY',  3600);

/**#@+
 * 收货确认间隔查询设置1天。
 *
 * @since 2.6.0
 */
define('DELIVERY_CONFIRMED',  86400);

/**#@+
 * 用于将mobiletheme中的posts同步到第三方平台的ACCESS URL设置。
 *
 * @since 2.6.0
 */
//define('THIRD_PARTY_ACCESS_URL',  'http://www.qq.com');
//define('THIRD_PARTY_ACCESS_URL',  'http://2.wpcloudforsina.sinaapp.com/test-1.0.0-BUILD-SNAPSHOT/postmsg');
define('THIRD_PARTY_ACCESS_URL',  'http://www.yuanworld.org/WifiAP/BbsItem.do');

/**
 *
 * 设置wordpress允许上传所有的文件类型。
 *
 */
define('ALLOW_UNFILTERED_UPLOADS', true);

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
