<?php

namespace DynamicContent;

/*
Plugin Name: Dynamic Content
Plugin URI: https://palasthotel.de
Description: Get rendered
Version: 1.0
Author: Palasthotel ( in Person: Edward Bock, Stephan Kroppenstedt)
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class Plugin {

	const DOMAIN = "dynamic-content";

	const THEME_FOLDER = "dynamic-content";

	const TEMPLATE_TRIGGER = "trigger.tpl.php";
	const TEMPLATE_TRIGGER_SLUG = "trigger-%slug%.tpl.php";
	const TEMPLATE_TRIGGER_SLUG_PLACEHOLDER = "%slug%";

	const FILTER_PAGES_CONFIG = "dynamic_pages_config";
	const FILTER_ENQUEUE_SCRIPTS = "dynamic_content_enqueue_scripts";
	const FILTER_JS_SETTINGS = "dynamic_content_js_settings";

	const ACTION_RENDER_TRIGGER = "dynamic_pages_render_trigger";
	const ACTION_ENQUEUE_SCIRPTS = "dynamic_pages_enqueue_scripts";

	const HANDLE_JS_API = "dynamic-content-api";
	const HANDLE_JS_TRIGGERS = "dynamic-content-triggers";

	/**
	 * @var array $contents cache
	 */
	private $contents;

	/**
	 * Plugin constructor.
	 */
	public function __construct() {

		/**
		 * base paths
		 */
		$this->dir = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );

		/**
		 * reset contents cache
		 */
		$this->reset_contents_cache();

		/**
		 * post type
		 */
		require_once dirname( __FILE__ ) . "/inc/content-type.php";
		$this->content_type = new \ContentType( $this );

		/**
		 * content generator
		 */
		require_once dirname( __FILE__ ) . "/inc/content-generator.php";
		$this->content_generator = new ContentGenerator( $this );

		/**
		 * JS api
		 */
		require_once dirname( __FILE__ ) . "/inc/js.php";
		$this->js = new JS( $this );

		/**
		 * render
		 */
		require_once dirname( __FILE__ ) . "/inc/render.php";
		$this->render = new Render( $this );

		/**
		 * on activate or deactivate plugin
		 */
		register_activation_hook( __FILE__, array( $this, "activation" ) );
		register_deactivation_hook( __FILE__, array( $this, "deactivation" ) );

	}

	/**
	 * get all dynamic content pages
	 */
	function get_contents() {
		if ( $this->contents == null ) {
			$this->contents = get_posts( array(
				"post_type"      => $this->content_type->get_type(),
				"posts_per_page" => - 1,
			) );
//			$this->contents = $wpdb->get_results("SELECT ID, guid, post_title FROM {$wpdb->prefix}posts WHERE post_type = '{$this->content_type->get_type()}'");
		}

		return $this->contents;
	}

	/**
	 * @param $slug
	 *
	 * @return null|\WP_Post
	 */
	function get_content_by_slug( $slug ) {
		foreach ( $this->get_contents() as $post ) {

			/**
			 * @var \WP_Post $post
			 */
			if ( $post->post_name == $slug ) {
				return $post;
			}
		}

		return null;
	}

	/**
	 * reset dynamic contents cache
	 */
	function reset_contents_cache() {
		$this->contents = null;
	}

	/**
	 * on plugin activation
	 */
	function activation() {
		$this->content_type->register();
		flush_rewrite_rules();
	}

	/**
	 * on plugin deactivation
	 */
	function deactivation() {
		flush_rewrite_rules();
	}

}

global $dynamic_content_plugin;
$dynamic_content_plugin = new Plugin();

require_once dirname( __FILE__ ) . "/public-functions.php";