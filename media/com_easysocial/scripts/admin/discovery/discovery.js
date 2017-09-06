EasySocial.module('admin/discovery/discovery', function($) {

var module = this;

EasySocial.require()
.script('admin/progress/progress')
.done(function($) {

EasySocial.Controller('Admin.Discovery', {
	"defaultOptions": {
		
		"files": [],
		"namespace": "",
		"progressController": null,

		"{progress}": "[data-progress]",
		"{result}": "[data-discovery-result]"
	}
}, function(self, opts, base) { return {

	init: function() {

		// Implement progress bar
		opts.progressController = self.progress().addController(EasySocial.Controller.Progress);

		$.Joomla('submitbutton', function(task) {

			if (task == 'discover') {
				self.start();
			}

			return;
		});
	},

	start: function() {

		// Reset the logs
		self.reset();

		// Discover the list of files.
		EasySocial.ajax(opts.namespaces.discover, {

		}).done(function(files, message) {

			// Set the files to the properties.
			opts.files = files;

			if (opts.files.length > 0) {

				opts.progressController.begin(opts.files.length);

				self.log(message);

				// Begin to loop through each files.
				self.startIterating();
			
			} else {
				opts.progressController.begin(1);
				opts.progressController.completed('Discover Completed');

				self.log(opts.messages.completed);
			}
		});
	},

	// Resets the scan.
	reset: function() {
		self.result().empty();

		// Reset progress bar.
		self.options.progressController.reset();
	},

	log: function(message) {
		$('<tr>').append( $( '<td>' ).html( message ) ).appendTo(self.result());
	},

	startIterating: function() {
		
		// Get the file from the shelf
		var file = opts.files.shift();

		EasySocial.ajax(opts.namespaces.install, {
			"file": file
		}).done(function(message) {

			opts.progressController.touch('...');
			self.log(message);

			// As long as the files list are not empty yet, we still need to process it.
			if (opts.files.length > 0) {
				self.startIterating();
			} else {
				self.log(opts.messages.completed);
			}
		});
	}
}});

module.resolve();

});

});
