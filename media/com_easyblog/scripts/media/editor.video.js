// module: start
EasyBlog.module("media/editor.video", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/editor.video",
	"media/editor.video.player"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Editor.Video",

	{
		defaultOptions: {

			view: {
				panel: "media/editor.video",
				player: "media/editor.video.player"
			},

			player: {
				width: 400, // 16
				height: 225, // 9
				autostart: false,
				controlbar: "bottom",
				backcolor: "#333333",
				frontcolor: "#ffffff",
				modes: [
					{
						type: 'html5'
					},
					{
						type: 'flash',
						src: $.rootPath + "/components/com_easyblog/assets/vendors/jwplayer/player.swf"
					},
					{
						type: 'download'
					}
				]
			},

			"{editorPreview}": ".editorPreview",
			"{editorPanel}": ".editorPanel",

			"{playerContainer}": ".playerContainer",

			// Insert options
			"{insertWidth}" : ".insertWidth",
			"{insertHeight}": ".insertHeight",
			"{autoplay}": ".autoplay"
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

			var insertWidth = $Media.options.exporter.video.width;
			var insertHeight = $Media.options.exporter.video.height;

			if (insertWidth!==undefined) {
				self.options.player.width = insertWidth;
			}

			if (insertHeight!==undefined) {
				self.options.player.height = insertHeight;
			}

			// Panel
			self.editorPanel()
				.html(self.view.panel({
					meta: meta,
					insertWidth: self.options.player.width,
					insertHeight: self.options.player.height
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

						self.initPlayer();
					}
				);
		},

		initPlayer: function() {

			// Show loading indicator
			self.preview.showDialog("loading");

			EasyBlog.require()
				.script($.rootPath + "/components/com_easyblog/assets/vendors/jwplayer/jwplayer.js")
				.done(function($) {

					var meta = self.meta(),

						place = self.place(),

						id = "player-" + $.uid(),

						options = $.extend(self.options.player, {
							id: id,
							file: self.meta().url,
						}),

						player = self.view.player({
							id: id,
							meta: meta,
							options: options
						});

					// Append player container
					self.preview.container()
						.append(player);

					self.player = jwplayer(id).setup(options);

					self.preview.resetLayout();

					// Hide loading indicator
					self.preview.hideDialog("loading");
				})
				.fail(function() {
				});
		},

		meta: function() {
			return $Library.getMeta(self.key);
		},

		place: function() {
			return $Library.getPlace(self.meta().place);
		},

		setLayout: function() {

		},

		"{self} cancelItem": function() {

			if (self.player) {

				if (self.player.getState()=="PLAYING") {

					self.player.pause();
				}
			}
		},

		//
		// Insert video
		//

		"{self} insertItem": function() {
			var options = {
				autostart: (self.autoplay().val() == '1') ? true : false,
				width: parseInt(self.insertWidth().val(), 10),
				height: parseInt(self.insertHeight().val(), 10)
			}

			$Media.insert(self.meta(), options);
		},

		resize: function() {

			if (self.player) {
				var width = parseInt(self.insertWidth().val(), 10);
				var height = parseInt(self.insertHeight().val(), 10);
				self.player.resize(width, height);
				self.preview.resetLayout();
			}
		},

		"{insertWidth} keyup": function() {

			self.resize();
		},

		"{insertHeight} keyup": function() {

			self.resize();
		}

	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
