<?php

/**
 * @return \DynamicContent\Plugin
 */
function dynamic_content_get_plugin() {
	global $dynamic_content_plugin;

	return $dynamic_content_plugin;
}

/**
 * render a single dynamic content trigger element
 *
 * @param $slug
 */
function dynamic_content_render_trigger( $slug ) {
	do_action( \DynamicContent\Plugin::ACTION_RENDER_TRIGGER, $slug );
}

/**
 * get content post by slug
 *
 * @param $slug
 *
 * @return null|\WP_Post
 */
function dynamic_content_get_page( $slug ) {
	return dynamic_content_get_plugin()->get_content_by_slug( $slug );
}

/**
 * @param $slug
 *
 * @return string
 */
function dynamic_content_get_permalink( $slug ) {
	$page = dynamic_content_get_page( $slug );
	if ( is_a( $page, 'WP_Post' ) ) {
		$parsed = parse_url( $page->guid );
		$path   = null;
		if ( isset( $parsed["path"] ) ) {
			$path = $parsed["path"];
			if ( ! empty( $parsed["query"] ) ) {
				$path .= "?" . $parsed["query"];
			}
			if ( ! empty( $parsed["fragment"] ) ) {
				$path .= "#" . $parsed["fragment"];
			}
		}
		if ( null != $path ) {
			return $path;
		}
	}

	return "__dynamic_content_permalink_not_found_for_${$slug}__";
}


/**
 * use after action wp
 *
 * @param $slug
 *
 * @return bool
 */
function dynamic_content_is_current_page( $slug ) {

	$page = dynamic_content_get_page( $slug );

	return null != $page && $page->ID == get_queried_object_id();

}