(function($)
{
	$(document).ready(function()
	{
		$('div.panel h3').click(function(){
			$('div.pane-slider').css({'overflow':'hidden'});
		});
		$('div.pane-slider').click(function(){
			$(this).css({'overflow':'visible'});
			$(this).find('fieldset.panelform').css({'overflow':'visible'});
		});
		
		//$('*[rel=tooltip]').tooltip()

		// Turn radios into btn-group
		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(function()
		{
			var label = $(this);
			var input = $('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0 || input.val().toLowerCase() == 'false' || input.val().toLowerCase() == 'no') {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.trigger('change');
			}
		});
		
		$('.btn-group input[checked=checked]').each(function()
		{
			if ($(this).val() == '') {
				$('label[for=' + $(this).attr('id') + ']').addClass('active btn-primary');
			} else if ($(this).val() == 0 || $(this).val().toLowerCase() == 'false' || $(this).val().toLowerCase() == 'no') {
				$('label[for=' + $(this).attr('id') + ']').addClass('active btn-danger');
			} else {
				$('label[for=' + $(this).attr('id') + ']').addClass('active btn-success');
			}
		});
		// add color classes to chosen field based on value
		$('select[class^="chzn-color"], select[class*=" chzn-color"]').on('liszt:ready', function(){
			var select = $(this);
			var cls = this.className.replace(/^.(chzn-color[a-z0-9-_]*)$.*/, '\1');
			var container = select.next('.chzn-container').find('.chzn-single');
			container.addClass(cls).attr('rel', 'value_' + select.val());
			select.on('change click', function()
			{
				container.attr('rel', 'value_' + select.val());
			});

		});
	})
})(jQuery);