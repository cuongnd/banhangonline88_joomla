// module: start
EasyBlog.module("media/editor", function($){

var module = this;

// require: start
EasyBlog.require()
// .library(
// 	"ui/position"
// )
// .view(
// 	"media/editor",
// 	"media/editor.viewport"
// )
.done(function(){

// controller: start
EasyBlog.Controller(

	"Media.Editor",

	{
		defaultOptions: {

			view: {
				editor: "media/editor",
				viewport: "media/editor.viewport"
			},

            "{modalHeader}": ".modalHeader",
            "{modalToolbar}": ".modalToolbar",
            "{modalContent}": ".modalContent",

            "{navigationPathway}": ".navigationPathway",

            "{insertItemButton}": ".insertItemButton",
            "{cancelEditingButton}": ".cancelEditingButton"
		}
	},

	function(self) { return {

		init: function() {

            self.element
                .addClass("editor")
                .html(self.view.editor());

			// Browser navigation
			self.navigationPathway()
				.implement(
					EasyBlog.Controller.Media.Navigation,
					{
						controller: {
							media: self.media
						},

						canActivate: false
					},
					function() {
						// Assign controller as a property of myself
						self.navigation = this;
					}
				);

			self.setLayout();
		},

		setLayout: function() {

            // Don't set layout if current modal is not us
            if (self.media.currentModal!=="editor") return;

			self.modalContent()
				.hide()
				.height(
					self.element.height() -
					self.modalHeader().outerHeight() -
					self.modalToolbar().outerHeight()
				)
				.show();

			// Also trigger set layout on handler
			var currentEditor = self.getEditor(self.currentEditor);

			if (currentEditor) {
				currentEditor.setLayout && currentEditor.setLayout();
			}
		},

		editors: [],

		handlers: [],

		loadHandler: function(type) {

			var handlerLoader = self.handlers[type];

			if (handlerLoader!==undefined) return handlerLoader;

			// Create new handler loader
			handlerLoader = $.Deferred();

			// Load handler
			handlerLoader.require =

				EasyBlog.require()
					.script(
						"media/editor." + type
					)
					.done(function(){

						var EditorHandler = EasyBlog.Controller.Media.Editor[$.String.capitalize(type)];

						if (EditorHandler!==undefined) {

							handlerLoader.resolve(EditorHandler);

						} else {

							delete self.handlers[type];

							handlerLoader.reject();
						}
					})
					.fail(function(){

						handlerLoader.reject();
					});

			return handlerLoader;
		},

		createEditor: function(key, callback) {

			// This will attempt to remove any previously created editor
			self.removeEditor(key);

			var meta = self.media.library.getMeta(key);

			// If there's no meta, skip.
			// TODO: Show error.
			if (meta===undefined) return;

			self.loadHandler(meta.type)
				.done(function(EditorHandler) {

					// Create editor & implement handler
					var editor = new EditorHandler(

						self.view.viewport()
							.addClass("editor-type-" + meta.type)
							.prependTo(self.modalContent()),

						{
							controller: {
								media: self.media,
								editor: self,
								key: self.media.library.getKey(meta)
							}
						}
					);

					// Register this editor instance
					self.editors[key] = editor;

					callback && callback(editor);
				});
		},

		removeEditor: function(key) {

			var editor = self.editors[key];

			if (editor===undefined) return;

			editor.destroy();

			delete self.editors[key];
		},

		getEditor: function(key) {

			return self.editors[key];
		},

		activateEditor: function(key) {

			self.deactivateEditor(self.currentEditor);

			// Set navigation pathway
			self.navigation.setPathway(key);

			var editor = self.getEditor(key),

				activateEditor = function(editor) {

					self.currentEditor = key;

					editor.element.addClass("active");

					editor.activate && editor.activate();
				};

			if (editor===undefined) {

				self.createEditor(key, activateEditor);

			} else {

				activateEditor(editor);
			}
		},

		deactivateEditor: function() {

			var editor = self.getEditor(self.currentEditor);

			if (editor===undefined) return;

			editor.deactivate && editor.deactivate();

			editor.element.removeClass("active");
		},

		"{self} modalActivate": function(el, event, key) {

			self.activateEditor(key);
		},

		"{self} modalDeactivate": function() {

			self.deactivateEditor();
		},

		"{insertItemButton} click": function() {

			var editor = self.getEditor(self.currentEditor);

			if (editor===undefined) return;

			editor.trigger("insertItem");
		},

		"{cancelEditingButton} click": function() {

			var editor = self.getEditor(self.currentEditor);

			if (editor) {
				editor.trigger("cancelItem");
			}

			self.media.browse();
		}
	}}

);

EasyBlog.Controller(

	"Media.Editor.Panel",

	{
		defaultOptions: {

			"{sectionHeader}": ".panelSectionHeader",
			"{sectionContent}": ".panelSectionContent"
		}
	},

	function(self) { return {

		init: function() {

		},

		// Common editor UI behaviour
		"{sectionHeader} click": function(sectionHeader) {

			var section = sectionHeader.parent();

			section.toggleClass("active");
		}
	}}
);

EasyBlog.Controller(

	"Media.Editor.Preview",

	{
		defaultOptions: {

			"{container}": ".previewContainer",
			"{dialogGroup}": ".previewDialogGroup"
		}
	},

	function(self) { return {

		init: function() {
		},

		resetLayout: function() {

			clearTimeout(self.resetLayoutTimer);

			self.resetLayoutTimer = setTimeout(function(){

				var container = self.container(),
					width = self.element.width(),
					height = self.element.height(),
					containerWidth = container.width(),
					containerHeight = container.height(),
					top = 0,
					left = 0,
					overflow = "none";

				if (containerWidth < width) {
					left = (width - containerWidth) / 2;
				} else {
					overflow = "auto";
				}

				if (containerHeight < height) {
					top = (height - containerHeight) / 2;
				} else {
					overflow = "auto";
				}

				self.element.css("overflow", overflow);

				container.css({
					top: top,
					left: left
				});

			}, 100);
		},

		showDialog: function(dialogName) {
			self.dialogGroup().addClass("show-dialog-" + dialogName);
		},

		hideDialog: function(dialogName) {
			self.dialogGroup().removeClass("show-dialog-" + dialogName);
		}
	}}
);

module.resolve();

});
// require: end

});
// module: end
