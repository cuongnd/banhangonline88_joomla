// module: start
EasyBlog.module("dashboard", function($){

var module = this;

EasyBlog.Controller(
	"Dashboard",
	{
		defaultOptions: function() {

		}
	},
	function(self) { return {

		init: function() {

			EasyBlog.dashboard = self;
		},

		registerPlugin: function(pluginName, instance) {

			if (self[pluginName]===undefined) {

				self[pluginName] = instance;
			}
		}
	}}
);

module.resolve();

});
