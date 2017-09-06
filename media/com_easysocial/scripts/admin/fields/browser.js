EasySocial.module('admin/fields/browser', function($) {

var module = this;

/* Browser Controller */
EasySocial.Controller('Fields.Browser', {
	defaultOptions: {
		'{browser}'		: '[data-fields-browser]',
		'{mandatory}'	: '[data-fields-browser-group-mandatory]',
		'{unique}'		: '[data-fields-browser-group-unique]',
		'{standard}'	: '[data-fields-browser-group-standard]',
		'{list}'		: '[data-fields-browser-list]',
		'{item}'		: '[data-fields-browser-item]',
		'affixClass'	: 'es-browser-affix'
	}
}, function(self, opts, base) { return {
	state: $.Deferred(),

	init: function() {
		self.registerApps();

		self.ready();

		self.affixHandler();

		self.initAffix();
	},

	ready: function() {
		self.state.resolve();
	},

	'{parent} controllersReady': function() {
		var id = $Steps.getCurrentStep().data('id');

		self.initDraggable(id);
	},

	'{parent} pageChanged': function(el, ev, page, uid) {
		self.item().draggable('destroy');

		self.initDraggable(uid);
	},

	'{parent} pageAdded': function(el, ev, page, uid) {
		self.item().draggable('destroy');

		self.initDraggable(uid);
	},

	initDraggable: function(id) {
		self.item().draggable({
			revert: 'invalid',
			helper: 'clone',
			connectToSortable: '[data-fields-editor-page-items-' + id + ']'
		});
	},

	affixHandler: function() {
		var parent = $(window),
			wrap = self.parent.wrap(),
			height = wrap.offset().top,
			scroll = parent.scrollTop();

		if(scroll > height && !self.browser().hasClass(self.options.affixClass)) {
			self.browser().addClass(self.options.affixClass);
		}

		if(scroll <= height && self.browser().hasClass(self.options.affixClass)) {
			self.browser().removeClass(self.options.affixClass);
		}
	},

	initAffix: function() {
		$(window).scroll(self.affixHandler);
	},

	registerApps: function() {
		// Register all available apps into an object
		$.each(self.item(), function(index, item) {
			item = $(item);

			var id = item.data('id');

			$Apps[id] = {
				id: id,
				element: item.data('element'),
				title: item.data('title'),
				params: item.data('params'),
				core: item.data('core'),
				unique: item.data('unique'),
				item: item
			};

			// Keep a list of core apps id in $Core
			if(item.data('core')) {
				$Core[id] = $Apps[id];
			}
		});
	},

	checkout: function(id) {
		if($Check[id] !== undefined) {
			delete $Check[id];
		}
	},

	'{item} click': function(el) {
		// Get the current page.
		var currentPage = $Editor.currentPage();

		// Get the app id of the item clicked
		var appId = el.data('id');

		// Add new item to the page
		currentPage.addNewField(appId);
	},

	/**
	 * Carry out any necessary actions when app is added as a field
	 */
	'{parent} fieldAdded': function(el, event, appid) {
		var app = $Apps[appid];

		if(app && app.core) {
			app.item.addClass('t-hidden');
			app.item.removeClass('t-inline-block');

			// If core app is added, check if there are any remaining core app left to hide the core group
			var items = self.mandatory().find(self.item.selector).filter(':visible');

			self.mandatory().toggle((items.length > 0));
		}

		if(app && app.unique) {
			app.item.hide();

			// If unique app is added, check if there are any remaining unique app left to hide the unique group
			var items = self.unique().find(self.item.selector).filter(':visible');

			self.unique().toggle((items.length > 0));
		}
	},

	/**
	 * Carry out any necessary actions when field is removed
	 */
	'{parent} fieldDeleted': function(el, event, appid, fieldid) {
		var app = $Apps[appid];

		if(app && app.core) {
			app.item.removeClass('t-hidden');
			app.item.addClass('t-inline-block');

			// If core app is deleted, then the browser group for core fields have to definitely show
			self.mandatory().show();

			return;
		}

		if(app && app.unique) {
			app.item.show();

			app.item.removeClass('t-hidden');
			app.item.addClass('t-inline-block');

			// If unique app is deleted, then the browser group for unique fields have to definitely show
			self.unique().show();

			return;
		}
	}
}});

module.resolve();
});
