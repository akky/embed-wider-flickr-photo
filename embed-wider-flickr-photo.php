<?php
/*
Plugin Name: Embed Wider Flickr Photo
Plugin URI: http://akimoto.jp/blog/
Version: 1.0.1
Description: Enable larger photos when oembed from Flickr URL
Author: Akky AKIMOTO
Author URI: http://akimoto.jp/
License: Apache License version 2.0
*/

// This bootup file must run with older version of PHP
// to tell it is unsupported, instead of fatal error.

// direct access is not allowed.
defined( 'ABSPATH' ) || exit;

if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	// call modern PHP scripts
	require_once __DIR__ . '/vendor/autoload.php';

	new \Akky\EmbedWiderFlickrPlugin();
	//require_once 'main.php';
} else {
	/**
     * PHP version is too old.
	 */
	is_admin() && add_action('admin_notices', create_function('', "
	echo '
		<div class=\"error\"><p>
		Embed Wider Flickr Photo requires PHP 5.5 or above.
		</p><p>
		Please upgrade your PHP, or deactivate the plugin.
		</p></div>
	';
    if (isset(\$_GET['activate'])) unset(\$_GET['activate']);
    "));
}
