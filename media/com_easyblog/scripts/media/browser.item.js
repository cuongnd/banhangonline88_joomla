// module: start
EasyBlog.module("media/browser.item", function($) {

var module = this;

EasyBlog.Controller("Media.Browser.Item",

	{
		defaultOptions: {

			"{itemTitle}": ".itemTitle",
			"{itemIcon}": ".itemIcon",
			"{childItem}": ".browserItem",

			hasCustomHandler: ["folder"]
		}
	},

	// Instance properties
	function(self) {

		var $Media, $Library, $Browser;

		return {

			init: function() {

				$Media = self.media;
				$Library = $Media.library;
				$Browser = $Media.browser;

				// Store a reference to the controller inside item data
				// and also add a item-type class.
				self.element
					.data("item", self)
					.addClass("item-type-" + self.meta().type);

				// Bind to the remove event
				self.meta().data.on("removed", function(){
					$Browser.removeItem(self);
				});

				// Create item handler
				self.createHandler();
			},

			meta: function() {
				return $Library.getMeta(self.key);
			},

			place: function() {
				return $Library.getPlace(self.key);
			},

			parentFolder: function() {
				return $Browser.getItem($Library.getParentKey(self.key));
			},

			createHandler: function() {

				if ($.inArray(self.meta().type, self.options.hasCustomHandler) < 0) return;

				var ItemHandler = EasyBlog.Controller.Media.Browser.Item[$.String.capitalize(self.meta().type)];

				if (ItemHandler===undefined) {

					EasyBlog.require()
						.script("media/browser.item." + self.meta().type)
						.done(function() {
							self.createHandler();
						});

					return;
				}

				self.handler = new ItemHandler(
					self.element,
					{
						controller: {
							media: self.media,
							item: self
						}
					}
				);
			},

			activate: function() {

				self.setLayout();

				if (self.handler) {
					self.handler.activate();
				}
			},

			remove: function() {

				try {

					if (self.element) {
						self.element.detach().remove();
					}

					// Destroy handler
					// TODO: This fails because this.element.data("controllers") is undefined.
					if (self.handler) {
						if (!self.handler._destroyed) {
							self.handler.destroy();
						}
					}

				} catch(e) {
				}
			},

			isVisible: function() {

				// TODO: Optimize routines using seed
				var itemElement = self.element,
					itemHeight  = itemElement.outerHeight(),
					itemTop     = itemElement.offset().top,
					itemBottom  = itemTop + itemHeight,
					itemField       = self.media.browser.itemField(),
					itemFieldTop    = itemField.offset().top,
					itemFieldBottom = itemFieldTop + itemField.height();

					isVisible = !((itemTop < itemFieldTop    && itemBottom < itemFieldTop) ||
						          (itemTop > itemFieldBottom && itemBottom > itemFieldBottom));

					// #debug:start
					if (self.media.options.debug.itemVisiblity) {

						self.media.console("info", [
							"Item visibility",
							{
								title: self.meta().title,
								isVisible: isVisible,
								item: self,
								itemHeight: itemHeight,
								itemTop: itemTop,
								itemBottom: itemBottom,
								itemFieldTop: itemFieldTop,
								itemFieldBottom: itemFieldBottom
							}
						]);
					}
					// #debug:end

				return isVisible;
			},

			setLayout: function(animate) {

				// Nothing to be done for folders
				if (self.meta().type=="folder") return;

				// Call handler's setLayout if exists
				if (self.handler && $.isFunction(self.handler.setLayout)) {

					return self.handler.setLayout();
				}

				self.setIcon();
			},

			setIcon: function() {

				// If icon is loading, skip.
				if (self.setIcon.loading || self.setIcon.loaded) return;

				// If no icon given or item has been destroyed, skip.
				if (self.meta().icon===undefined || self._destroyed) return;

				self.setIcon.loading = true;

				$Browser.iconThread.addDeferred(function(thread) {

					var itemIcon = self.itemIcon();

					// Save on calculating this
					// if we rely on a set layout seed
					if (!self.isVisible()) {

						self.setIcon.loading = false;

						thread.reject();

					} else {

						var meta = self.meta(),
							place = self.place(),
							iconUrl = meta.icon.url;

						if (!self.setIcon.useNaturalUrl &&
							!/easysocial|jomsocial|flickr/.test(place.id) &&
							meta.type==="image") {

							iconUrl = EasyBlog.baseUrl
							          + "&view=media&layout=getIconImage"
							          + "&place=" + encodeURIComponent(place.id)
							          + "&path="  + encodeURIComponent(self.meta().path)
							          + "&format=image&tmpl=component";
						}

						self.element.addClass("loading-icon");

						itemIcon
							.image("get", iconUrl)
							.done(function(){

								self.element.removeClass("loading-icon");

								self.setIcon.loaded = true;
								self.setIcon.loading = false;

								thread.resolve();
							})
							.fail(function(){

								self.element.removeClass("loading-icon");

								self.setIcon.loaded = false;
								self.setIcon.loading = false;

								thread.reject();

								if (!self.setIcon.triedNaturalUrl) {
									self.setIcon.useNaturalUrl = true;
									self.setIcon.triedNaturalUrl = true;
								}
							});
					}
				});
			}

		}
	}

);

EasyBlog.Controller("Media.Browser.Item.Folder",

	{
		defaultOptions: {
			"{childItem}": ".browserItem"
		}
	},

	// Instance properties
	function(self) {

		var $Media, $Library, $Browser;

		return {

			init: function() {

				$Media = self.media;
				$Library = $Media.library;
				$Browser = $Media.browser;

				self.element.empty();
			},

			items: {},

			// This is to make sure parent class's setLayout isn't called.
			setLayout: function() {

				var place = self.item.place(),
					status;

				switch (place.ready.state()) {

					case "pending":
						status = "loading";
						break;

					case "rejected":
						status = "error";
						break;

					case "resolved":

						if (self.folderView && self.folderView.map.length > 0) {

							status = "ready";
							$Browser.browserPagination().show();

						} else {

							switch (place.populate.task.state()) {

								case "pending":
									status = "loading";
									break;

								case "rejected":
									status = "error";
									break;

								case "resolved":

									if (self.folderView && self.folderView.map.length < 1) {

										status = place.acl.canUploadItem ? "empty canUpload" : "empty";

										if (self.item.meta().place==="jomsocial" && self.item.meta().path===$Media.options.directorySeparator) {
											status = "selectAlbum";
										}

										if (self.item.meta().place==="easysocial" && self.item.meta().path===$Media.options.directorySeparator) {
											status = "selectAlbum";
										}

									} else {

										status = "ready";
									}

									$Browser.browserPagination().toggle(!/empty|selectAlbum/.test(status));
									break;
							}
						}
						break;
				}

				$Browser.currentFolderStatus(status);

				$Browser.setItemLayout();

				if(self.isChangingPage) {
					self.isChangingPage = false;
				} else {
					self.populatePages();
				}
			},

			populate: function(files) {

				if ($Browser.itemField().hasClass("searching")) return;

				var i, l = files.length;

				var _items = self.items;
					self.items = {};

				// If there is nothing to show
				if (l<1) {

					// Detach everything
					try {
						// Wrapped in try catch because deleted items by may be involved.
						self.childItem().detach();
					} catch(e) {

					}

					// TODO: If this at an imposible range,
					// revert to the last possible range.
					// return;

				} else {

					for (i=0; i<l; i++) {

						var key = files[i];

						// This either return the newly created file,
						// or the file that has been previously created.
						var file = $Browser.createFile(key);

						file.element
							.appendTo(self.element);

						self.items[key] = file;
						delete _items[key];
					}

					for (key in _items) {

						// TODO: Check if this will result in error for removed item
						// Then remove the try catch
						try {
							_items[key].element.detach();
						} catch(e) {

						}
					}
				}

				// If we are populating in the background,
				// we don't need to set the item layout yet.
				if (self.item.place().itemGroup.hasClass("active") &&
					self.element.hasClass("focus")) {

					self.setLayout();
				}
			},

			activate: function() {

				if (!self.folderView) {

					self.folderView =
						self.item.meta().data.views
							.create({from: 0, to: $Browser.options.layout.maxIconPerPage});

					self.folderView
						.updated(self.populate);
				}

				self.setLayout();
			},

			populatePages: function() {

				var page = self.totalPage();

				if(page < 2) {
					$Browser.browserPagination().hide();
				} else {
					$Browser.browserPagination().show();

					$Browser.pageSelection().html('');

					for(var i = 1; i <= page; i++) {
						$Browser.view.paginationPage({
							page: i
						}).appendTo($Browser.pageSelection());
					}

					self.folderView.currentPage = self.folderView.currentPage || 1;

					$Browser.paginationPage().removeClass('selected');
					$Browser.paginationPage('.page' + self.folderView.currentPage).addClass('selected');
				}
			},

			totalPage: function() {

				var totalItems = self.folderView.map.length,
					basePage = totalItems % $Browser.options.layout.maxIconPerPage,
					page = Math.floor((basePage > 0) ? totalItems / $Browser.options.layout.maxIconPerPage + 1 : totalItems / $Browser.options.layout.maxIconPerPage);

				if($Browser.totalPage().text() != page) {
					$Browser.totalPage().text(page);
				}

				return page;
			},

			// both getter and setter
			currentPage: function(page, callback) {

				var current = parseInt(self.folderView.currentPage);

				if(isNaN(current)) {
					current = 1;

					self.folderView.currentPage = 1;

					$Browser.paginationPage().removeClass('selected');
					$Browser.paginationPage(':first').addClass('selected');
				}

				if(page===undefined) {
					page = {
						from: self.folderView.from
					};
				}

				if($.isPlainObject(page) && page.from !== undefined) {
					var totalItems = self.folderView.map.length,
						currentPage = Math.floor(page.from / $Browser.options.layout.maxIconPerPage) + 1;
				}

				page = currentPage || page;

				if(page != current) {

					var from = (page - 1) * $Browser.options.layout.maxIconPerPage,
						to = from + $Browser.options.layout.maxIconPerPage;

					if(from != page.from) {
						self.folderView.select({from: from, to: to});
					}

					self.folderView.currentPage = page;

					$Browser.paginationPage().removeClass('selected');
					$Browser.paginationPage('.page' + page).addClass('selected');

					$Browser.trigger('pageChanged', [current, page]);
				}

				callback && callback();

				return page;
			},

			next: function() {
				self.changePage('next');
			},

			prev: function() {
				self.changePage('prev');
			},

			changePage: function(type) {
				self.isChangingPage = true;

				var totalPage = self.totalPage(),
					currentPage = self.currentPage();

				if(type == 'next' && currentPage < totalPage) {
					currentPage += 1;
				}

				if(type == 'prev' && currentPage > 1) {
					currentPage -= 1;
				}

				self.currentPage(currentPage);
			},

			locateItem: function(meta) {

				var meta = $Library.getMeta(meta),
					page = self.getItemPage(meta);

				if (page) {

					self.currentPage(page, function(){
						$Browser.focusItem(meta.key, true);
					});
				}
			},

			getItemPage: function(meta) {

				var meta = $Library.getMeta(meta),
					key = meta.key,
					mapLength = self.folderView.map.length,
					matchedIndex;

				$.each(self.folderView.map, function(i, value) {
					if(value == key) {
						matchedIndex = i;
						return false;
					}
				});

				return (matchedIndex !== undefined) ? Math.floor(matchedIndex / $Browser.options.layout.maxIconPerPage) + 1 : false;
			}
		}
	}

);

module.resolve();

});
// module: end
