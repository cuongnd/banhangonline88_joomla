
EasySocial.require().script('site/comments/frame').done(function($) {
	var selector = '[data-comments="<?php echo $group; ?>-<?php echo $element; ?>-<?php echo $verb; ?>-<?php echo $uid; ?>"]';

	$(selector).addController('EasySocial.Controller.Comments', {
		'enterkey': '<?php echo $this->config->get('comments.enter'); ?>'
	});
});
