<script type="text/javascript">
(function() {
	var $ = jQuery;

	var refresh_preview = function() {
		$('iframe.mailchimp-tools-campaign-preview').attr(
			'src', $('iframe.mailchimp-tools-campaign-preview').attr('src'));
		return false;
	};

	//$(document).on('heartbeat-tick.autosave', refresh_preview);

	$(document).ready(function() {
		$('a.mailchimp-refresh-preview').click(refresh_preview);
	});
})();
</script>
<iframe class="mailchimp-tools-campaign-preview" src="<?php echo get_bloginfo('url'); ?>?campaign_preview=true&post_id=<?php echo esc_attr( $post->ID ); ?>"></iframe>
<p><a class="mailchimp-refresh-preview" href="#">Refresh preview</a></p>
<p><a target="_blank" href="<?php echo get_bloginfo('url'); ?>?campaign_preview=true&post_id=<?php echo esc_attr( $post->ID ); ?>">Preview campaign in a new window &raquo;</a></p>
<p><a target="_blank" href="https://<?php echo $mc_api_endpoint; ?>.admin.mailchimp.com/campaigns/wizard/confirm?id=<?php echo $web_id; ?>">Preview &amp; edit campaign in MailChimp &raquo;</a></p>
