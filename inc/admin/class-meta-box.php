<?php
/**
 * Essential MailChimp meta box class.
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */
class MCMetaBox {
	public function __construct($post_type='post') {
		$this->post_type = $post_type;
		$this->api = mailchimp_tools_get_api_handle();

		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'save_post', array( $this, 'process_form' ) );
	}

	public function add_meta_box() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'The `add_meta_box` method is not implemented.' );
		}
	}

	public function process_form() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'The `process_form` method is not implemented.' );
		}
	}

	public function render_meta_box() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'The `render_meta_box` method is not implemented.' );
		}
	}

	public function enqueue_assets() {
		$screen = get_current_screen();
		if ( $screen->post_type == $this->post_type ) {
			wp_enqueue_style( 'mailchimp-tools-admin' );
		}
	}
}
