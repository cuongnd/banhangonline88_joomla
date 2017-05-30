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

	window.migrateXMLBlogger = function() {

		if( $( '#bloggerxmlfiles' ).val() === null)
		{
		    alert('Please select xml file to proceed.');
		    return;
		}

		if( $( '#authorid' ).val() == '' )
		{
		    alert('Please enter your user id in Blog Import As field.');
		    return;
		}

		if( $( '#categoryId' ).val() == '' )
		{
		    alert('Please select category.');
		    return;
		}


		$( "#migrator-submit8" ).attr('disabled' , 'true');
		$( "#migrator-submit8" ).html('Updating ...');

		$( "#no-progress8" ).css( 'display' , 'none' );
		$( "#icon-wait8" ).css( 'display' , 'block' );
		$( "#progress-status8" ).html( '' );

		//form the query string so that we can use ejax to submit.
		finalData	= ejax.getFormVal('#adminForm8');
		//process the migration
		ejax.load('migrators','migrateArticle',finalData);
	}

	window.divSrolltoBottomBloggerXML = function() {

		var objDiv = document.getElementById("progress-status8");
	    objDiv.scrollTop = objDiv.scrollHeight;

		var objDiv2 = document.getElementById("stat-status8");
	    objDiv2.scrollTop = objDiv2.scrollHeight;
	}

});
</script>


<form action="" method="post" name="adminForm8" id="adminForm8">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform form-horizontal">
			<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_BLOGGERXML' ); ?></legend>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_DESC'); ?>
			</div>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_NOTICE'); ?>
			</div>

			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_BLOGGERXML_FILE' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_FILE'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_BLOGGERXML_FILE' ); ?>
					</span>
				</div>
					<?php echo $this->lists['bloggerxmlfiles'];?>
			</div><!-- /.control-group -->
			<br />
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_AS' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_AS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_AS' ); ?>
					</span>
				</div>
				<input type="input" size="5" name="authorid" id="authorid" value="<?php echo EasyBlogHelper::getDefaultSAIds() ; ?>" />
			</div><!-- /.control-group -->
			<br />
			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_INTO_CATEGORY' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_INTO_CATEGORY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_BLOGGERXML_IMPORT_INTO_CATEGORY' ); ?>
					</span>
				</div>
				<?php echo $this->getEasyBlogCategories(); ?>
			</div><!-- /.control-group -->

			<div style="padding-top:20px;">
				<button id="migrator-submit8" class="btn btn-success" onclick="migrateXMLBlogger();return false;"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></button>
			</div>

			<div id="icon-wait8" style="display:none;">
				<img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/wait.gif'; ?>" />&nbsp;&nbsp;<?php echo JText::_('COM_EASYBLOG_MIGRATOR_PLEASE_WAIT'); ?>
			</div>

			</fieldset>
		</div>
		<div class="span6">
			<fieldset class="adminform" style="height:200px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_PROGRESS' ); ?></legend>
				<div id="no-progress8"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				<div id="progress-status8"  style="overflow:auto; height:98%;"></div>
			</fieldset>

			<fieldset class="adminform" style="height:65px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_STATISTIC' ); ?></legend>
				<div id="stat-status8"  style="overflow:auto; height:98%;"></div>
			</fieldset>
		</div>
	</div>


<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="com_type" value="xml_blogger" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="migrators" />
</form>
