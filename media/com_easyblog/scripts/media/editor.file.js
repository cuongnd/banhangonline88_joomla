// module: start
EasyBlog.module("media/editor.file", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/editor.file",
	"media/editor.file.preview"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Editor.File",

	{
		defaultOptions: {

			view: {
				panel: "media/editor.file",
				preview: "media/editor.file.preview"
			},

			"{editorPreview}": ".editorPreview",
			"{editorPanel}": ".editorPanel",

			// Preview
			"{filePreviewCaption}" : ".filePreviewCaption",

			// Insert button
			"{insertItemButton}": ".insertItemButton",
			"{insertItemDetail}": ".insertItemDetail",

			// Insert options
			"{insertCaption}"	: ".insertCaption",
			"{insertAs}"		: ".insertAs"
		}
	},

	function(self) {

		var $Media, $Library, $Browser;

		return {

		init: function() {

			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser;

			var meta = self.meta();

			// Panel
			self.editorPanel()
				.html(self.view.panel({
					meta: meta
				}))
				.implement(
					EasyBlog.Controller.Media.Editor.Panel,
					{},
					function() {

						// Keep a reference to this controller
						self.panel = this;
					}
				);

			// Preview
			self.editorPreview()
				.implement(
					EasyBlog.Controller.Media.Editor.Preview,
					{},
					function() {

						// Keep a reference to this controller
						self.preview = this;

						self.generatePreview();
					}
				);
		},

		generatePreview: function() {
			var preview = self.preview.container().find('a'),
				target = self.insertAs().val(),
				content = self.insertCaption().val();

			if(preview.length < 1) {
				var meta = self.meta();

				self.preview.container().html(self.view.preview({
					meta: meta,
					target: target,
					content: content
				}));
			} else {
				preview.attr('target', target).text(content);
			}
			self.preview.resetLayout();
		},

		setLayout: function() {

		},

		meta: function() {
			return $Library.getMeta(self.key);
		},

		place: function() {
			return $Library.getPlace(self.meta().place);
		},

		"{self} insertItem": function() {

			var meta = self.meta(),
				options = {
					title: meta.title,
					target: self.insertAs().val(),
					content: self.insertCaption().val()
				};

			$Media.insert(self.meta(), options);
		},

		"{insertCaption} keyup" : function(el) {
			self.generatePreview();
		},

		"{insertCaption} blur": function(el) {
			if(el.val() == '') {
				var meta = self.meta();

				el.val(meta.title);

				self.generatePreview();
			}
		},

		"{insertAs} change": function(el) {
			self.generatePreview();
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
