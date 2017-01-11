<?php
/**
 * MailChimp Campaign editor meta box
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */
class CampaignEditor extends MCMetaBox {

	/*
	 * TODO:
	 * - Send a preview/test email to select email address
	 * - Schedule post to send as MailChimp Campaign
	 * - WIP: Preview the Campaign in the post editor
	 */

	public $label = 'MailChimp Campaign Editor';

	public $id = 'mailchimp-campaign-edit';

	public $location = 'advanced';

	public function render_meta_box() {
		$post = get_post();
		$settings = get_option( 'mailchimp_settings' );
		$existing = mailchimp_tools_get_existing_campaign_data_for_post( $post, false );
		$lists = $this->api->lists->getList();
		$segments = array();
		$groups = array();

		foreach ( $lists['data'] as $list ) {
			$list_segments = $this->api->lists->segments( $list['id'] );
			if ( ! empty( $list_segments['saved'] ) ) {
				$segments[$list['id']] = $list_segments['saved'];
			}
			try {
				$list_groups = $this->api->lists->interestGroupings( $list['id'] );
				if ( ! empty( $list_groups ) ) {
					$groups[$list['id']] = $list_groups;
				}
			} catch ( MailChimp_List_InvalidOption $e ) {
				continue;
			}
		}

		$web_id = get_post_meta( $post->ID, 'mailchimp_web_id', true );
		$mc_api_key_parts = explode( '-', $settings['mailchimp_api_key'] );
		$mc_api_endpoint = $mc_api_key_parts[1];

		$post_type_obj = get_post_type_object( $this->post_type );
		$settings_key = $post_type_obj->name . '_mailchimp_settings';
		$saved_settings = get_option( $settings_key, false );

		/**
		 * Try to get existing group and subgroup data.
		 *
		 * Do this here instead of in the campaign-edit.php template
		 * because it's fairly complex to parse the necessary info
		 * from $existing data.
		 */
		if ( ! empty( $existing['segment_opts'] ) ) {
			$existing_seg_opts = $existing['segment_opts'];

			if ( isset( $existing_seg_opts['conditions'] ) ) {
				$conditions = $existing_seg_opts['conditions'][0];

				$saved_group_id = str_replace( 'interests-', '', $conditions['field'] );
				$saved_subgroup_bit = $conditions['value'][0];

				$saved_group_settings = array(
					'group' => array(
						'saved_group_id' => $saved_group_id,
					),
					'subgroup' => array(
						'saved_subgroup_bit' => $saved_subgroup_bit
					)
				);

				/**
				 * Use existing values for groups and subgroups if they were preset in $existing data
				 */
				if ( isset( $saved_settings['segment'] ) ) {
					unset( $saved_settings['segment'] );
				}
				$saved_settings = wp_parse_args( $saved_group_settings, $saved_settings );
			}
		}

		$context = array(
			'lists' => $lists,
			'segments' => $segments,
			'groups' => $groups,
			'templates' => $this->api->templates->getList(
				array(
					'gallery' => false,
					'base' => false
				),
				array( 'include_drag_and_drop' => true )
			),
			'existing' => $existing,
			'mc_api_endpoint' => $mc_api_endpoint,
			'web_id' => $web_id,
			'saved_settings' => $saved_settings,
		);
		mailchimp_tools_render_template( 'campaign-edit.php', $context );
	}

	public function process_form($post_id=null, $post=null) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( false !== wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( isset( $_POST ) && isset( $_POST['mailchimp'] ) ) {
			$data = $_POST['mailchimp'];

			if ( ! isset( $data['draft'] ) && ! isset( $data['send'] ) && ! isset( $data['send_test'] ) ) {
				return;
			}

			if ( isset( $data['send_test'] ) ) {
				$this->send_test( $data, $post );
			}

			if ( isset( $data['send'] ) ) {
				$this->send_campaign( $data, $post );
			}

			if ( isset( $data['draft'] ) ) {
				$this->create_or_update_campaign( $data, $post );
			}
		}
	}

	public function send_test($data, $post=null) {
		if ( empty( $post ) ) {
			$post = get_post();
		}
		$test_emails = array_map( function($x) { return trim( $x ); }, explode( ',', $data['test_emails'] ) );
		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );
		if ( ! empty( $cid ) ) {
			$this->api->campaigns->sendTest( $cid, $test_emails, 'html' );
		}
	}

	public function send_campaign($data, $post=null) {
		if ( empty( $post ) ) {
			$post = get_post();
		}
		$this->create_or_update_campaign( $data, $post );
		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );
		if ( ! empty( $cid ) ) {
			$this->api->campaigns->send( $cid );
		}
	}

	public function create_or_update_campaign($data, $post=null) {
		if ( empty($post) ) {
			$post = get_post();
		}

		$data = stripslashes_deep( $data );

		// Remove submit button value from $data
		foreach ( array( 'send', 'draft', 'send_test' ) as $submit_val ) {
			if ( isset( $data[$submit_val] ) ) {
				unset( $data[$submit_val] );
			}
		}

		// Stash campaign type and unset
		$type = $data['type'];
		unset( $data['type'] );

		if ( $type == 'plaintext' ) {
			unset( $data['template_id'] );
		}

		// Stash segment options if present and unset
		$segment_options = null;
		if ( isset( $data['segment'] ) ) {
			$segment_options = $data['segment'];
			unset( $data['segment'] );
		}

		// Stash group segment options if present and unset
		if ( isset( $data['group'] ) && isset( $data['subgroup'] ) ) {
			$group = $data['group'];
			$subgroup = $data['subgroup'];

			$segment_options = array(
				'match' => 'any',
				'conditions' => array(
					array(
						'field' => 'interests-' . $group['saved_group_id'],
						'op' => 'one',
						'value' => array( $subgroup['saved_subgroup_bit'] )
					)
				)
			);
			unset( $data['group'] );
			unset( $data['subgroup'] );
		}

		// Grab the list from MC to use its default values for to/from address
		$list_results = $this->api->lists->getList( array(
			'list_id' => $data['list_id']
		) );
		$list = $list_results['data'][0];

		// Compose campaign options using what's left in $data
		$campaign_options = wp_parse_args($data, array(
			'from_email' => $list['default_from_email'],
			'from_name' => $list['default_from_name'],
			'generate_text' => ( $type == 'plaintext' ) ? false : true
		));

		$html = apply_filters( 'the_content', $post->post_content );

		$campaign_content = array(
			'text' => wp_strip_all_tags($html),
			'sections' => array(
				'body' => $html,
				'header' => $post->post_title
			)
		);

		$cid = get_post_meta( $post->ID, 'mailchimp_cid', true );

		if ( empty( $cid ) ) {
			$response = $this->api->campaigns->create(
				$type,
				$campaign_options,
				$campaign_content,
				$segment_options,
				null
			);

			update_post_meta( $post->ID, 'mailchimp_web_id', $response['web_id'] );
			update_post_meta( $post->ID, 'mailchimp_cid', $response['id'] );
		} else {
			$updates = array(
				'options' => $campaign_options,
				'content' => $campaign_content,
				'segment_opts' => $segment_options
			);

			foreach ( $updates as $name => $value ) {
				$this->api->campaigns->update($cid, $name, $value);
			}
		}
	}

	public function enqueue_assets() {
		parent::enqueue_assets();

		wp_register_script(
			'mailchimp-tools-campaign-edit',
			MAILCHIMP_TOOLS_DIR_URI . '/assets/js/campaign-edit.js',
			array( 'mailchimp-tools-campaign-common' ),
			MAILCHIMP_TOOLS_VER,
			true
		);

		$screen = get_current_screen();
		if ( $screen->post_type == $this->post_type && $screen->base == 'post' ) {
			wp_enqueue_script('mailchimp-tools-campaign-edit');
		}
	}
}
