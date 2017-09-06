<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
EasySocial
	.require()
	.script('apps/fields/page/permalink/content')
	.done(function($) {

		$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Page.Permalink',
		{
			required	: <?php echo $field->required ? 1 : 0; ?>,
			id			: <?php echo $field->id; ?>,
			pageid		: "<?php echo $pageid; ?>"
		});

	});
