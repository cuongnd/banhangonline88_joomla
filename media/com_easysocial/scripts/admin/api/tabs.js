EasySocial.module('admin/api/tabs', function($) {

var module = this;


// Add active tab state
$(document)
	.on('click.tabs.active', '[data-es-form-tabs] [data-item]', function() {
		var hidden = $('[data-tab-active]');

		if (hidden.length <= 0) {
			return;
		}

		var selected = $(this).data('item');

		hidden.val(selected);

	});


module.resolve();

});
