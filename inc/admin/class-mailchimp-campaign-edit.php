<?php
/**
 * TKTK.
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */
class CampaignEdit extends MCMetaBox {

	/*
	 * TODO: Meta box to allow:
	 * - Send post to MailChimp as draft
	 * - Schedule post to send as MailChimp Campaign
	 * - Send a post as a Campaign NOW
	 * - Send a preview/test email to select email address
	 * - Preview the Campaign in the post editor
	 * - Select a MailChimp template to use for the Campaign
	 * - Select a list (or list segment) to send to
	 */

	public function add_meta_box() {
		if ( ! is_array( $this->post_type ) ) {
			$this->post_type = (array) $this->post_type;
		}

		foreach ( $this->post_type as $post_type ) {
			add_meta_box(
				'mailchimp-campaign-edit',
				'MailChimp Campaign',
				array( $this, 'render_meta_box' ),
				$post_type,
				'advanced'
			);
		}
	}

	public function render_meta_box() {
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
			)
		);
		mailchimp_tools_render_template( 'campaign-edit.php', $context );
	}

	public function process_form() {
		if ( isset( $_POST ) && isset( $_POST['mailchimp'] ) ) {
			$data = $_POST['mailchimp'];

			if ( isset( $data['send'] ) ) {
				$this->send_campaign($data);
			}

			if ( isset( $data['draft'] ) ) {
				$this->create_campaign($data);
			}
		}
	}

	public function send_campaign($data) {
		$this->create_campaign($data);
		// Then send.
	}

	public function create_campaign($data) {
		$post = get_post();

		// Remove submit button value from $data
		foreach ( array( 'send', 'draft' ) as $submit_val ) {
			if ( isset( $data[$submit_val] ) ) {
				unset( $data[$submit_val] );
			}
		}

		// Stash campaign type and unset
		$type = $data['type'];
		unset( $data['type'] );

		// Stash segment options if present and unset
		$segment_options = ( isset( $data['segment'] ) ) ? $data['segment'] : null;
		unset( $data['segment'] );

		// Grab the list from MC to use its default values for to/from address
		$list_results = $this->api->lists->getList( array(
			'list_id' => $data['list_id']
		) );
		$list = $list_results['data'][0];

		// Compose campaign options using what's left in $data
		$campaign_options = wp_parse_args($data, array(
			'from_email' => $list['default_from_email'],
			'from_name' => $list['default_from_name'],
			'generate_text' => ( $type == 'plaintext' ) ? false : true
		));

		$html = apply_filters( 'the_content', $post->post_content );

		$campaign_content = array(
			'html' => $html, // the content!
			'text' => '' // Leave blank for the auto-generated text content
		);

		$response = $this->api->campaigns->create(
			$type,
			$campaign_options,
			$campaign_content,
			$segment_options,
			null
		);

		update_post_meta( $post->ID, 'mailchimp_web_id', $response['web_id'] );
		update_post_meta( $post->ID, 'mailchimp_mc_id', $response['mc_id'] );
	}

	public function enqueue_assets() {
		parent::enqueue_assets();

		wp_register_script(
			'mailchimp-tools-campaign',
			MAILCHIMP_TOOLS_DIR_URI . '/assets/js/campaign.js',
			array('jquery'),
			MAILCHIMP_TOOLS_VER,
			true
		);

		$screen = get_current_screen();
		if ( in_array( $screen->post_type, $this->post_type ) ) {
			wp_enqueue_script('mailchimp-tools-campaign');
		}

	}
}
