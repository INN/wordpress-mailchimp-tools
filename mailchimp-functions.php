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

		if ( (bool) $options['preview'] ) {
			new CampaignPreview( $post_type );
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
	function mailchimp_tools_get_existing_campaign_data_for_post($post=null) {
		$post = get_post($post);
		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );
		if ( ! empty( $cid ) ) {
			$transient_key = 'mailchimp_tools_campaign_' . $cid;
			$cached = get_transient( $transient_key );
			if ( $cached !== false ) {
				return $cached;
			}

			$api = mailchimp_tools_get_api_handle();
			if ( ! empty( $api ) ) {
				$existing_campaign = $api->campaigns->getList( array( 'campaign_id' => $cid ) );
				if ( isset( $existing_campaign['data'][0] ) ) {
					$ret = $existing_campaign['data'][0];
					set_transient( $transient_key, $ret, 60 );
					return $ret;
				}
			}
		}
		return null;
	}
}

if ( ! function_exists( 'mailchimp_tools_get_template_source' ) ) {
	/**
	 * Get source code for a MailChimp template_id
	 *
	 * @since 0.0.1
	 */
	function mailchimp_tools_get_template_source($template_id=null) {
		if ( ! empty( $template_id ) ) {
			$transient_key = 'mailchimp_tools_template_source_' . $template_id;
			$cached = get_transient( $transient_key );
			if ( $cached !== false ) {
				return $cached;
			}

			$api = mailchimp_tools_get_api_handle();
			if ( ! empty( $api ) ) {
				$template_details = $api->templates->info( $template_id );
				$ret = $template_details['source'];
				set_transient( $transient_key, $ret, ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 60 : 60*60 );
				return $ret;
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

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$args = wp_parse_args( $args, array( 'debug' => true ) );
	}

	if ( empty( $settings['mailchimp_api_key'] ) ) {
		return false;
	}

	return new Mailchimp( $settings['mailchimp_api_key'], $args );
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
