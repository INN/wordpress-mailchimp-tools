<?php
/**
 * Essential MailChimp meta box class.
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */
class MCMetaBox {
	public $label = 'MailChimp Meta box';
	public $id = 'mailchimp-meta-box';
	public $location = 'normal';

	public function __construct($post_type='post') {
		$this->post_type = $post_type;
		$this->api = mailchimp_tools_get_api_handle();

		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'save_post_' . $this->post_type, array( $this, 'process_form' ), 9999, 2 );
	}

	public function add_meta_box() {
		if ( apply_filters( 'mailchimp_tools_render_meta_box', true, $this ) ) {
			add_meta_box(
				$this->id,
				$this->label,
				array( $this, 'render_meta_box' ),
				$this->post_type,
				$this->location
			);
		}
	}

	public function process_form($post_id=null, $post=null) {
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
