<div class="mailchimp-tools">
	<h2><?php echo $post_type_obj->labels->singular_name; ?> Campaign Settings</h2>
	<p>Set default values to use when creating and sending MailChimp campaigns via the <?php echo $post_type_obj->labels->singular_name; ?> editor.</p>

	<?php if ( $post_type_obj->name == 'post' ) { ?>
	<form method="post" action="<?php echo admin_url( 'edit.php?page=' . $settings_key ); ?>">
	<?php } else { ?>
	<form method="post" action="<?php echo admin_url( 'edit.php?post_type=' . $post_type_obj->name . '&page=' . $settings_key ); ?>">
	<?php } ?>

		<h3>Default campaign type:</h3>

		<ul>
			<li><input type="radio" <?php checked( $saved_settings['type'], 'regular' ); ?>
					name="<?php echo $settings_key; ?>[type]" value="regular">Regular</input></li>
			<li><input type="radio" <?php checked( $saved_settings['type'], 'plaintext' ); ?>
					name="<?php echo $settings_key; ?>[type]" value="plaintext">Text-only</input></li>
		</ul>

		<h3>Default list to send to:</h3>

		<ul>
		<?php if ( $lists['lists'] ) : ?>
			<?php foreach ( $lists['lists'] as $key => $list ) { ?>
				<li class="list">
					<input type="radio" <?php checked( $saved_settings['list_id'], $list['id'] ); ?>
						name="<?php echo $settings_key; ?>[list_id]" value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></input>
					<?php if ( isset( $segments[$list['id']] ) ) { ?>
						<h4>Saved segments:</h4>
						<ul class="segment-list">
						<?php foreach ( $segments[$list['id']] as $segment ) { ?>
							<li class="segment"><input type="radio" <?php checked( $saved_settings['segment']['saved_segment_id'], $segment['id'] ); ?>
								name="<?php echo $settings_key; ?>[segment][saved_segment_id]"
								value="<?php echo $segment['id']; ?>"><?php echo $segment['name']; ?></input></li>
						<?php } ?>
						</ul>
					<?php } ?>

					<?php if ( isset( $groups[$list['id']] ) ) { ?>
						<h4>Groups:</h4>
						<ul class="group-list">
						<?php foreach ( $groups[$list['id']]['categories'] as $group ) { ?>
							<li class="group"><input type="radio" <?php checked( $saved_settings['group']['saved_group_id'], $group['id'] ); ?>
								name="<?php echo $settings_key; ?>[group][saved_group_id]"
								value="<?php echo $group['id']; ?>"><?php echo $group['title']; ?></input>
								<?php if ( ! empty( $group['interests'] ) ) { ?>
								<ul>
									<?php foreach ( $group['interests'] as $subgroup ) { ?>
									<li class="subgroup"><input type="radio" <?php checked( $saved_settings['subgroup']['saved_subgroup_bit'], $subgroup['id'] ); ?>
										name="<?php echo $settings_key; ?>[subgroup][saved_subgroup_bit]"
										value="<?php echo $subgroup['id']; ?>"><?php echo $subgroup['name']; ?></input>
									</li>
									<?php } ?>
								</ul>
								<?php } ?>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>
		<?php else : ?>
			<em>You don't have any lists setup in MailChimp.</em>
		<?php endif; ?>
		</ul>

		<div id="mailchimp-tools-template" <?php if ( ! empty( $saved_settings ) && $saved_settings['type'] == 'plaintext' ) { ?>style="display: none;"<?php } ?>>
			<h3>Default template:</h3>
			<select name="<?php echo $settings_key; ?>[template_id]">
				<option value="">---</option>
				<?php foreach ( $templates['templates'] as $key => $template ) { ?>
					<option <?php selected( $saved_settings['template_id'], $template['id'] ); ?>
						value="<?php echo $template['id']; ?>" /><?php echo $template['name']; ?></option>
				<?php } ?>
			</select>
		</div>

		<div id="mailchimp-tools-default-test-emails">
			<h3>Default email addresses for campaign tests:</h3>
			<p>A comma-separated list of email addresses that campaign tests should be sent to.</p>
			<input type="text" name="<?php echo $settings_key; ?>[default_test_emails]"
				placeholder="Ex: freddie@mailchimp.com, mannie@mandrill.com..."
				<?php if ( ! empty( $saved_settings['default_test_emails'] ) ) { ?>value="<?php echo $saved_settings['default_test_emails']; ?>"<?php } ?> />
		</div>

		<p class="submit">
			<?php submit_button( 'Save', 'primary', 'save', false ); ?>
			<?php submit_button( 'Reset', 'delete',  'reset', false ); ?>
		</p>
	</form>
</div>
