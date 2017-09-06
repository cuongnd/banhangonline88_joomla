EasySocial.module('shared/popdown', function($){

var module = this;

$(document)
	.on('click.data.popdown', '[data-popdown-option]', function(event) {
		
		event.preventDefault();

		var option = $(this);
		var optionHtml = option.find('>a').html();
		var tmpl = $(optionHtml).clone();

		var popdown = option.parents('[data-popdown]');
		var value = option.data('popdown-option');
		var active = popdown.find('[data-popdown-active]');
		var input = popdown.find('input[type=hidden]');

		// Set the current option as active
		popdown.find('[data-popdown-option]').removeClass('active');
		option.addClass('active');

		active.html(tmpl);
		input.val(value);
	});



module.resolve();
});
