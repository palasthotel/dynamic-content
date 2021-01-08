<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 30.01.17
 * Time: 14:06
 */

namespace DynamicContent;


use WP_Post;

class Assets extends _Component {


	public function onCreate() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * register scripts
	 */
	function init() {

		// ---------------------------------------------
		// api scripts
		// ---------------------------------------------
		wp_register_script(
			Plugin::HANDLE_JS_API,
			$this->plugin->url . '/dist/api.js',
			array( 'jquery', 'underscore' ),
			filemtime( $this->plugin->path . '/dist/api.js' ),
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
			 * @var WP_Post $post
			 */
			$settings['contents'][] = array(
				"post_title" => get_the_title( $post->ID ),
				"guid"       => get_the_permalink( $post->ID ),
				"slug"       => $post->post_name,
			);
		}
		wp_localize_script( Plugin::HANDLE_JS_API, 'DynamicContent_API', $settings );

		// ---------------------------------------------
		// triggers script
		// ---------------------------------------------
		wp_register_script(
			Plugin::HANDLE_JS_TRIGGERS,
			$this->plugin->url . '/dist/triggers.js',
			[Plugin::HANDLE_JS_API],
			filemtime( $this->plugin->path . '/dist/triggers.js' ),
			true
		);
	}

}
