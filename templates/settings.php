<div class="wrap">
	<h2><?php _e( 'MailChimp Tools', 'mailchimp-tools' ); ?></h2>

	<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">

		<?php settings_fields( 'mailchimp_settings' ); ?>
		<?php do_settings_sections( 'mailchimp_settings' ); ?>

		<p><a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Find-or-Generate-Your-API-Key"><?php _e( 'Find your MailChimp API Key', 'mailchimp-tools' ); ?></a></p>

		<?php submit_button(); ?>
	</form>
</div>
