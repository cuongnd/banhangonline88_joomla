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
<ul class="list-form reset-ul">
	<li>
		<label for="description"><?php echo JText::_('COM_EASYBLOG_BLOGS_META_DESCRIPTION'); ?></label>
		<div><textarea name="description" id="description" class="inputbox full-width"><?php echo $this->meta->description; ?></textarea></div>
	</li>
	<li>
		<label for="keywords"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_META_KEYWORDS' ); ?></label>
		<div>
			<textarea name="keywords" id="keywords" class="inputbox full-width"><?php echo $this->meta->keywords; ?></textarea>
			<div class="small">( <?php echo JText::_('COM_EASYBLOG_BLOGS_META_KEYWORDS_INSTRUCTIONS'); ?> )</div>
		</div>
	</li>
	<li>
		<label for="keywords"><?php echo JText::_( 'COM_EASYBLOG_BLOGS_META_ROBOTS' ); ?></label>
		<div>
			<input type="text" name="robots" id="robots" class="inputbox full-width" value="<?php echo $this->blog->robots;?>" />
		</div>
	</li>
</ul>