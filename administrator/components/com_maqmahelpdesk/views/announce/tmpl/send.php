<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class MaQmaHtmlSend
{
	static function display($announces, $n_users)
	{ ?>
	    <form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value=""/>

	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=announce"><?php echo JText::_('announcements'); ?></a>
	            <span><?php echo JText::_('send'); ?></span>
	        </div>
	        <div class="contentarea">
	            <br/>

	            <p><span><?php echo JText::_('first_announce');?></span>:
					<?php echo $announces->mindate; ?></p>

	            <p><span><?php echo JText::_('last_announce');?></span>:
					<?php echo $announces->maxdate; ?></p>

	            <p><span><?php echo JText::_('n_announces');?></span>:
					<?php echo $announces->total; ?></p>

	            <p><span><?php echo JText::_('n_subscribers');?></span>:
					<?php echo $n_users; ?></p>
	        </div>
	    </form><?php
	}
}
