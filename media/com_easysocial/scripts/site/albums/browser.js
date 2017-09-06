EasySocial.module("site/albums/browser", function($){

var module = this;

EasySocial.Controller("Albums.Browser", {
	hostname: "browser",
	defaultOptions: {

		isMobile : false,

		itemRenderOptions: {},

		"{sidebar}": "[data-album-browser-sidebar]",
		"{content}": "[data-album-browser-content]",
		"{wrapper}": "[data-wrapper]",

		"{createAlbumButton}"    : "[data-album-create-button]",
		"{createAlbumButtonLink}": "[data-album-create-button] > a",

		"{listItemGroup}": "[data-album-list-item-group]",
		"{listItemRegularGroup}": "[data-album-list-item-group=regular]",
		"{listItemCoreGroup}": "[data-album-list-item-group=core]",

		// Sidebar items

		"{listItem}"     : "[data-album-list-item]",
		"{listItemLink}" : "[data-album-list-item] > a",
		"{listItemTitle}": "[data-album-list-item-title]",
		"{listItemCover}": "[data-album-list-item-cover]",
		"{listItemCount}": "[data-album-list-item-count]",

		"{albumItem}": "[data-album-item]",

		"{photoBrowser}": "[data-photo-browser]"
	}
},
function(self) { return {

	init: function() {
		// Attach existing album items as subscriber
		self.albumItem().each(function(){
			self.addSubscriber($(this).controller("EasySocial.Controller.Albums.Item"));
		});

		if (self.options.isMobile) {

			var toggleButton = $("[data-es-sidebar-toggle]");
			$(toggleButton)
				.on('click.es.sidebar.toggle', function() {
					// Locate the closest container
					var button = $(this);
					var container = button.siblings('[data-album-browser]');

					if (container.length <= 0) {
						return;
					}

					container
						.toggleClass('sidebar-open');
				});
		}
	},


	setLayout: function(layout) {

		// Don't switch layout on dialog.
		if (self.element.hasClass("layout-dialog")) return;

		self.element
			.data("layout", layout)
			.switchClass("layout-" + layout);
	},

	open: function(view) {

		var args = $.makeArray(arguments);

		self.trigger("contentload", args);

		var method = "view" + $.String.capitalize(view);
		var loader = self[method].apply(self, args.slice(1));

		loader
			.done(self.displayContent(function(){
				self.trigger("contentdisplay", args);
				return arguments;
			}))
			.fail(function(){
				self.trigger("contentfail", args);
			})
			.always(function(){
				self.trigger("contentcomplete", args);
			});

		return loader;
	},

	"{self} contentdisplay": function(el, event, view) {

		if (/album|albumform/gi.test(view)) {
			self.setLayout("album");
		}

		if (/photo/gi.test(view)) {
			self.setLayout("photo");
		}

		// remove the loading indicator
		self.wrapper().removeClass('is-loading');
	},

	displayContent: $.Enqueue(function(html){

		var scripts = [],
			content = $($.buildFragment([html], document, scripts));

		// Insert content
		self.content().html(content);

		// Remove scripts
		$(scripts).remove();
	}),

	viewAlbum: function(albumId) {

		// Remove loading indicator from any existing ones
		self.listItem().removeClass("active is-loading");

		var listItem = self.getListItem(albumId)
							.addClass("active is-loading");

		// Don't route if we're on dialog layout
		if (self.element.data("layout")!=="dialog") {
			listItem.find("> a").route();
		}

		var loader = EasySocial.ajax("site/views/albums/item", {
						"id": albumId,
						"renderOptions": self.options.itemRenderOptions
					})
					.always(function(){

						listItem.removeClass("is-loading");
					});

		return loader;
	},

	viewPhoto: function(photoId) {

		var loader =
			EasySocial.ajax("site/views/photos/item", {
					id: photoId,
					browser: 1
				})
				.fail(function(){
				})
				.always(function(){
				});

		return loader;
	},

	"{listItem} click": function(listItem, event) {

		event.preventDefault();
		event.stopPropagation();

		// Don't do anything on new album item
		if (listItem.hasClass("new")) {
			return;
		}

		var albumId = listItem.data("albumId");

		// Load album
		self.open("Album", albumId);

        // trigger sidebar toggle for responsive view.
        self.trigger('onEasySocialFilterClick');

		// Trigger sidebar toggle for mobile apps view
		$('body').trigger('afterUpdatingContents');

		if (self.options.isMobile) {
			// if this is on mobile, lets trigger the sidebar toggle from the dialog
			$('[data-es-sidebar-toggle]').trigger('click.es.sidebar.toggle');
		}
	},

	"{listItemLink} click": function(listItemLink, event) {

		// Progressive enhancement, no longer refresh the page.
		event.preventDefault();

		// Prevent item from getting into :focus state
		listItemLink.blur();
	},

	"{createAlbumButton} click": function() {

		self.open("AlbumForm");

		// Don't route if we're on dialog layout
		if (self.element.data("layout")!=="dialog") {

			self.createAlbumButtonLink().route();
		}
	},

	"{createAlbumButtonLink} click": function(el, event) {

		event.preventDefault();
	},

	"{albumItem} init.albums.item": function(el, event, albumItem) {

		self.addSubscriber(albumItem);
	},

	getListItem: function(albumId, context) {

		var listItem =
			(!albumId) ?
				self.listItem(".new") :
				self.listItem().filterBy("albumId", albumId);

		if (!context) return listItem;

		return listItem.find(self["listItem" + $.String.capitalize(context)].selector);
	},

	updateListItemCount: function(albumId, val, append) {

		var stat = self.getListItem(albumId, "count");

		// If no stat element found, stop.
		if (stat.length < 0) return;

		// Get current stat count
		var statCount;

		if (append) {
			statCount = (parseInt(stat.text()) || 0) + (parseInt(val) || 0);
		} else {
			statCount = val;
		}

		// Always stays at 0 if less than that
		if (statCount < 0) statCount = 0;

		// Update stat count
		stat.text(statCount);
	},

	"{albumItem} albumSave": function(el, event, task) {
		task.done(function(album)
		{
			// For new albums
			// Remove item link's new state
			self.getListItem()
				.attr("data-album-id", album.id)
				.removeClass("new")

			// Update item link & route url
			self.getListItem(album.id)
				.find("> a")
				.attr({
					href : album.permalink,
					title: album.title
				})
				.route();

			// For existing albums
			self.getListItem(album.id, "title")
				.html(album.title);
		});
	},

	"{albumItem} titleChange": function(el, event, title, album) {

		self.getListItem(album.id, "title")
			.html($.trim(title) || "&nbsp;");
	},

	"{albumItem} coverChange": function(el, event, photo, album) {

		self.getListItem(album.id, "cover")
			.css("backgroundImage", $.cssUrl(photo.sizes.thumbnail.url));
	},

	"{albumItem} coverRemove": function(el, event, album) {

		self.getListItem(album.id, "cover")
			.css("backgroundImage", "");
	},

	"{albumItem} photoAdd": function(el, event, photoItem, photoData, album) {

		self.updateListItemCount(album.id, 1, true);
	},

	"{albumItem} photoMove": function(el, event, task, photo) {

		task
			.done(function(){
				self.updateListItemCount(photo.album.id, -1, true);
			});
	},

	"{albumItem} photoDelete": function(el, event, task, photo) {

		task
			.done(function(){
				self.updateListItemCount(photo.album.id, -1, true);
			});
	},

	"{photoBrowser} init.photos.browser": function(el, event, photoBrowser) {

		// Attach browser to photo browser
		self.addSubscriber(photoBrowser);
	},

	"{self} contentload": function() {
		// Display the loading indication
		self.content().html('&nbsp;');
		self.wrapper().addClass('is-loading');

		// Remove any new item because there can only be one
		self.listItem(".new").remove();
	}

}});

module.resolve();

});
