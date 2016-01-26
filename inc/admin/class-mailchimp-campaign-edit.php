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
		var_log('add_meta_box');
		add_meta_box(
			'mailchimp-campaign-edit',
			'MailChimp Campaign',
			array( $this, 'render_meta_box' ),
			$this->post_type,
			'side'
		);
	}

	public function render_meta_box() { ?>
		<p>Campaigns!</p><?php

		var_log('render_meta_box');
		$lists = $this->api->lists->getList(); ?>
			<ul>
			<?php foreach ( $lists['data'] as $key => $list ) { ?>
				<li><input type="checkbox" name="mailchimp_send_to_lists[]" value="<?php echo $list['id']; ?>" /><?php echo $list['name']; ?></input></li>
			<?php } ?>
			</ul><?php
	}

}
