<?php
/**
 * MailChimp Campaign preview meta box
 *
 * @package MailChimp Tools
 * @since 0.0.1
 */
class CampaignPreview extends MCMetaBox {

	public $label = 'MailChimp Campaign Preview';

	public $id = 'mailchimp-campaign-preview';

	public $location = 'side';

	public function __construct($post_type='post') {
		parent::__construct($post_type);
		add_action( 'wp_loaded', array( $this, 'bless_query_var' ) );
		add_action( 'template_redirect', array( $this, 'render_preview_page' ) );
		add_filter( 'preview_post_link', array( $this, 'preview_link' ) );
	}

	public function preview_link($link) {
		$existing = mailchimp_tools_get_existing_campaign_data_for_post( get_post( $_GET['post'] ), false );
		if ( ! $existing ) {
			return $link;
		} else {
			global $post;
			return get_bloginfo('url'). '?campaign_preview=true&post_id=' . esc_attr( $post->ID );
		}
	}

	public function add_meta_box() {
		if ( strstr( $_SERVER['REQUEST_URI'], 'post-new.php' ) ) {
			return false;
		}

		$existing = mailchimp_tools_get_existing_campaign_data_for_post( get_post( $_GET['post'] ) );
		if ( ! $existing ) {
			return false;
		}

		parent::add_meta_box();
	}

	public function render_meta_box() {
		$post = get_post();
		$settings = get_option( 'mailchimp_settings' );
		$web_id = get_post_meta( $post->ID, 'mailchimp_web_id', true );
		$mc_api_key_parts = explode( '-', $settings['mailchimp_api_key'] );
		$mc_api_endpoint = $mc_api_key_parts[1];
		$context = array(
			'post' => $post,
			'mc_api_endpoint' => $mc_api_endpoint,
			'web_id' => $web_id,
		);
		mailchimp_tools_render_template( 'campaign-preview.php', $context );
	}

	/*
	 * The endpoint for iframe that displays the campaign preview
	 */
	public function render_preview_page() {
		global $wp_query, $post;

		if ( ! isset( $wp_query->query_vars['campaign_preview'] ) ) {
			return false;
		}

		if ( ! isset( $_GET['post_id'] ) ) {
			return false;
		}

		$post = get_post( $_GET['post_id'] );
		setup_postdata( $post );

		$existing = mailchimp_tools_get_existing_campaign_data_for_post( $post );
		$html = apply_filters( 'the_content', $post->post_content );

		if ( $existing['type'] == 'plaintext' ) {
			echo wp_strip_all_tags($post->post_content);
		} else if ( $existing['type'] == 'regular' ) {
			$template_source = mailchimp_tools_get_template_source( $existing['template_id'] );
			$doc = new DOMDocument();
			@$doc->loadHTML( '<?xml encoding="UTF-8">' . $template_source );

			foreach ( $doc->getElementsByTagName('*') as $element ) {
				if ( $element->getAttribute( 'mc:edit' ) == 'body' ) {
					$fragment = new DOMDocument();
					@$fragment->loadHTML( '<?xml encoding="UTF-8">' . $html );
					$item = $fragment->getElementsByTagName('body')->item(0);

					while ( $element->childNodes->length ){
						$element->removeChild($element->firstChild);
					}

					$element->appendChild( $doc->importNode( $item, true ) );
					break;
				}
			}
			echo $doc->saveHTML();
		}

		wp_reset_postdata();
		die();
	}

	public function bless_query_var() {
		add_rewrite_endpoint('campaign_preview', EP_ALL);
	}

}
