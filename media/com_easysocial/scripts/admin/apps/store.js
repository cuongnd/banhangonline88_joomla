EasySocial.module('admin/apps/store', function($) {

var module = this;

EasySocial.Controller('Apps.Store', {
	"defaultOptions": {
		"{item}": "[data-app-item]",
		"{install}": "[data-app-install]"
	}
}, function(self, opts, base) { return {

	init: function() {
	},

	getItem: function(element) {
		return element.closest(self.item.selector);
	},

	"{install} click": function(button, event) {
		var item = self.getItem(button);
		var payment = button.data('payment') == 1;
		var external = button.data('app-install') == "external";

		EasySocial.dialog({
			"content": EasySocial.ajax('admin/views/store/confirmation', {
				"id": item.data('id')
			})
		});
	}
}});

module.resolve();

});