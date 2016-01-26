<div class="mailchimp-tools">
	<h3>Choose a campaign type:</h3>
	<ul>
		<?php if ( empty( $existing ) ) { ?>
			<li><input type="radio" name="mailchimp[type]" value="regular">Regular</input></li>
			<li><input type="radio" name="mailchimp[type]" value="plaintext">Text-only</input></li>
		<?php } else { ?>
			<li><input type="radio" name="mailchimp[type]" checked disabled
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

	<h3>Choose a template:</h3>
	<select name="mailchimp[template_id]">
		<?php foreach ( $templates['user'] as $key => $template ) { ?>
			<option value="<?php echo $template['id']; ?>" <?php selected( $existing['template_id'], $template['id'] ); ?>" /><?php echo $template['name']; ?></option>
		<?php } ?>
	</select>

	<h3>Campaign actions:</h3>
	<p>
		<input type="submit" class="button button-primary button-large" name="mailchimp[send]" id="send" value="Send now"></input>
		<input type="submit" class="button button-large" name="mailchimp[draft]" id="draft" value="<?php echo ( empty( $existing ) ) ? 'Create' : 'Update'; ?> draft"></input>
		<?php /* <input type="submit" class="button button-large" name="mailchimp[schedule]" id="schedule" value="Schedule send"></input> */ ?>
	</p>
</div>
