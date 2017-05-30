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
<div class="adminform-body">
	<div>
	    <fieldset class="adminform">
	        <legend><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_UPDATE_API_KEY_LEGEND'); ?></legend>
	        <div style="padding: 10px 0 10px 0"><?php echo JText::_('COM_EASYBLOG_QUICKGUIDE_INSTRUCTIONS_UPDATE_API_KEY_DESC'); ?></div>
	        <form name="apiform" action="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=settings&task=saveApi' );?>" method="post">
			<input type="text" class="inputbox" style="width: 200px;" name="apikey" value="<?php echo EasyBlogHelper::getConfig()->get( 'apikey' ); ?>" />
			<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_SAVE' );?>" class="button" />
			<input type="hidden" name="option" value="com_easyblog" />
			<input type="hidden" name="c" value="settings" />
			<input type="hidden" name="task" value="saveApi" />
			<input type="hidden" name="from" value="updater" />
			</form>
			<div style="padding: 10px 0 10px 0">
				<?php echo JText::_( 'COM_EASYBLOG_QUICKGUIDE_RETRIEVE_API_KEY' ); ?> <a href="http://stackideas.com/dashboard" target="_blank" /><?php echo JText::_( 'COM_EASYBLOG_DOWNLOADS_AREA');?></a>
			</div>
		</fieldset>
	</div>


    <fieldset class="adminform">
	    <legend><?php echo JText::_('COM_EASYBLOG'); ?></legend>
		<div>
	    	<div>
	    		<p><?php echo JText::sprintf( 'COM_EASYBLOG_UPDATER_LOCAL_VERSION' , $this->getInstalledVersion() ); ?></p>
	    		<p><?php echo JText::sprintf( 'COM_EASYBLOG_UPDATER_LATEST_VERSION' , $this->getLatestVersion() ); ?></p>

	    		<?php if( $this->getInstalledVersion() < $this->getLatestVersion() ){ ?>
					<?php if( !$this->curl ){ ?>
						<div><?php echo JText::_( 'COM_EASYBLOG_UPDATER_NEEDS_CURL' ); ?></div>
					<?php } else { ?>
						<p>
							<input type="checkbox" name="agree" id="agree" />
							<label for="agree" style="margin-top:4px;margin-left:5px;line-height:14px;"><?php echo JText::_( 'COM_EASYBLOG_UPDATER_TERMS' ); ?></label>
						</p>
					<div style="margin-top:3px"><input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_UPDATER_RUN' ); ?>" onclick="downloadPatches();" class="button-green" /></div>
					<?php } ?>
	    		<?php } else { ?>
	    		<div>
	    			<?php echo JText::_( 'COM_EASYBLOG_UPDATER_ALREADY_LATEST' );?>
	    		</div>
	            <?php } ?>
	    	</div>
	    </div>
	    <?php if( $this->getInstalledVersion() < $this->getLatestVersion() ){ ?>
	    <div id="progress-box">

	        <div id="progress-bar">
	            <div class="bar-holder">
	                <div class="bar-progress" id="bar-progress" style="width:0%;"><span id="progress-indicator" style="color:#fff;line-height:22px;margin-left:5px;"></span></div>
	            </div>
	        </div>
	        <div id="progress-log">
	            <div id="result-notices">
	                <div class="result-holder" id="result-holder"></div>
	            </div>
	            <a href="javascript:void(0);" onclick="toggleLogs();"><?php echo JText::_( 'COM_EASYBLOG_UPDATER_VIEW_LOGS' ); ?></a>
	        </div>
	    </div>
	    <?php } ?>
    </fieldset>
</div>
