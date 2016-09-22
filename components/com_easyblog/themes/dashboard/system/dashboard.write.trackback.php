<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if( $this->acl->rules->add_trackback ){ ?>
<div class="ui-modbox" id="widget-write-trackback">
    <div class="ui-modhead">
        <div class="ui-modtitle"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_TRACKBACKING_HEADING'); ?></div>
    	<a href="javascript:void(0);" onclick="eblog.dashboard.toggle( this );" class="ui-tog pabs atr ir"><?php echo JText::_( 'COM_EASYBLOG_HIDE' );?></a>
    </div>
    <div class="ui-modbody clearfix">
        <div class="eblog-message info">
            <?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_TRACKBACKING_DESC'); ?>
        </div>
        <ul class="list-form reset-ul">
            <li>
                <div class="mrs">
                    <textarea name="trackback" id="description" class="input textarea width-full" style="height: 80px;"><?php echo (isset($blog->unsaveTrackbacks)) ? $blog->unsaveTrackbacks : ''; ?></textarea>
                </div>
                <div class="small"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_TRACKBACKING_INSTRUCTIONS'); ?></div>
        	</li>
            <?php
			if(!empty($trackbacks))
			{
            ?>
            <li>
        	    <label for="trackback"><?php echo JText::_('COM_EASYBLOG_TRACKBACK_URL_LIST'); ?></label>
        	    <div>
                    <div class="ui-inputfull mts">
                        <ul id="trackback-list" class="reset-ul">
            			<?php
        					foreach($trackbacks as $trackback)
        					{
        					?>
        					<li class="trackback-list-item">
        						<span class="trackback-caption"><?php echo $trackback->url; ?></span>
        					</li>
        					<?php
        					}
                        ?>
            			</ul>
                    </div>
                </div>
        	</li>
            <?php } ?>
        </ul>
    </div>
</div>
<?php } ?>
