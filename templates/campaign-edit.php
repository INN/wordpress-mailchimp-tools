<div class="mailchimp-tools">

	<?php if ( ! empty( $existing ) && $existing['status'] !== 'save' ) { ?>
		<p>A campaign for this post has already been sent. <a target="_blank" href="https://<?php echo $mc_api_endpoint; ?>.admin.mailchimp.com/reports/summary?id=<?php echo $web_id; ?>">Click here view details in MailChimp.</a></p>
	<?php } else { ?>

		<?php if ( ! empty( $existing ) && $existing['status'] == 'save' ) { ?>
			<p>A campaign already exists for this post. <a target="_blank" href="https://<?php echo $mc_api_endpoint; ?>.admin.mailchimp.com/campaigns/wizard/confirm?id=<?php echo $web_id; ?>">Click here to continue editing the campaign in MailChimp.</a></p>
		<?php } ?>

		<h3>Choose a campaign type:</h3>

		<ul>
			<?php if ( empty( $existing ) ) { ?>
				<li><input type="radio" name="mailchimp[type]" value="regular">Regular</input></li>
				<li><input type="radio" name="mailchimp[type]" value="plaintext">Text-only</input></li>
			<?php } else { ?>
				<li><input type="radio" name="mailchimp[type]" checked readonly class="disabled"
						value="<?php echo $existing['type']; ?>"><?php echo ( $existing['type'] == 'regular' ) ? 'Regular' : 'Text-only'; ?></input></li>
			<?php } ?>
		</ul>

		<h3>Choose a list to send to:</h3>

		<ul>
		<?php foreach ( $lists['data'] as $key => $list ) { ?>
			<li class="list">
				<input type="radio" name="mailchimp[list_id]" value="<?php echo $list['id']; ?>" <?php checked( $existing['list_id'], $list['id'] ); ?>><?php echo $list['name']; ?></input>
				<?php if ( isset( $segments[$list['id']] ) ) { ?>
					<h4>Saved segments:</h4>
					<ul class="segment-list">
					<?php foreach ( $segments[$list['id']] as $segment ) { ?>
						<li class="segment"><input type="radio" name="mailchimp[segment][saved_segment_id]" value="<?php echo $segment['id']; ?>" <?php checked( $existing['saved_segment']['id'], $segment['id'] ); ?>><?php echo $segment['name']; ?></input></li>
					<?php } ?>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>
		</ul>

		<h3>Campaign details:</h3>
		<label for="name"><p>Campaign title:</p>
		<input type="text" name="mailchimp[title]" placeholder="Campaign title (for internal use)" <?php if ( $existing['title'] ) { ?>value="<?php echo $existing['title']; ?>"<?php } ?>></input>
		</label>
		<label for="subject"><p>Campaign subject:</p>
			<input type="text" name="mailchimp[subject]" placeholder="Campaign email subject line (subscribers will see this)" <?php if ( $existing['subject'] ) { ?>value="<?php echo $existing['subject']; ?>"<?php } ?>></input>
		</label>

		<div id="mailchimp-tools-template" <?php if ( ! empty( $existing ) && $existing['type'] == 'plaintext' ) { ?>style="display: none;"<?php } ?>>
			<h3>Choose a template:</h3>
			<select name="mailchimp[template_id]">
				<?php foreach ( $templates['user'] as $key => $template ) { ?>
					<option value="<?php echo $template['id']; ?>" <?php selected( $existing['template_id'], $template['id'] ); ?>" /><?php echo $template['name']; ?></option>
				<?php } ?>
			</select>
		</div>

		<h3>Campaign actions:</h3>
		<p>
			<?php $attrs = ( ! empty( $existing ) && $existing['status'] !== 'save' ) ? array( 'disabled' => 'disabled' ) : null; ?>
			<?php submit_button('Send now', 'primary', 'mailchimp[send]', false, $attrs); ?>
			<?php submit_button(( empty( $existing ) ) ? 'Create draft' : 'Update draft', 'large', 'mailchimp[draft]', false, $attrs); ?>
		</p>
	<?php } ?>
</div>
