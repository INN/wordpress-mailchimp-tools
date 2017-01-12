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
	function mailchimp_tools_register_for_post_type($post_type='post', $options=array()) {
		$settings = get_option( 'mailchimp_settings' );

		if ( empty( $settings['mailchimp_api_key'] ) ) {
			return false;
		}

		$options = wp_parse_args($options, array(
			'preview' => true,
			'editor' => true,
			'settings' => true
		));

		if ( (bool) $options['editor'] ) {
			new CampaignEditor( $post_type );
		}

		if ( (bool) $options['settings'] ) {
			new PostTypeSettings( $post_type );
		}
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

if ( ! function_exists( 'mailchimp_tools_get_existing_campaign_for_post' ) ) {
	/**
	 * See if the post has a Campaign ID stored and try retrieving Campaign data from MailChimp
	 *
	 * @since 0.0.1
	 */
	function mailchimp_tools_get_existing_campaign_data_for_post($post=null, $use_cache=true) {
		$post = get_post($post);
		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );
		if ( ! empty( $cid ) ) {
			$transient_key = 'mailchimp_tools_campaign_' . $cid;

			if ( $use_cache ) {
				$cached = get_transient( $transient_key );
				if ( $cached !== false ) {
					return $cached;
				}
			}

			$api = mailchimp_tools_get_api_handle();
			if ( ! empty( $api ) ) {
				$existing_campaign = $api->get( 'campaigns/' . $cid );
				if ( isset( $existing_campaign['campaigns'][0] ) ) { // @TODO test this offset
					$ret = $existing_campaign['campaigns'][0];
					set_transient( $transient_key, $ret, 60 );
					return $ret;
				}
			}
		}
		return null;
	}
}

/*
 * Other non-pluggable functions
 */

/**
 * Return a configured MailChimp API object
 *
 * @since 0.0.1
 */
function mailchimp_tools_get_api_handle($args=array()) {
	$settings = get_option( 'mailchimp_settings' );

	if ( empty( $settings['mailchimp_api_key'] ) ) {
		return false;
	}

	return new \DrewM\MailChimp\Mailchimp( $settings['mailchimp_api_key'] );
}

/**
 * Register MailChimp Tools assets
 *
 * @since 0.0.1
 */
function mailchimp_tools_register_assets() {
	// Styles
	wp_register_style(
		'mailchimp-tools-admin',
		MAILCHIMP_TOOLS_DIR_URI . '/assets/css/admin.css'
	);

	// Scripts
	wp_register_script(
		'mailchimp-tools-campaign-common',
		MAILCHIMP_TOOLS_DIR_URI . '/assets/js/campaign-common.js',
		array( 'jquery', 'backbone' ),
		MAILCHIMP_TOOLS_VER,
		true
	);
}
add_action( 'init', 'mailchimp_tools_register_assets' );
