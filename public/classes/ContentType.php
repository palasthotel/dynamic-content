<?php

namespace DynamicContent;

class ContentType extends _Component {

	public function onCreate() {
		add_action( 'init', array( $this, 'register' ) );
	}

	function get_type(){
		return 'dynamic_content';
	}

	function get_slug(){
		return 'd';
	}

	// Register Custom Post Type
	function register() {

		$rewrite = array(
			'slug'                  => $this->get_slug(),
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Dynamic Content', \DynamicContent\Plugin::DOMAIN ),
			'description'           => __( 'Dynamic Page Content', \DynamicContent\Plugin::DOMAIN),
			'supports'              => array( 'title', ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'page',
//			'show_in_rest'          => true,
//			'rest_base'             => 'dynamicy_content',
//			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);
		register_post_type( $this->get_type(), $args );

	}



}
