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
<div id="section-trackbacks" class="blog-section tab_container">
	<div>
		<div class="entry-trackback" onclick="eblog.trackback.url.copy();"><?php echo JText::_('COM_EASYBLOG_ENTRY_TRACKBACK_DESC'); ?></div>
		<div class="entry-trackback-input mtl">
            <input type="text" class="inputbox width-full ffa fsl fwb" id="trackback-url" value="<?php echo $trackbackURL;?>" style="width: 90%;" onclick="eblog.trackback.url.copy();" />
        </div>
		<?php
		if( !empty( $trackbacks) )
		{
        ?>
        <ul class="entry-trackback-links reset-ul">
        <?php
			foreach( $trackbacks as $trackback )
			{
		?>
		<li>
			<h4 class="trackback-item-title"><a href="<?php echo $trackback->url ;?>"><?php echo $trackback->title; ?></a></h4>
			<div class="trackback-item-date"><?php echo JText::_('COM_EASYBLOG_POSTED_BY'); ?> <?php echo $trackback->blog_name; ?> <?php echo JText::_('COM_EASYBLOG_ON'); ?> <?php echo $this->formatDate( $system->config->get('layout_dateformat', '%A, %d %B %Y') , $trackback->created ); ?></div>
			<div class="trackback-item-content"><?php echo $trackback->excerpt; ?> ... </div>
		</li>
		<?php
			}
        ?>
        </ul>
        <?php
		}
		?>
	</div>
</div>