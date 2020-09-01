<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 30.01.17
 * Time: 14:06
 */

namespace DynamicContent;


class JS {
	
	/**
	 * Page constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct( $plugin ) {
		$this->plugin = $plugin;
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_api_scripts' ) );
	}
	
	/**
	 * only the core scripts for api usage
	 */
	function enqueue_api_scripts() {
		wp_enqueue_script(
			Plugin::HANDLE_JS_API,
			$this->plugin->url . 'js/api.js',
			array( 'jquery', 'underscore' ),
			filemtime( $this->plugin->dir. '/js/api.js' ),
			TRUE
		);
		
		$settings = array(
			'breakpoint'   => 767,
			'placeholders' => array(
				"loading" => '<p>' . __( 'Loading...', Plugin::DOMAIN ) . '</p>',
				"error" => '<p>Something went wrong. Please check your internet connection.</p>',
			),
			'classes'      => array(
				'parent'  => 'parent',
				'content' => 'dynamic-content__content',
				'active'  => 'is-active',
				'loading' => 'is-loading',
			),
			'ids'          => array(
				'ajax_part' => 'dynamic-content__ajax-part',
			),
		);
		
		$settings             = apply_filters( Plugin::FILTER_JS_SETTINGS, $settings );
		$settings['contents'] = array();
		foreach ( $this->plugin->get_contents() as $post ) {
			/**
			 * @var \WP_Post $post
			 */
			$settings['contents'][] = array(
				"post_title" => get_the_title( $post->ID ),
				"guid"       => get_the_permalink( $post->ID ),
				"slug"       => $post->post_name,
			);
		}
		wp_localize_script( Plugin::HANDLE_JS_API, 'DynamicContent_API', $settings );
		
		/**
		 * modify default scripts?
		 */
		$scripts   = array();
		$scripts[] = array(
			'path'         => $this->plugin->url . 'js/triggers.js',
			'handle'       => Plugin::HANDLE_JS_TRIGGERS,
			'dependencies' => array( Plugin::HANDLE_JS_API ),
			'version'      => filemtime( $this->plugin->dir. '/js/triggers.js' ),
		);
		$scripts   = apply_filters( Plugin::FILTER_ENQUEUE_SCRIPTS, $scripts );
		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script['handle'], $script['path'], $script['dependencies'], $script['version'], TRUE );
		}
		
		/**
		 * add own scripts
		 */
		do_action( Plugin::ACTION_ENQUEUE_SCIRPTS, Plugin::HANDLE_JS_API );
	}
	
}