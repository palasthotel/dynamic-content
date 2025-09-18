<?php


namespace DynamicContent;

class _Component {

    public Plugin $plugin;

	public function __construct($plugin) {
		$this->plugin = $plugin;
		$this->onCreate();
	}

	/**
	 * overwrite this method in component implementations
	 */
	public function onCreate(){

	}
}
