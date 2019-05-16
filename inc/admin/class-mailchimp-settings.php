<?php
/**
 * The essentials for MailChimp settings panel.
 *
 * This functions as a static singleton class.
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */

class MailChimpSettings {

	/**
	 * @var MailChimpSettings $instance The singleton instance of this class.
	 */
	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new MailChimpSettings;
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Initialize the class
	 *
	 * @uses MailChimpSettings::add_options_page
	 * @uses MailChimpSettings::register_settings
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public static function add_options_page() {
		add_options_page(
			'MailChimp Settings',
			'MailChimp Settings',
			'manage_options',
			'mailchimp_settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	public static function register_settings() {
		register_setting(
			'mailchimp_settings',
			'mailchimp_settings'
		);
		add_settings_section(
			'mailchimp_tools_api',
			__( 'MailChimp API Settings', 'mailchimp-tools' ),
			null,
			'mailchimp_settings'
		);
		add_settings_field(
			'mailchimp_api_key',
			__( 'MailChimp API Key', 'mailchimp-tools' ),
			array( __CLASS__, 'mailchimp_api_key_input' ),
			'mailchimp_settings',
			'mailchimp_tools_api'
		);
	}

	public static function mailchimp_api_key_input() {
		$settings = get_option( 'mailchimp_settings' );
		?>
			<input
				style="width: 300px;"
				type="text"
				name="mailchimp_settings[mailchimp_api_key]"
				id="mailchimp_api_key"
				value="<?php echo esc_attr( $settings['mailchimp_api_key'] ); ?>"
				placeholder="MailChimp API Key"
			/>
		<?php
	}

	public static function render_settings_page() {
		mailchimp_tools_render_template( 'settings.php' );
	}
}
