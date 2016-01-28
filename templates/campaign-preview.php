<script type="text/javascript">
(function() {
  var $ = jQuery;
  $(document).on('heartbeat-send.autosave', function() {
    $('iframe.mailchimp-tools-campaign-preview').attr('src', $('iframe.mailchimp-tools-campaign-preview').attr('src'));
  })
})();
</script>
<iframe class="mailchimp-tools-campaign-preview" src="<?php echo get_bloginfo('url'); ?>?campaign_preview=true&post_id=<?php echo esc_attr( $post->ID ); ?>"></iframe>
<p><a target="_blank" href="<?php echo get_bloginfo('url'); ?>?campaign_preview=true&post_id=<?php echo esc_attr( $post->ID ); ?>">Preview campaign in a new window &raquo;</a></p>
<p><a target="_blank" href="https://<?php echo $mc_api_endpoint; ?>.admin.mailchimp.com/campaigns/wizard/confirm?id=<?php echo $web_id; ?>">Preview &amp; edit campaign in MailChimp &raquo;</a></p>
