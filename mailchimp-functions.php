<?php
/*
 * Collection of pluggable functions used throughout MailChimp Tools
 *
 * @package MailChimp Tools
 */

/**
 * Render a template by specifying a filename and context
 *
 * @param (string) $template -- the filename of the template to render.
 * @param (array) $context -- associative array of values used within the template.
 *
 * @since 0.0.1
 */
if ( ! function_exists( 'mailchimp_tools_render_template' ) ) {
	function mailchimp_tools_render_template($template, $context=false) {
		if ( ! empty( $context ) )
			extract( $context );

		include MAILCHIMP_TOOLS_TEMPLATE_DIR . '/' . $template;
	}
}

if ( ! function_exists( 'mailchimp_tools_register_for_post_type' ) ) {
	/**
	 * Register MailChimp Tools for a Post Type
	 *
	 * @since 0.0.1
	 */
	function mailchimp_tools_register_for_post_type($options=array()) {
		$defaults = array(
			'post_type' => array('post')
		);
		$options = wp_parse_args( $options, $defaults );

		new CampaignEdit();
	}
}

if ( ! function_exists( 'mailchimp_tools_admin_init_settings' ) ) {
	/**
	 * Initialize MailChimp Settings
	 *
	 * @since 0.0.1
	 */
	function mailchimp_tools_admin_init_settings() {
		MailChimpSettings::get_instance();
	}
	add_action( 'init', 'mailchimp_tools_admin_init_settings' );
}

/**
 * Return a configured MailChimp API object
 *
 * @since 0.0.1
 */
function mailchimp_tools_get_api_handle($args=array()) {
	$settings = get_option( 'mailchimp_settings' );
	$args = wp_parse_args($args, array(
		'debug' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? WP_DEBUG : false
	));

	return new Mailchimp( $settings['mailchimp_api_key'], $args );
}
