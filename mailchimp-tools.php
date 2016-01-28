<?php
/*
 * MailChimp Tools for WordPress
 *
 * A collection of resuable WordPress components and functions to help you build your custom
 * newsletter authoring tool.
 *
 * @package MailChimp Tools
 */

/*
 * Only load once no matter how many projects include these tools
 */
if ( defined( 'MAILCHIMP_TOOLS_LOADED' ) && MAILCHIMP_TOOLS_LOADED ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log('Stopped unnecessary attempt to load MailChimp Tools.');
	}
	return;
}

/*
 * Constants
 */
define( 'MAILCHIMP_TOOLS_LOADED', true );
define( 'MAILCHIMP_TOOLS_DIR', __DIR__ );
/* TODO: using plugins_url makes this tool set way less flexible */
define( 'MAILCHIMP_TOOLS_DIR_URI', plugins_url(basename(__DIR__), __DIR__));
define( 'MAILCHIMP_TOOLS_TEMPLATE_DIR', MAILCHIMP_TOOLS_DIR . '/templates' );
define( 'MAILCHIMP_TOOLS_VER', '0.0.1' );

/*
 * Include pluggable functions
 */
function mailchimp_tools_include_pluggable_funcs() {
	require_once( __DIR__ . '/mailchimp-functions.php' );
}
add_action( 'plugins_loaded', 'mailchimp_tools_include_pluggable_funcs' );
