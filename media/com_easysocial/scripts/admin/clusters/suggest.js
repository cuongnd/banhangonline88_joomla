EasySocial.module('admin/clusters/suggest', function($) {

var module = this;

EasySocial.require()
.library('textboxlist')
.done(function($) {

EasySocial.Controller('Clusters.Suggest', {
	defaultOptions: {
		max: null,
		exclusive: true,
		exclusion: [],
		minLength: 1,
		highlight: true,
		name: "uid[]",
		type: "",
		clusterType: ""
	}
}, function(self, opts, base) { return {
	init: function() {

		// Implement the textboxlist on the current element.
		self.element
			.textboxlist({
				"component": 'es',
				"name": opts.name,
				"max": opts.max,
				"plugin": {
					autocomplete: {
						exclusive: opts.exclusive,
						minLength: opts.minLength,
						highlight: opts.highlight,
						showLoadingHint: false,
						showEmptyHint: false,

						query: function(keyword) {

							// Run an ajax call to retrieve suggested groups
							var result = EasySocial.ajax('site/controllers/' +opts.clusterType+ '/suggest', {
											"search": keyword,
											"exclusion": opts.exclusion
										});

							return result;
						}
					}
				}
			})
			.textboxlist("enable");
	},

	"{self} filterItem": function(el, event, item) {

		var html = $('<div/>').html(item.html);
		var title = html.find('[data-suggest-title]').text();
		var id = html.find('[data-suggest-id]').val();

		item.id = id;
		item.title = title;
		item.menuHtml = item.html;
	},

	"{self} filterMenu": function(el, event, menu, menuItems, autocomplete, textboxlist) {
        // Get list of items that are already added into the bucket
        var selected = textboxlist.getAddedItems();
        var selected = $.pluck(selected, "id");

        // Add the items into the 
        var exclude = selected.concat(opts.exclusion);

        menuItems.each(function(){

            var menuItem = $(this);
            var item = menuItem.data("item");

            var isSelected = $.inArray(item.id.toString(), exclude) > -1;
            menuItem.toggleClass('hidden', isSelected);
            
        });				
	}
}});
	
module.resolve();
});

});