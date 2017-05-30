EasySocial.module("site/search/toolbar", function($){

	var module	= this;

	EasySocial.Controller("Search.Toolbar",
	{
		defaultOptions: {
			"{textfield}": "[data-nav-search-input]"
		}
	},
	function(self, opts, base) { return {

		init : function() {
		},

		cache: {},

		search: $.debounce(function(keyword) {

			if (self.loading) {
				return;
			}

			if (!keyword || !(keyword=$.trim(keyword)) || keyword.length <= 2) {
				return;
			}

			var textfield = self.textfield();

			// Cheap fix
			textfield.popbox("widget").hide();
			textfield.popbox("widget").destroy();

			var task =

				// Take from cache if keyword has been searched before
				self.cache[keyword] ||

				// Else make and ajax call
				EasySocial.ajax("site/controllers/search/getItems", {
					"q": keyword,
					"mini": "1"
				})
				.done(function(){
					// Cache this search result
					self.cache[keyword] = task;
				});

			task
				.fail(function(message) {
					console.log(message);
				})
				.always(function(){
					self.loading = false;
				});

			self.hide();

			base.popbox({
				content: task,
				id: "fd",
				component: "es",
				type: "search",
				toggle: "click",
				cache: false,
				offset: 0
			});

			var popbox = base.popbox("widget");

			popbox.show();
			popbox.keyword = keyword;

		}, 250),

		hide: function() {

			var popbox = base.popbox("widget");

			if (popbox) {
				popbox.hide();
			}
		},

		"{textfield} keydown": function() {

			self.hide();
		},

		"{textfield} keyup": function(textfield, event) {

			// 27 == escape
			if (event.which===27) {
				return;
			}

			var keyword = textfield.val();
			self.search(keyword);
		},

		"{self} popboxLoading": function(el, event, popbox) {

			popbox.loader.width(base.width());

			popbox.loader
				.position(popbox.position);

			base.addClass("is-active");
		},

		"{self} popboxActivate": function(el, event, popbox) {

			popbox.tooltip.width(base.width());

			popbox.tooltip
				.position(popbox.position);

			base.addClass("is-active");
		},

		"{self} popboxDeactivate": function(el, event, popbox) {

			base.removeClass("is-active");
		}

	}});

	module.resolve();

});