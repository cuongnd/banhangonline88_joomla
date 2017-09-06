EasySocial.module('site/system/notifier', function($){

var module = this;

EasySocial.Controller('System.Notifier', {
	defaultOptions: {
		"interval": 45
	}
}, function(self, opts) { return {

	init: function() {
		// run the checking only if the user is not a guest.
		if (!opts.guest) {
			self.start();
		}
	},

	getInterval: function() {
		return opts.interval * 1000;
	},

	start: function() {
		opts.state = setTimeout(self.check, self.getInterval());
	},

	stop: function() {
		clearTimeout(opts.state);
	},

	check: function() {
		// When checking, ensure that all previous queues are stopped
		self.stop();

		// Needs to run in a loop since we need to keep checking for new notification items.
		setTimeout(function(){

			// before we send request to server, lets gather data from
			// other plugins.
			var collection = {};
			self.element.trigger('notifier.collection', collection);

			EasySocial.ajax('site/controllers/notifier/check', {
				"data": collection,
			}).done(function(data) {
				self.element.trigger('notifier.updates', data);
				self.start();
			});

		}, self.getInterval());
	}

}});

module.resolve();
});
