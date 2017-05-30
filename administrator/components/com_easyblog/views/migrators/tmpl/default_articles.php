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
EasyBlog.ready(function($) {

	window.migrateJoomla = function() {

		$( "#migrator-submit" ).attr('disabled' , 'true');
		$( "#migrator-submit" ).html('Updating ...');

		$( "#no-progress" ).css( 'display' , 'none' );
		$( "#icon-wait" ).css( 'display' , 'block' );
		$( "#progress-status" ).html( '' );

		finalData	= ejax.getFormVal('#adminForm');

		//process the migration
		ejax.load('migrators','migrateArticle',finalData);
	}

	window.divSrolltoBottom = function() {
		var objDiv = document.getElementById("progress-status");
	    objDiv.scrollTop = objDiv.scrollHeight;

		var objDiv2 = document.getElementById("stat-status");
	    objDiv2.scrollTop = objDiv2.scrollHeight;
	}
});
</script>

<form action="" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA' ); ?></legend>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_DESC'); ?>
			</div>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_NOTICE'); ?>
			</div>

			<table class="admintable" cellspacing="1">
				<tbody>
				<?php if(EasyBlogHelper::getJoomlaVersion() <= '1.5') : ?>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_SECTION' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_SECTION_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_SECTION' ); ?>
					</span>
					</td>
					<td valign="top">
						 <?php echo $this->lists['sectionid'];?>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_CATEGORY' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_CATEGORY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_CATEGORY' ); ?>
					</span>
					</td>
					<td valign="top">
						 <?php echo $this->lists['catid'];?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_AUTHOR' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_AUTHOR_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_AUTHOR' ); ?>
					</span>
					</td>
					<td valign="top">
						 <?php echo $this->lists['authorid'];?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_STATE' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_JOOMLA_STATE_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_JOOMLA_STATE' ); ?>
					</span>
					</td>
					<td valign="top">
						 <?php echo $this->lists['state'];?>
					</td>
				</tr>
				</tbody>
			</table>

			<?php if( $this->jomcommentInstalled ){ ?>
			<div>
				<input type="checkbox" name="content-jomcomment" id="content-jomcomment" />
				<label for="content-jomcomment"><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_CONTENT_JOMCOMMENT' );?></label>
			</div>
			<?php } ?>

			<div style="padding-top:20px;">
				<button id="migrator-submit" class="btn btn-success" onclick="migrateJoomla();return false;"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></button>
			</div>

			<div id="icon-wait" style="display:none;">
				<img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/wait.gif'; ?>" />&nbsp;&nbsp;<?php echo JText::_('COM_EASYBLOG_MIGRATOR_PLEASE_WAIT'); ?>
			</div>

			</fieldset>
		</div>
		<div class="span6">
			<fieldset class="adminform" style="height:200px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_PROGRESS' ); ?></legend>
				<div id="no-progress"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				<div id="progress-status"  style="overflow:auto; height:98%;"></div>
			</fieldset>

			<fieldset class="adminform" style="height:65px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_STATISTIC' ); ?></legend>
				<div id="stat-status"  style="overflow:auto; height:98%;"></div>
			</fieldset>
		</div>

	</div>

<input type="hidden" name="boxchecked" value="1" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="com_type" value="com_content" />
<input type="hidden" name="myblogSection" value="<?php echo $this->myBlogSection; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="migrators" />
</form>
