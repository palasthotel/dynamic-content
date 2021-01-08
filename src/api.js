"use strict";

/**
 * API for ajax request with dynamic-contents
 */
(function($){

	var API = DynamicContent_API;

	var contents = API.contents;
	var content_id = API.ids.ajax_part;

	var register = {};

	API.HOOKS = {
		ALTER_URL: "alter_url",
	};

	/**
	 * hook functions
	 */
	API.hook = {
		add:function(name, fn){
			if(typeof register[name] !== typeof []) register[name] = [];
			register[name].push(fn);
		},
		// change value
		filter:function(name){
			if(typeof register[name] !== typeof []) return arguments[1];
			var additional_args = Array.prototype.slice.call(arguments, 1);
			for( var index in register[name]){
				var fn = register[name][index];
				additional_args[0] = fn.apply(fn, additional_args);
			}
			return additional_args[0];
		},
	};



	/**
	 * get post object by slug
	 * @param slug
	 * @return {null|object}
	 */
	function get_content(slug){
		for(var i = 0; i < contents.length; i++){
			if(contents[i].slug === slug) return contents[i];
		}
		return null;
	};
	API.get_content = get_content;

	/**
	 * load content
	 *
	 * use then(...), .done(...), .fail(...), .always(...)
	 *
	 * @param {object} options
	 * @return xhjr request object
	 */
	API.load = function(options){

		var _options = _.extend({
			method: 'GET',
			xhrFields: {
				withCredentials: true
			},
			cache: true,
			data:{},
			timeout: 20000,
		},options);

		var post = get_content(options.slug);
		if(post === null) return;
		return $.ajax({
			method: _options.method,
			url: API.hook.filter(API.HOOKS.ALTER_URL, post.guid, options.slug),
			crossDomain : true,
			xhrFields: _options.xhrFields,
			cache:_options.cache,
			data: _options.data,
			timeout: _options.timeout,
		});
	};


	/**
	 * load content part
	 *
	 * use then(...), .done(...), .fail(...), .always(...)
	 *
	 * @param {object} options
	 * @return xhjr request object
	 */
	function load_part(options){
		if(typeof options.success !== "function"){
			throw "no success function defined";
		}
		return API.load(options).then(function(data){
			options.success($(data).find("#"+content_id).html());
		}).fail(function(data, status, error){
			console.error(data, status, error);
			if(typeof options.error !== typeof undefined) options.error();
		});
	};
	API.load_part = load_part;


})(jQuery);
