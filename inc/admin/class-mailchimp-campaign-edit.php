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
}
