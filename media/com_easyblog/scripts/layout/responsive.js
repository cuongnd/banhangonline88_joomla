EasyBlog.module('layout/responsive', function($) {

	var module = this;

	if (EasyBlog.options.responsive) {
		$(function(){
			$('#eblog-wrapper')
				.responsive([
					{at: 818,  switchTo: 'w768'},
					{at: 600,  switchTo: 'w768 w600'},
					{at: 500,  switchTo: 'w768 w600 w320'}
				]);
		});
	}

	module.resolve();

});