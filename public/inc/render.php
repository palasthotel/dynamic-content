<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 30.01.17
 * Time: 14:06
 */

namespace DynamicContent;


class Render {
	
	/**
	 * Page constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct($plugin) {
		$this->plugin = $plugin;
		
		// TODO: render tigger point
		// TODO: templateable trigger point
		add_action(Plugin::ACTION_RENDER_TRIGGER, array($this, "render_trigger"));
	}
	
	/**
	 * @param array|string $slug
	 */
	function render_trigger($slug){
		
		if(is_array($slug)){
			foreach ($slug as $s){
				$this->render_trigger($s);
			}
			return;
		}
		
		$post = $this->plugin->get_content_by_slug($slug);
		if($post == null){
			include $this->plugin->dir."/templates/not-found.tpl.php";
			return;
		}
		
		include $this->get_template_path_by_slug($slug);
		
	}
	
	/**
	 * @param $slug
	 *
	 * @return false|string
	 */
	function get_template_path_by_slug($slug){
		foreach ($this->get_template_names($slug) as $template){
			$path = $this->get_template_path($template);
			if($path !== false){
				return $path;
			}
		}
		return false;
	}
	
	/**
	 * list of template names sorted by priority
	 * @param $slug
	 *
	 * @return array
	 */
	function get_template_names($slug){
		return array(
			str_replace(Plugin::TEMPLATE_TRIGGER_SLUG_PLACEHOLDER,$slug, Plugin::TEMPLATE_TRIGGER_SLUG),
			Plugin::TEMPLATE_TRIGGER,
		);
	}
	
	/**
	 * look for existing template path
	 * @return string|false
	 */
	function get_template_path( $template ) {
		if ( $overridden_template = locate_template( Plugin::THEME_FOLDER . "/" . $template ) ) {
			return $overridden_template;
		}
		$fallback = $this->plugin->dir . 'templates/' . $template;
		return (is_file($fallback))? $fallback: false;
		
	}
	
}