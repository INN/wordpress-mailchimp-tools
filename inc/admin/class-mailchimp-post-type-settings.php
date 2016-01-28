<?php
/**
 * MailChimp per-post-type campaign settings
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */
class PostTypeSettings {

	/**
	 * TODO: Ability to set default values for:
	 *
	 * - Campaign type
	 * - List, list segment
	 * - Template
	 */

	public function __construct($post_type=null) {
		$this->post_type = $post_type;
		$this->post_type_obj = get_post_type_object( $this->post_type );
		$this->settings_key = $this->post_type_obj->name . '_mailchimp_settings';
		$this->init();
	}

	public function init() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function add_options_page() {
		$parent_slug = ( $this->post_type == 'post' ) ? 'edit.php' : 'edit.php?post_type=' . $this->post_type;

		add_submenu_page(
			$parent_slug,
			'MailChimp Campaign Settings',
			'MailChimp Campaign Settings',
			'manage_options',
			$this->settings_key,
			array( $this, 'render_settings_page' )
		);
	}

	public function render_settings_page() {
		if ( isset( $_POST ) && isset( $_POST[$this->settings_key] ) ) {
			$this->process_form($_POST[$this->settings_key]);
		}
		$this->api = mailchimp_tools_get_api_handle();
		$lists = $this->api->lists->getList();
		$segments = array();

		foreach ( $lists['data'] as $list ) {
			$list_segments = $this->api->lists->segments( $list['id'] );
			if ( ! empty( $list_segments['saved'] ) ) {
				$segments[$list['id']] = $list_segments['saved'];
			}
		}

		$context = array(
			'lists' => $lists,
			'segments' => $segments,
			'templates' => $this->api->templates->getList(
				array(
					'gallery' => false,
					'base' => false
				),
				array( 'include_drag_and_drop' => true )
			),
			'post_type_obj' => $this->post_type_obj,
			'settings_key' => $this->settings_key,
			'saved_settings' => get_option( $this->settings_key, false )
		);
		mailchimp_tools_render_template( 'post-type-settings.php', $context );
	}

	public function process_form($data=array()) {
		if ( isset( $_POST['reset'] ) ) {
			delete_option( $this->settings_key );
			return;
		}

		if ( ! isset( $_POST['save'] ) ) {
			return;
		}

		if ( trim( $data['template_id'] ) == '' ) {
			unset( $data['template_id'] );
		}

		if ( ! empty( $data ) ) {
			update_option( $this->settings_key, $data );
		}
	}

	public function enqueue_assets() {
		$screen = get_current_screen();
		$result = preg_match( '/^\w+_page_\w+_mailchimp_settings$/', $screen->base );
		if ( $result ) {
			wp_enqueue_script( 'mailchimp-tools-campaign-common' );
			wp_enqueue_style( 'mailchimp-tools-admin' );
		}
	}
}
