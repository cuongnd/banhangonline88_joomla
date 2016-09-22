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
<div class="stackTip tooltip-calendar" data-options='<?php echo $options; ?>'>
<div id="ezttip">
<div id="ezttip-in">
<div id="ezttip-in-in">
    <div class="ezttip-wrap">
        <div class="ezttip-content">
            <div class="ezttip-title title-calendar"><span><?php echo $date;?></span></div>
            <div class="ezttip-desc"><?php echo $this->getNouns( 'COM_EASYBLOG_CALENDAR_COUNT', count( $data ) , true ); ?></div>
            <ul class="ezttip-entries-list reset-ul">
            <?php foreach( $data as $entry ){ ?>
	            <li>
					<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id . '&Itemid=' . $itemId );?>"><?php echo $entry->title;?></a>
				</li>
            <?php } ?>
            </ul>
        </div>
    </div>
</div>
</div>
</div>
</div>