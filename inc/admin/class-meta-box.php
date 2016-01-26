<?php
/**
 * Essential MailChimp meta box class.
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */
class MCMetaBox {
	public function __construct($options=array()) {
		$options = wp_parse_args($options, array(
			'post_type' => 'post'
		));
		$this->post_type = $options['post_type'];
		$this->add_meta_box();
		$this->api = mailchimp_tools_get_api_handle();
	}

	public function add_meta_box() {
		throw new NotImplementedException('The `add_meta_box` method is not implemented.');
	}

	public function render_meta_box() {
		throw new NotImplementedException('The `render_meta_box` method is not implemented.');
	}
}
