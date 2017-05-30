EasyBlog.module("tag", function($){

var module = this;

EasyBlog.require()
	.view("dashboard/dashboard.tags.item")
	.done(function(){

		EasyBlog.Controller(

			"Tag.Form",
			{
				defaultOptions: {

					tags: [],
					tagLimit: 0,
					tagSelections: [],
					tagSelectionLimit: 25,

					"{tagList}"             : ".tag-list.creation",
					"{tagItems}"            : ".tag-list.creation .tag-item",
					"{tagItemRemoveButton}" : ".remove-tag",

					"{tagCreationForm}"		: ".new-tag-item",
					"{tagInput}"            : ".tagInput",
					"{tagCreateButton}"     : ".tag-create",
					"{totalTags}"			: ".total-tags",

					"{tagSelectionFilter}"  : ".tag-selection-filter",
					"{tagSelectionList}"    : ".tag-list.selection",
					"{tagSelectionItems}"   : ".tag-list.selection .tag-item",
					"{tagSelection}"        : ".tag-selection",

					"{showAllTagsButton}"   : ".show-all-tags",

					view: {
						tagItem: "dashboard/dashboard.tags.item"
					}
				}
			},

			function(self) { return {

				init: function() {

					if (EasyBlog.dashboard) {
						EasyBlog.dashboard.registerPlugin("tags", self);
					}

					// Fork this into an asynchronous process
					// in case of large dataset
					setTimeout(function(){

						// Populate tag selections
						var tags = self.options.tagSelections,
							i, l = tags.length;

						for (i=0; i<l; i++) {
							self.tags[tags[i].title.toLowerCase()] = tags[i];
						}

						// Populate selected tags if any
						var tags = self.options.tags,
							l = tags.length;

						for (i=0; i<l; i++) {

							var key = self.getKey(tags[i].title);

							self.selectTag(key);
						}

						// Generate tag selections
						self.showTagSelections("");

					}, 0);
				},

				// Tag data
				tags: {},

				// Tag elements
				items: {},

				// Selected tags
				selected: {},

				sanitizeTitle: function(title) {

					return $.trim(title).replace(/[,\'\"\#\<\>]/gi,"");
				},

				getKey: function(title) {

					return self.sanitizeTitle(title).toLowerCase();
				},

				getTag: function(key) {

					// This is because of key conflicts with native object methods
					// like "watch" or "hasOwnProperty" since tags can be anything.
					return Object.prototype.hasOwnProperty.call(self.tags, key) ? self.tags[key] : undefined;
				},

				createTag: function(title) {

					var title = self.sanitizeTitle(title),
						key = title.toLowerCase();

					return self.getTag(key) || (self.tags[key] = {title: title});
				},

				getTagItem: function(key) {

					var tag = self.getTag(key);

					if (!tag) return;

					return tag.item || (tag.item = self.view.tagItem({title: tag.title}).data("key", key));
				},

				getTagData: function(key) {

					var tag = self.getTag(key);

					if (!tag) return;

					return tag.data || (tag.data = $('<input class="tagdata" type="hidden" name="tags[]" value="' + tag.title + '" />'));
				},

				search: function(keyword) {

					var keyword = $.trim(keyword).toLowerCase(),
						results = [];

					for (key in self.tags) {

						if (key.indexOf(keyword) < 0) continue;

						results.push(key);
					}

					return results;
				},

				selectTag: function(key) {

					clearTimeout(self.selectTag.refreshTagSelection);

					var tagItem = self.getTagItem(key);

					if (!tagItem) return;

					var tagItems = self.tagItems();

					if (self.options.tagLimit > 0 && tagItems.length >= self.options.tagLimit) return;

					tagItem.css({opacity: 0});

					// When no item is selected
					if (tagItems.length < 1) {

						self.tagList()
							.prepend(tagItem);

					// When there are selected items
					} else {

						var lastTagItem = tagItems.filter(":last");

						// Don't move tag if it's already the last one.
						if (lastTagItem[0]!=tagItem[0]) {

							tagItem.insertAfter(lastTagItem);
						}
					}

					tagItem.animate({opacity: 1});

					// Attach tag data
					var tagData = self.getTagData(key);

					tagData.appendTo(self.element);

					self.selected[key] = true;

					self.checkTagLimit();

					self.selectTag.refreshTagSelection = setTimeout(function(){

						// Refresh tag selection
						self.showTagSelections();

					}, 500);
				},

				unselectTag: function(key) {

					var tagItem = self.getTagItem(key);

					if (!tagItem) return;

					// Detach tag item
					tagItem.detach();

					// Detach tag data
					var tagData = self.getTagData(key);

					tagData.detach();

					delete self.selected[key];

					self.checkTagLimit();

					var tag = self.getTag(key);

					if (tag.alias!==undefined) {

						// Refresh tag selection
						self.showTagSelections();
					}
				},

				addToTagSelectionList: function(key) {

					var tagItem = self.getTagItem(key);

					return tagItem && tagItem.appendTo(self.tagSelectionList());
				},

				showTagSelections: function(filter) {

					// Detach everything
					self.tagSelectionItems().detach();

					filter = self.currentFilter =
						(filter===undefined) ? self.currentFilter || "" : filter;

					var c = 0,
						limit = self.options.tagSelectionLimit;

					if (filter==="") {

						for (key in self.tags) {
							if (c >= limit) break;
							if (self.selected[key] || self.getTag(key).alias===undefined) continue;
							self.addToTagSelectionList(key);
							c++;
						}

					} else {

						var results = self.search(filter),
							i, l = results.length;

						for (i=0; i<l; i++) {
							if (c >= limit) break;
							var key = results[i];
							if (self.selected[key] || self.getTag(key).alias===undefined) continue;
							self.addToTagSelectionList(key);
							c++;
						}
					}

					self.tagSelection().toggleClass("no-selection", c < 1);
				},

				"{tagInput} keydown": function(tagInput, event) {

					event.stopPropagation();

					self.realEnterKey = (event.keyCode==13);
				},

				"{tagInput} keypress": function(tagInput, event) {

					event.stopPropagation();

					// We need to verify whether or not the user is actually entering
					// an ENTER key or exiting from an IME context menu.
					self.realEnterKey = self.realEnterKey && (event.keyCode==13);
				},

				"{tagInput} keyup": function(tagInput, event) {

					clearTimeout(self.filterTask);

					event.stopPropagation();

					switch(event.keyCode) {

						case 27: // escape
							tagInput.val("");
							break;

						case 13: // enter
							if (self.realEnterKey && tagInput.hasClass("canCreate")) {
								self.createTagFromInput();
							}
							break;
					}

					self.filterTask = setTimeout(function(){

						self.showTagSelections(tagInput.val());

					}, 250);
				},

				createTagFromInput: function() {

					var title = $.trim(self.tagInput().val());

					if (title!=="") {

						var key = self.getKey(title),
							tag = self.createTag(title);

						self.selectTag(key);

						self.tagInput().val("");
					}

					// Reset show tag selections to original state
					self.showTagSelections("");
				},

				checkTagLimit: function() {

					var limit = self.options.tagLimit;

					if (limit < 1) return;

					var totalTags = self.tagItems().length;

					// Update data count
					self.totalTags().text(totalTags);

					self.tagCreationForm()[totalTags >= limit ? "hide" : "show"]();
				},

				"{tagCreateButton} click": function() {

					self.createTagFromInput();
				},

				"{tagSelectionItems} click": function(el) {

					var key = el.data("key");
					self.selectTag(key);
				},

				"{tagItemRemoveButton} click": function(el) {

					var key = el.parents(".tag-item").data("key");

					self.unselectTag(key);
				},

				"{showAllTagsButton} click": function(el) {

					if (el.hasClass("active")) {

						el.removeClass("active");
						self.options.tagSelectionLimit = self.originalLimit;

					} else {

						el.addClass("active");
						self.originalLimit = self.options.tagSelectionLimit;
						self.options.tagSelectionLimit = 9999;
					}

					self.showTagSelections("");
				}
			}}
		);

		module.resolve();

	});
});
