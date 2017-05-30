<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog.ready(function($){

	var checkRules = function(type) {
		$('#adminForm .option-' + type ).click();
	}

	$.Joomla("submitbutton", function(action) {

		if (action == 'enableall')
		{
			checkRules('enable');
		}
		else if(action == 'disableall')
		{
			checkRules( 'disable' );
		}
		else
		{
			$.Joomla("submitform", [action]);
		}
	});

	window.insertMember = function(id, name)
	{
		$('#cid').val(id);
		$('#aclid').html(id);
		$('#aclname').val(name);
		$.Joomla("squeezebox").close();
	}
});
</script>
<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">

<?php echo $this->loadTemplate( $this->getTheme() ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="acl" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="cid" id="cid" value="<?php echo !empty($this->rulesets->id)? $this->rulesets->id : ''; ?>" />
<input type="hidden" name="name" value="<?php echo !empty($this->rulesets->name)? $this->rulesets->name : ''; ?>" />
<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
<input type="hidden" name="add" value="<?php echo $this->add; ?>" />
</form>
