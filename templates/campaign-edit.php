<div class="mailchimp-tools">

	<h3>Choose a campaign type:</h3>
	<ul>
		<li><input type="radio" name="campaign_type" value="regular">Regular</input></li>
		<li><input type="radio" name="campaign_type" value="text">Text-only</input></li>
	</ul>

	<h3>Choose a list to send to:</h3>

	<ul>
	<?php foreach ( $lists['data'] as $key => $list ) { ?>
		<li>
			<input type="radio" name="mailchimp_send_to_lists" value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></input>
			<?php if ( isset( $segments[$list['id']] ) ) { ?>
				<h4>Saved segments:</h4>
				<ul class="segment-list">
				<?php foreach ( $segments[$list['id']] as $segment ) { ?>
					<li><input type="radio" name="mailchimp_send_to_list_segment" value="<?php echo $segment['id']; ?>"><?php echo $segment['name']; ?></input></li>
				<?php } ?>
				</ul>
			<?php } ?>
		</li>
	<?php } ?>
	</ul>

	<h3>Campaign details:</h3>
	<label for="campaign_name"><p>Campaign name:</p>
		<input type="text" name="campaign_name" placeholder="Campaign name (for internal use)"></input>
	</label>
	<label for="campaign_subject"><p>Campaign subject:</p>
		<input type="text" name="campaign_subject" placeholder="Campaign email subject line (subscribers will see this)"></input>
	</label>

	<h3>Choose a template:</h3>
	<select name="campaign_template">
		<?php foreach ( $templates['user'] as $key => $template ) { ?>
			<option <?php selected(get_option('lroundups_mailchimp_template'), $template['id'], true); ?> value="<?php echo $template['id']; ?>" /><?php echo $template['name']; ?></option>
		<?php } ?>
	</select>
</div>
