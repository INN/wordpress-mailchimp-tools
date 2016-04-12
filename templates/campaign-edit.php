<div class="mailchimp-tools">

	<?php if ( ! empty( $existing ) && $existing['status'] !== 'save' ) { ?>
		<p>A campaign for this post has already been sent. <a target="_blank" href="https://<?php echo $mc_api_endpoint; ?>.admin.mailchimp.com/reports/summary?id=<?php echo $web_id; ?>">Click here view details in MailChimp.</a></p>
	<?php } else { ?>

		<?php if ( ! empty( $existing ) && $existing['status'] == 'save' ) { ?>
			<p>A campaign already exists for this post. <a target="_blank" href="https://<?php echo $mc_api_endpoint; ?>.admin.mailchimp.com/campaigns/wizard/confirm?id=<?php echo $web_id; ?>">Click here to continue editing the campaign in MailChimp.</a></p>
		<?php } ?>

		<h3>Choose a campaign type:</h3>

		<ul>
			<?php if ( ! empty( $existing ) ) { ?>
				<li><input type="radio" name="mailchimp[type]" checked readonly class="disabled"
					value="<?php echo $existing['type']; ?>"><?php echo ( $existing['type'] == 'regular' ) ? 'Regular' : 'Text-only'; ?></input></li>
			<?php } else { ?>
				<li><input type="radio" name="mailchimp[type]" <?php checked( $saved_settings['type'], 'regular' ); ?> value="regular">Regular</input></li>
				<li><input type="radio" name="mailchimp[type]" <?php checked( $saved_settings['type'], 'plaintext' ); ?> value="plaintext">Text-only</input></li>
			<?php } ?>
		</ul>

		<h3>Choose a list to send to:</h3>

		<ul>
		<?php foreach ( $lists['data'] as $key => $list ) { ?>
			<li class="list">
				<?php if ( ! empty( $existing ) ) { ?>
					<input type="radio" name="mailchimp[list_id]" value="<?php echo $list['id']; ?>" <?php checked( $existing['list_id'], $list['id'] ); ?>><?php echo $list['name']; ?></input>
				<?php } else if ( ! empty( $saved_settings['list_id'] ) ) { ?>
					<input type="radio" name="mailchimp[list_id]" value="<?php echo $list['id']; ?>" <?php checked( $saved_settings['list_id'], $list['id'] ); ?>><?php echo $list['name']; ?></input>
				<?php } else { ?>
					<input type="radio" name="mailchimp[list_id]" value="<?php echo $list['id']; ?>" <?php checked( $existing['list_id'], $list['id'] ); ?>><?php echo $list['name']; ?></input>
				<?php } ?>

				<?php if ( isset( $segments[$list['id']] ) ) { ?>
					<h4>Saved segments:</h4>
					<ul class="segment-list">
					<?php foreach ( $segments[$list['id']] as $segment ) { ?>
						<?php if ( ! empty( $existing ) ) { ?>
							<li class="segment"><input type="radio" name="mailchimp[segment][saved_segment_id]" value="<?php echo $segment['id']; ?>" <?php checked( $existing['saved_segment']['id'], $segment['id'] ); ?>><?php echo $segment['name']; ?></input></li>
						<?php } else if ( ! empty( $saved_settings['segment'] ) ) { ?>
							<li class="segment"><input type="radio" name="mailchimp[segment][saved_segment_id]" value="<?php echo $segment['id']; ?>" <?php checked( $saved_settings['segment']['saved_segment_id'], $segment['id'] ); ?>><?php echo $segment['name']; ?></input></li>
						<?php } else { ?>
							<li class="segment"><input type="radio" name="mailchimp[segment][saved_segment_id]" value="<?php echo $segment['id']; ?>"><?php echo $segment['name']; ?></input></li>
						<?php } ?>
					<?php } ?>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>
		</ul>

		<h3>Campaign details:</h3>
		<label for="name"><p>Campaign title:</p>
		<input type="text" name="mailchimp[title]" placeholder="Campaign title (for internal use)" <?php if ( $existing['title'] ) { ?>value="<?php echo $existing['title']; ?>"<?php } ?>></input><br />
			<a href="#" id="mailchimp-use-post-title-for-campaign-title">Use post title as campaign title</a>
		</label>
		<label for="subject"><p>Campaign subject:</p>
			<input type="text" name="mailchimp[subject]" placeholder="Campaign email subject line (subscribers will see this)" <?php if ( $existing['subject'] ) { ?>value="<?php echo $existing['subject']; ?>"<?php } ?>></input><br />
			<a href="#" id="mailchimp-use-post-title-for-campaign-subject">Use post title as campaign subject</a>
		</label>

		<div id="mailchimp-tools-template" <?php if ( ! empty( $existing ) && $existing['type'] == 'plaintext' || ! empty( $saved_settings['type'] ) && $saved_settings['type'] == 'plaintext' ) { ?>style="display: none;"<?php } ?>>
			<h3>Choose a template:</h3>
			<select name="mailchimp[template_id]">
				<?php foreach ( $templates['user'] as $key => $template ) { ?>
					<option value="<?php echo $template['id']; ?>"
						<?php selected( $saved_settings['template_id'], $template['id'] ); ?>
						<?php selected( $existing['template_id'], $template['id'] ); ?> /><?php echo $template['name']; ?></option>
				<?php } ?>
			</select>
		</div>

		<h3>Campaign actions:</h3>
		<p>
			<?php $attrs = ( ! empty( $existing ) && $existing['status'] !== 'save' ) ? array( 'disabled' => 'disabled' ) : null; ?>
			<?php submit_button('Send now', 'primary', 'mailchimp[send]', false, $attrs); ?>
			<?php submit_button(( empty( $existing ) ) ? 'Create draft' : 'Update draft', 'large', 'mailchimp[draft]', false, $attrs); ?>
			<?php if ( ! empty( $existing ) ) { submit_button('Send test', 'large', 'mailchimp[send_test]', false, $attrs); } ?>

		</p>
	<?php } ?>
</div>

<script type="text/template" id="mailchimp-tools-modal-tmpl">
	<div class="mailchimp-tools-modal-header">
		<div class="mailchimp-tools-modal-close"><span class="close">&#10005;</span></div>
	</div>
	<div class="mailchimp-tools-modal-content"><% if (content) { %><%= content %><% } %></div>
	<div class="mailchimp-tools-modal-actions">
		<span class="spinner"></span>
		<% _.each(actions, function(v, k) { %>
			<a href="#" class="<%= k %> button button-primary"><%= k %></a>
		<% }); %>
	</div>
</script>

<script type="text/template" id="mailchimp-tools-test-emails-tmpl">
	<div class="mailchimp-tools-test-emails">
		<h3>Send a test</h3>
		<p>Send a test to</p>
		<input type="text" name="mailchimp[test_emails]" id="mailchimp[test_emails]" placeholder="Ex: freddie@mailchimp.com, mannie@mandrill.com..." />
		<small>Comma separate emails to send to multiple accounts.</small>
	</div>
</script>
