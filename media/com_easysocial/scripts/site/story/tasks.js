EasySocial.module('site/story/tasks', function($) {

var module = this;

EasySocial.Controller('Story.Tasks', {
	defaultOptions: {
		"{form}": "[data-story-tasks-form]",
		"{input}": "[data-story-tasks-input]",
		"{remove}": "[data-story-tasks-remove]",
		"{list}": "[data-story-tasks-list]",
		"{milestone}": "[data-story-tasks-milestone]",
		"{due}": "[data-story-tasks-due]"
	}
}, function(self, opts) { return {

	"{story} save": function(element, event, save) {
		var values = new Array();

		$.each(self.input() , function( i , item ) {
			if ($( item ).val() != '') {
				values.push($(item).val() );	
			}
		});

		var data = {
						"items": values,
						"milestone": self.milestone().val(),
						"due": self.due().val()
					};

		save.addData(self, data);
	},

	"{story} clear": function() {
	}

}});
	
module.resolve();
});