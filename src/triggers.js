'use strict';

/**
 * handle dynamic triggers
 */
(function($) {

	var API = DynamicContent_API;
	API.HOOKS.AFTER_CONTENT_INSERT = "after_content_insert";


    var classes = API.classes;
	var bp = parseInt(API.breakpoint);
	var load_part = API.load_part;

	var $triggers = $('[data-dynamic-content-slug]');

	$triggers.on('hover', load_on_hover);
	function load_on_hover(e) {

		if (!trigger_is_active()) return;

		API.load_area($(this));

	}

	API.load_area = function($element){
		var $content = $element.next('.' + classes.content);
		var slug = $element.data('dynamic-content-slug');
		var cache = ($element.data('dynamic-content-cache') !== 'no-cache');

		if (!$content.length) {
			var content_div = document.createElement('div');
			$element.after(content_div);

			_set_loading(content_div);
			_add_slug_area(slug, content_div, {cache: cache});

			API.reload_slug(slug);
		}
	};

	API._slug_areas = {};
	function _add_slug_area(slug, content_div, options) {
		if (typeof options === typeof undefined) options = {};
		var _options = {
			cache: (options.cache) ? options.cache : true,
		};
		if (typeof API._slug_areas[slug] !==
			typeof []) API._slug_areas[slug] = [];
		API._slug_areas[slug].push(content_div);
	}

	API.reload_slug = function(slug) {
		if (typeof API._slug_areas[slug] === typeof []) {
			for (var index in API._slug_areas[slug]) {
				var content_div = API._slug_areas[slug][index];
				_set_loading(content_div);
			}
			load_part({
				slug: slug,
				success: insert_all_contents.bind(API, API._slug_areas[slug],
					slug),
				error: insert_all_errors.bind(API, API._slug_areas[slug], slug),
			});
		}

	};

	function _set_loading(content_div) {
		var $content = $(content_div);
		$content.html(API.placeholders.loading);
		$content.addClass(classes.content);
		$content.parent().addClass(classes.loading);
	}

	function insert_all_contents(content_divs, slug, data) {
		for (var index in content_divs) {
			var content_div = content_divs[index];
			insert_content.call(content_div, content_div, slug, data);
		}
	}

	function insert_all_errors(content_divs, slug, data) {
		for (var index in content_divs) {
			var content_div = content_divs[index];
			insert_error.call(content_div, content_div, slug, data);
		}
	}

	/**
	 * insert content to content wrapper
	 * @param content_element
	 * @param slug
	 * @param data
	 */
	function insert_content(content_element, slug, data) {
		get_parent(this).removeClass(classes.loading);
		$(content_element).html(data);
		API.hook.filter(API.HOOKS.AFTER_CONTENT_INSERT, slug, content_element);
	}

	/**
	 * insert error to content wrapper
	 * @param content_element
	 * @param slug
	 * @param data
	 */
	function insert_error(content_element, slug, data) {
		get_parent(this).removeClass(classes.loading);
		$(content_element).html(DynamicContent_API.placeholders.error);
	}

	/**
	 * toggle active state on parent
	 */
	$triggers.on('click', toggle_trigger);
	function toggle_trigger(e) {
		if (trigger_is_active()) {
			e.preventDefault();
			get_parent(this).toggleClass(classes.active);
		}
	}

	/**
	 * get the specified parent
	 * @param trigger
	 */
	function get_parent(trigger) {
		if (classes.parent === 'parent') {
			return $(trigger).parent();
		} else {
			var selector = '.'+classes.parent;
			return $(trigger).closest(selector);
		}
	}

	/**
	 * triggers are active if window is larger than breakpoint
	 * @return {boolean}
	 */
	function trigger_is_active() {
		return bp <= window.innerWidth;
	}

	/**
	 * if click elsewhere hide opend
	 */
	$(document).on('click', function(e) {
		for (var i = 0; i < $triggers.length; i++) {
			var trigger = $triggers[i];
			if (!trigger.parentNode.contains(e.target)) {
				get_parent(trigger).removeClass(classes.active);
			}
		}
	});

})(jQuery);
