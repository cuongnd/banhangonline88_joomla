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

/*
 * Layout utilities class.
 * 
 */   

class LayoutHelper
{

	function links($rows, $team)
	{
?>
		<ul class="eblog_entry_links_list">
		<?php for ( $i = 1; $i < count($rows); $i++) { ?>
			<?php $item = $rows[$i]; ?>
			<li>
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$item->id); ?>">
					<?php echo (JString::strlen($item->title) > 30) ? JString::substr(strip_tags($item->title), 0, 30) . '...' : strip_tags($item->title) ; ?>
				</a>
				<span><?php echo (JString::strlen($item->content) > 100) ? JString::substr(strip_tags($item->content), 0, 100) . '...' : strip_tags($item->content) ; ?></span>
			</li>
		<?php }//end for ?>
		</ul>
<?php
	}

}