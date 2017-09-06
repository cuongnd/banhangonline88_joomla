EasySocial.module('site/search/default' , function($){

var module	= this;

EasySocial.Controller('Search.List', {
	defaultOptions: {
		"{pagination}": "[data-search-pagination]",
		"{moreButton}": "[data-more]"
	}
}, function(self, opts, base) { return {

	init : function() {

		self.on("scroll.search", window, $._.debounce(function(){

			if (self.loading) {
				return;
			}

			if (self.pagination().visible()) {

				self.loadMore();
			}

		}, 250));
	},

	"{moreButton} click": function(button, event) {
		self.loadMore();
	},

	loadMore: function() {

		var query = $("[data-search-query]").val();
		var type = $("[data-sidebar-menu].active").data( 'type' );
		var next_limit = self.pagination().data('limitstart');

		var filters = [];

		$("[data-search-filtertypes]:checked").each( function(idx, ele) {
			filters.push($(ele).val());
		});

		if (next_limit == '-1') {
			self.pagination().empty();
			return;
		}

		self.loading = true;

		self.moreButton().addClass('is-loading');

		EasySocial.ajax('site/controllers/search/getItems', {
			"next_limit": next_limit,
			"type": type,
			"q": query,
			"loadmore" : '1',
			'filtertypes' : filters
		}).done(function(contents, limitstart) {

			// update next last-update and last-type
			self.pagination().data('limitstart', limitstart);

			// append stream into list.
			self.pagination().before(contents);

			if (limitstart == '-1') {
				self.pagination().empty();
			}

		}).always(function(){

			self.loading = false;
		});
	}
}});

module.resolve();
});
