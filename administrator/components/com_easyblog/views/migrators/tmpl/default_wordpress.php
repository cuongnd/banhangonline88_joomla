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

	window.migrateWordPress = function()
	{
		$( "#migrator-submit5" ).attr('disabled' , 'true');
		$( "#migrator-submit5" ).html('Updating ...');

		$( "#no-progress5" ).css( 'display' , 'none' );
		$( "#icon-wait5" ).css( 'display' , 'block' );
		$( "#progress-status5" ).html( '' );

		//form the query string so that we can use ejax to submit.
		finalData	= ejax.getFormVal('#adminForm5');
		//process the migration
		ejax.load('migrators','migrateArticle',finalData);
	}

	window.divSrolltoBottomWordPress = function()
	{
		var objDiv = document.getElementById("progress-status5");
	    objDiv.scrollTop = objDiv.scrollHeight;

		var objDiv2 = document.getElementById("stat-status5");
	    objDiv2.scrollTop = objDiv2.scrollHeight;
	}

});
</script>

<?php if( !$this->wpInstalled ) { ?>
	<?php echo JText::_('COM_EASYBLOG_MIGRATOR_WORDPRESS_COMPONENT_NOT_FOUND');?>
<?php } else { ?>

<form action="" method="post" name="adminForm5" id="adminForm5">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform form-horizontal">
			<legend><?php echo JText::_( 'Wordpress' ); ?></legend>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_MYBLOG_DESC'); ?>
			</div>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_MYBLOG_NOTICE'); ?>
			</div>

			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'WordPress Blog' ); ?>::<?php echo JText::_('WordPress Blog Desc'); ?>">
						<?php echo JText::_( 'WordPress Blog' ); ?>
					</span>
				</div>
					<?php echo $this->lists['wpblogs'];?>
			</div><!-- /.control-group -->

			<div style="padding-top:20px;">
				<button id="migrator-submit5" class="btn btn-success" onclick="migrateWordPress();return false;"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></button>
			</div>

			<div id="icon-wait5" style="display:none;">
				<img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/wait.gif'; ?>" />&nbsp;&nbsp;<?php echo JText::_('COM_EASYBLOG_MIGRATOR_PLEASE_WAIT'); ?>
			</div>

			</fieldset>
		</div>
		<div class="span6">
			<fieldset class="adminform" style="height:200px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_PROGRESS' ); ?></legend>
				<div id="no-progress5"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				<div id="progress-status5"  style="overflow:auto; height:98%;"></div>
			</fieldset>

			<fieldset class="adminform" style="height:65px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_STATISTIC' ); ?></legend>
				<div id="stat-status5"  style="overflow:auto; height:98%;"></div>
			</fieldset>
		</div>
	</div>


<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="com_type" value="com_wordpress" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="migrators" />
</form>
<?php } ?>
