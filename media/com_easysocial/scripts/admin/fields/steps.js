EasySocial.module('admin/fields/steps', function($) {

var module = this;

EasySocial.Controller('Fields.Steps', {
	defaultOptions: {
		'{steps}': '[data-fields-step]',
		'{step}': '[data-fields-step-item]',
		'{stepLink}': '[data-fields-step-item-link]',
		'{add}': '[data-fields-step-add]',
	}
}, function(self, opts, base) { return {
	state: $.Deferred(),

	init: function() {
		self.ready();
	},

	ready: function() {
		self.state.resolve();
	},

	// Delayed init
	'{parent} controllersReady': function() {
		self.initSort();
	},

	initSort: function() {
		self.steps().sortable({
			items: self.step.selector,
			placeholder: 'ui-state-highlight',
			cursor: 'move',
			helper: 'clone',
			forceHelperSize: true,
			stop: function() {
				// Manually remove all the freezing tooltip due to conflict between bootstrap tooltip and jquery sortable
				$('.tooltip-es').remove();

				// Mark as changed
				$Parent.customFieldChanged();
			}
		});
	},

	'{parent} pageDeleted': function(el, event, uid) {
		self.deleteStep(uid);

		// Load the first step as the active page
		if($Steps.step().length > 0) {
			$Steps.stepLink(':first').tab('show');
		}
	},

	'{step} click': function(el, ev) {
		if(!el.hasClass('active')) {
			var id = el.data('id');
			$Parent.trigger('pageChanged', [$Editor.getPage(id), id]);
		}
	},

	'{add} click': function() {
		
		// Generate an unique id to link between step and page
		var uid = $.uid('step');

		// Add a new page menu on the sidebar
		EasySocial.ajax('admin/views/fields/getPageMenu', {
			"uid": uid
		}).done(function(contents) {

			self.add().before(contents);

			// Add a new page form.
			$Editor.addPage(uid, function() {
				
				// Go to the last page automatically since the last page would be the item that is created.
				self.stepLink(':last').tab('show');
			});
		});
	},

	getStep: function(uid) {
		return self.step().filterBy('id', uid);
	},

	getStepLink: function(uid) {
		return self.stepLink().filterBy('id', uid);
	},

	deleteStep: function(uid) {
		self.getStep(uid).remove();
	},

	getCurrentStep: function() {
		return self.step('.active');
	},

	currentStepIndex: function() {
		return self.step().index(self.step('.active')) + 1;
	},

	updateResult: function(sequence, newid) {
		var step = self.step(':eq(' + sequence + ')');

		if(step.data('id') != newid) {
			var oldid = step.data('id');

			step.removeAttr('data-fields-step-item-' + oldid);

			step.attr('data-fields-step-item-' + newid, true);

			step.data('id', newid);

			step.attr('data-id', newid);

			var stepLink = self.stepLink().eq(sequence);

			stepLink.removeAttr('data-fields-step-item-link-' + oldid);

			stepLink.attr('data-fields-step-item-link-' + newid, true);

			stepLink.attr('href', '#formStep_' + newid);
		}
	},

	toObject: function() {
		var data = [];

		$.each(self.stepLink(), function(i, step) {
			step = $(step);

			data.push({
				uid: step.data('id'),
				title: step.text(),
				description: step.attr('data-original-title')
			});
		});

		return data;
	}
}});

module.resolve();
});
