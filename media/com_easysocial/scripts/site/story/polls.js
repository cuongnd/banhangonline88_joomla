EasySocial.module('site/story/polls', function($) {

var module = this;


EasySocial.Controller('Story.Polls', {
    defaultOptions: {
        '{base}': '[data-story-event-base]',

        '{category}': '[data-story-event-category]',
        '{form}': '[data-story-event-form]',

        '{timezone}': '[data-event-timezone]',

        '{datetimeForm}': '[data-event-datetime-form]',

        '{datetime}': '[data-event-datetime]',

        '{title}': '[data-event-title]',
        '{description}': '[data-event-description]',
    }
}, function(self, opts) { return {

    '{story} save': function(element, event, save) {

        if (save.currentPanel != 'polls') {
            return;
        }

        var pollController = element.find('[data-polls-form]').controller('EasySocial.Controller.Polls.Form');

        self.options.name = 'polls';

        var task = save.addTask('validatePollsForm');
        self.save(task, pollController);
    },

    save: function(task, pollController) {

        var valid = pollController.validateForm();

        if (! valid) {
            return task.reject(opts.error);
        }

        var data = pollController.toData();
        task.save.addData(self, data);

        task.resolve();
    }
}});

module.resolve();

});
