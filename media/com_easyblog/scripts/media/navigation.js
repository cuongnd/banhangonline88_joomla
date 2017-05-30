// module: start
EasyBlog.module("media/navigation", function($){

var module = this;

// require: start
EasyBlog.require()
// .view(
// 	"media/navigation.item",
// 	"media/navigation.itemgroup"
// )
.done(function(){


var $Media, $Library, DS;

// controller: start
EasyBlog.Controller(

	"Media.Navigation",

	{
		defaultOptions: {

			view: {
				item: "media/navigation.item",
				itemGroup: "media/navigation.itemgroup"
			},

			nestLevel: 8,
			groupCollapseDelay: 1000,
			canActivate: true,

			"{itemGroup}": ".navigationItemGroup",
			"{item}": ".navigationItem"
		}
	},

	function(self) { return {

		init: function() {

			// Globals
			$Media = self.media;
			$Library = $Media.library;
			DS = $Media.options.directorySeparator;

			self.element.toggleClass("canActivate", self.options.canActivate);
		},

		setPathway: function(meta) {

			var meta = $Library.getMeta(meta);

			if (!meta) return;

			self.currentKey = meta.key;

			// Set path as current path
			var place      = $Library.getPlace(meta.place),
				path       = meta.path,
				isFolder   = (meta.type==="folder"),
				folders    = path.split(DS).splice(1),
				nestLevel  = self.options.nestLevel,
				groupUntil = folders.length - ((folders.length % nestLevel) || nestLevel),
				itemGroup;

			// Clear out existing breadcrumb and
			// toggle folder class if path lead to a folder.
			self.element
				.empty()
				.toggleClass("type-folder", isFolder);

			// Base folder
			self.view.item({title: place.title || DS})
				.addClass("base")
				.data("key", place.id + "|" + DS)
				.appendTo(self.element);

			var isJomSocial = place.id==="jomsocial";

			if (path!==DS) {

				$.each(folders, function(i, folder) {

					var isFile = (!isFolder && i==folders.length-1),

						path = DS + folders.slice(0, i + 1).join(DS),

						key = place.id + "|" + path,

						folder = (isJomSocial) ? $Library.getMeta(key).title : folder,

						item = self.view.item({title: (isFile) ? meta.title : folder})
								   .data("key", key)
								   .toggleClass("filename", isFile);

					if (i >= groupUntil) {
						item.appendTo(self.element);
					} else {
						if (i % nestLevel == 0) {
							itemGroup = self.view.itemGroup()
											.appendTo(self.element);
						}
						item.appendTo(itemGroup);
					}
				});
			}
		},

		"{itemGroup} mouseover": function(el) {

			clearTimeout(el.data("delayCollapse"));
			el.addClass("expand");
		},

		"{itemGroup} mouseout": function(el) {

			el.data("delayCollapse",
				setTimeout(function() {
					el.removeClass("expand");
				}, self.options.groupCollapseDelay)
			);
		},

		"{item} click": function(el) {

			if (self.options.canActivate) {
				var key = el.data("key");
				self.trigger("activate", key);
			}
		}

	}}

);
// controller: end

module.resolve();

})
// require: end

});
// module: end
