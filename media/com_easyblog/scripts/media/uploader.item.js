// module: start
EasyBlog.module("media/uploader.item", function($) {

var module = this;

// controller: start
EasyBlog.Controller("Media.Browser.Uploader.Item",

	{
		defaultOptions: {
			"{filename}": ".uploadFilename",
			"{progressBar}": ".uploadProgressBar progress",
			"{percentage}": ".uploadPercentage",
			"{status}": ".uploadStatus",
			"{removeButton}": ".uploadRemoveButton"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			self.element.data("item", self);

			self.filename()
				.html(self.file.name);

			self.setState("queued");
		},

		getFilesize: function(p, s) {

			return (
				(self.file.size===undefined || self.file.size=="N/A") ?
					"":
                    ((p) ? p : "") + $.plupload.formatSize(self.file.size) +  ((s) ? s : "")
            );
		},

		setProgress: function(val) {

			self.progressBar()
				.attr("value", val);

			self.percentage()
				.html(val);
		},

		setState: function(state) {

			// queued, uploading, failed, done

			self.element
				.removeClass("upload-state-" + self.state)
				.addClass("upload-state-" + state);

			self.state = state;
		},

		setMessage: function(message) {

			self.status()
				.html(message);
		},

		"{removeButton} click": function(el, event) {

			event.stopPropagation();

			// TODO: Garbage collection
			self.element
				.slideUp(function(){

					self.element.remove();
				});
		}
	}}

);
// controller: end

module.resolve();


});
// module: end
