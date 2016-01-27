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
	 * - Send a preview/test email to select email address
	 * - Schedule post to send as MailChimp Campaign
	 * - Preview the Campaign in the post editor
	 * - DONE: Send post to MailChimp as draft
	 * - DONE: Send a post as a Campaign NOW
	 * - DONE: Select a MailChimp template to use for the Campaign
	 * - DONE: Select a list (or list segment) to send to
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
		$post = get_post();
		$settings = get_option( 'mailchimp_settings' );

		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );
		if ( ! empty( $cid ) ) {
			$existing_campaign = $this->api->campaigns->getList( array( 'campaign_id' => $cid ) );
			if ( isset( $existing_campaign['data'][0] ) ) {
				$existing = $existing_campaign['data'][0];
			}
		}

		$lists = $this->api->lists->getList();
		$segments = array();

		foreach ( $lists['data'] as $list ) {
			$list_segments = $this->api->lists->segments( $list['id'] );
			if ( ! empty( $list_segments['saved'] ) ) {
				$segments[$list['id']] = $list_segments['saved'];
			}
		}

		$web_id = get_post_meta( $post->ID, 'mailchimp_web_id', true );
		$mc_api_key_parts = explode( '-', $settings['mailchimp_api_key'] );
		$mc_api_endpoint = $mc_api_key_parts[1];

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
			'existing' => $existing,
			'mc_api_endpoint' => $mc_api_endpoint,
			'web_id' => $web_id
		);
		mailchimp_tools_render_template( 'campaign-edit.php', $context );
	}

	public function process_form() {
		if ( isset( $_POST ) && isset( $_POST['mailchimp'] ) ) {
			$data = $_POST['mailchimp'];

			if ( isset( $data['send'] ) ) {
				$this->send_campaign( $data );
			}

			if ( isset( $data['draft'] ) ) {
				$this->create_or_update_campaign( $data );
			}
		}
	}

	public function send_campaign($data) {
		$this->create_or_update_campaign( $data );

		$post = get_post();
		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );
		if ( ! empty( $cid ) ) {
			$this->api->campaigns->send( $cid );
		}
	}

	public function create_or_update_campaign($data) {
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

		if ( $type == 'plaintext' ) {
			unset( $data['template_id'] );
		}

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
			'html' => $html,
			'text' => wp_strip_all_tags($html),
			'sections' => array(
				'body' => $html,
				'header' => '<h1>' . $post->post_title . '</h1>'
			)
		);

		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );
		if ( empty( $cid ) ) {
			$response = $this->api->campaigns->create(
				$type,
				$campaign_options,
				$campaign_content,
				$segment_options,
				null
			);

			update_post_meta( $post->ID, 'mailchimp_web_id', $response['web_id'] );
			update_post_meta( $post->ID, 'mailchimp_cid', $response['id'] );
		} else {
			$updates = array(
				'options' => $campaign_options,
				'content' => $campaign_content,
				'segment_opts' => $segment_options
			);

			foreach ( $updates as $name => $value ) {
				$this->api->campaigns->update($cid, $name, $value);
			}
		}
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
