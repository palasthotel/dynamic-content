<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 30.01.17
 * Time: 14:06
 */

namespace DynamicContent;


class ContentGenerator {

	const OPTION_LOCK = "_dynamic_contents_generating";
	
	/**
	 * Page constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct($plugin) {
		$this->plugin = $plugin;
		add_action("init", array($this, "save_pages_if_needed"));
	}
	
	function save_pages_if_needed(){
		
		$pages = array();
		$pages = apply_filters(Plugin::FILTER_PAGES_CONFIG, $pages);
		if( !is_array($pages) || count($pages) < 1) return;
		
		$post_type = $this->plugin->content_type->get_type();
		$post_slug = $this->plugin->content_type->get_slug();
		
		$results = $this->plugin->get_contents();
		
		$rewrite = false;
		foreach ($pages as $slug => $page){
			
			$found = false;
			foreach ($results as $result){
				$regex = "%(.*)\/{$post_slug}\/{$slug}\/$%";
				if( preg_match($regex,$result->guid ) && $result->post_title == $page["title"] ){
					$found = true;
					break;
				}
			}
			if(!$found){
				$rewrite = true;
				break;
			}
			
		}
		
		/**
		 * no rewrite needed skip
		 */
		if(!$rewrite) return;
		if( get_transient(self::OPTION_LOCK) == "yes" ) return;

		set_transient(self::OPTION_LOCK, "yes", 15);
		
		/**
		 * delete all pages
		 */
		$posts = get_posts(array(
			"post_type" => $post_type,
			"posts_per_page" => -1,
		));
		foreach ($posts as $result){
			wp_delete_post($result->ID, true);
		}
		
		/**
		 * insert pages
		 */
		foreach ($pages as $slug => $page){
			
			wp_insert_post(array(
				"post_title" => $page["title"],
				"post_name" => $slug,
				"post_type" => $post_type,
				'post_status' => 'publish',
			));
			
		}
		
		$this->plugin->reset_contents_cache();

		delete_transient(self::OPTION_LOCK );
		
	}
	
}