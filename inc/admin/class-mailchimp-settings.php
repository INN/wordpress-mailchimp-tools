<?php
/**
 * The essentials for MailChimp settings panel.
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */

class MailChimpSettings {

	private static $instance;

	function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new MailChimpSettings;
			self::$instance->init();
		}

		return self::$instance;
	}

	function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_options_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	function add_options_page() {
		add_options_page(
			'MailChimp Settings',
			'MailChimp Settings',
			'manage_options',
			'mailchimp_settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	function register_settings() {
		register_setting(
			'mailchimp_settings',
			'mailchimp_settings',
			array( __CLASS__, 'validate_mailchimp_integration' )
		);
		add_settings_section(
			'mailchimp_tools_api',
			__( 'MailChimp API Settings', 'mailchimp-tools' ),
			null,
			'mailchimp_settings'
		);
		add_settings_field(
			'use_mailchimp_integration',
			__( 'Use MailChimp Integration?', 'mailchimp-tools' ),
			array( __CLASS__, 'mailchimp_integration_input' ),
			'mailchimp_settings',
			'mailchimp_tools_api'
		);
		add_settings_field(
			'mailchimp_api_key',
			__( 'MailChimp API Key', 'mailchimp-tools' ),
			array( __CLASS__, 'mailchimp_api_key_input' ),
			'mailchimp_settings',
			'mailchimp_tools_api'
		);
	}

	function validate_mailchimp_integration($input) {
		if ( empty( $_POST['mailchimp_tools_mailchimp_api_key'] ) && isset( $_POST['mailchimp_tools_use_mailchimp_integration'] ) ) {
			add_settings_error(
				'mailchimp_tools_use_mailchimp_integration',
				'mailchimp_tools_use_mailchimp_integration_error',
				__( 'Please enter a valid MailChimp API Key.', 'mailchimp-tools' ),
				'error'
			);
			return '';
		}

		return $input;
	}

	function mailchimp_integration_input() {
		$settings = get_option( 'mailchimp_settings' ); ?>
		<input type="checkbox" name="mailchimp_settings[use_mailchimp_integration]" id="use_mailchimp_integration"
		<?php checked( $settings['use_mailchimp_integration'], 'on', true ); ?> /><?php
	}

	function mailchimp_api_key_input() {
		$settings = get_option( 'mailchimp_settings' ); ?>
		<input style="width: 300px;" type="text" name="mailchimp_settings[mailchimp_api_key]" id="mailchimp_api_key"
			value="<?php echo $settings['mailchimp_api_key']; ?>"
			placeholder="Mailchimp API Key" /><?php
	}

	function render_settings_page() {
		mailchimp_tools_render_template( 'settings.php' );
	}
}
