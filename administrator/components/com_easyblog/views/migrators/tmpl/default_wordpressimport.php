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

	window.migrateXMLWordPress = function() {

		if( $( '#wpxmlfiles' ).val() === null)
		{
		    alert('Please select xml file to proceed.');
		    return;
		}


		$( "#migrator-submit6" ).attr('disabled' , 'true');
		$( "#migrator-submit6" ).html('Updating ...');

		$( "#no-progress6" ).css( 'display' , 'none' );
		$( "#icon-wait6" ).css( 'display' , 'block' );
		$( "#progress-status6" ).html( '' );

		//form the query string so that we can use ejax to submit.
		finalData	= ejax.getFormVal('#adminForm6');
		//process the migration
		ejax.load('migrators','migrateArticle',finalData);
	}

	window.divSrolltoBottomWordPressXML = function() {

		var objDiv = document.getElementById("progress-status6");
	    objDiv.scrollTop = objDiv.scrollHeight;

		var objDiv2 = document.getElementById("stat-status6");
	    objDiv2.scrollTop = objDiv2.scrollHeight;
	}

});
</script>


<form action="" method="post" name="adminForm6" id="adminForm6">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform form-horizontal">
			<legend><?php echo JText::_( 'Wordpress XML Import' ); ?></legend>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_WP_IMPORT_DESC'); ?>
			</div>
			<div>
				<?php echo JText::_('COM_EASYBLOG_MIGRATOR_WP_IMPORT_NOTICE'); ?>
			</div>

			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_WP_WORDPRESS_BLOG' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_WP_WORDPRESS_XML_FILE'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_WP_WORDPRESS_XML_FILE' ); ?>
					</span>
				</div>
					<?php echo $this->lists['wpxmlfiles'];?>
			</div><!-- /.control-group -->

			<div class="control-group">
				<div class="control-label">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_WP_BLOG_IMPORT_AS' ); ?>::<?php echo JText::_('COM_EASYBLOG_MIGRATOR_WP_BLOG_IMPORT_AS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_WP_BLOG_IMPORT_AS' ); ?>
					</span>
				</div>
					<input type="input" size="5" name="authorid" id="authorid" value="<?php echo EasyBlogHelper::getDefaultSAIds() ; ?>" />
			</div><!-- /.control-group -->


			<div style="padding-top:20px;">
				<button id="migrator-submit6" class="btn btn-success" onclick="migrateXMLWordPress();return false;"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_RUN_NOW'); ?></button>
			</div>

			<div id="icon-wait6" style="display:none;">
				<img src="<?php echo JURI::root() . 'administrator/components/com_easyblog/assets/images/wait.gif'; ?>" />&nbsp;&nbsp;<?php echo JText::_('COM_EASYBLOG_MIGRATOR_PLEASE_WAIT'); ?>
			</div>

			</fieldset>
		</div>
		<div class="span6">
			<fieldset class="adminform" style="height:200px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_PROGRESS' ); ?></legend>
				<div id="no-progress6"><?php echo JText::_('COM_EASYBLOG_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				<div id="progress-status6"  style="overflow:auto; height:98%;"></div>
			</fieldset>

			<fieldset class="adminform" style="height:65px;">
				<legend><?php echo JText::_( 'COM_EASYBLOG_MIGRATOR_STATISTIC' ); ?></legend>
				<div id="stat-status6"  style="overflow:auto; height:98%;"></div>
			</fieldset>
		</div>
	</div>


<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="com_type" value="xml_wordpress" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="migrators" />
</form>
