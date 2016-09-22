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
<script type="text/javascript" language="javascript">
EasyBlog.ready(function($){

	window.migrateK2 = function()
	{
		$( "#migrator-submit" ).attr('disabled' , 'true');
		$( "#migrator-submit" ).html('Updating ...');

		$( "#no-progress" ).css( 'display' , 'none' );
		$( "#icon-wait" ).css( 'display' , 'block' );
		$( "#progress-status" ).html( '' );

		finalData	= ejax.getFormVal('#k2form');

		//process the migration
		ejax.load('migrators','migrateK2',finalData);
	}

	window.scrollToBottomK2 = function()
	{
		var objDiv = document.getElementById("progress-status");
	    objDiv.scrollTop = objDiv.scrollHeight;

		var objDiv2 = document.getElementById("stat-status");
	    objDiv2.scrollTop = objDiv2.scrollHeight;
	}

});
</script>

<form action="" method="post" name="adminForm" id="k2form">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform form-horizontal">
			<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_K2' ); ?></legend>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_K2_DESC'); ?>
			</div>
			<div>
				<ul>
					<li><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_K2_NOTICE_BACKUP' );?></li>
					<li><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_K2_NOTICE_OFFLINE' );?></li>
				</ul>
			</div>

			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_CATEGORY' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_CATEGORY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_CATEGORY' ); ?>
					</span>
				</div>
					<?php echo $this->lists['k2cats'];?>
			</div><!-- /.control-group -->

			<div class="control-group">
				<div class="control-label">

				</div>
				<input type="checkbox" name="migrate_k2_comments" value="1" id="migrate_k2_comments" />
				<div class="help-inline">
				<label for="migrate_k2_comments"><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_K2_COMMENTS' ); ?></label>
				</div>
			</div><!-- /.control-group -->

			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">

					</td>
					<td valign="top">

					</td>
				</tr>
				<tr>
					<td class="key">
						&nbsp;
					</td>
					<td>

					</td>
				</tr>
				</tbody>
			</table>

			<div style="padding-top:20px;">
				<button id="migrator-submit-k2" class="btn btn-success" onclick="migrateK2();return false;"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></button>
			</div>

			<div id="icon-wait-k2" style="display:none;">
				<img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/wait.gif'; ?>" />&nbsp;&nbsp;<?php echo JText::_('COM_EASYBLOG_MIGRATOR_PLEASE_WAIT'); ?>
			</div>

			</fieldset>
		</div>
		<div class="span6">
			<fieldset class="adminform" style="height:200px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_PROGRESS' ); ?></legend>
				<div id="no-progress-k2"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				<div id="progress-status-k2"  style="overflow:auto; height:98%;"></div>
			</fieldset>

			<fieldset class="adminform" style="height:65px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_STATISTIC' ); ?></legend>
				<div id="stat-status-k2"  style="overflow:auto; height:98%;"></div>
			</fieldset>
		</div>
	</div>


<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="com_type" value="com_k2" />
<input type="hidden" name="myblogSection" value="<?php echo $this->myBlogSection; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="migrators" />
</form>
