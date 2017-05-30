// module: start
EasyBlog.module("media/editor.audio", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/editor.audio",
	"media/editor.audio.player"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Editor.Audio",

	{
		defaultOptions: {

			view: {
				panel: "media/editor.audio",
				player: "media/editor.audio.player"
			},

			player: {
				width: 400,
				height: 24,
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

			var meta = self.meta(),
				place = self.place();

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

						self.initPlayer();
					}
				);
		},

		initPlayer: function() {

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

		stop: function() {

			if (self.player) {

				if (self.player.getState()=="PLAYING") {

					self.player.pause();
				}
			}
		},

		deactivate: function() {

			self.stop();
		},

		"{self} cancelItem": function() {

			self.stop();
		},

		//
		// Insert audio
		//

		"{self} insertItem": function() {
			var options = {
				autostart: (self.autoplay().val() == '1') ? true : false,
			}

			$Media.insert(self.meta(), options);
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end
