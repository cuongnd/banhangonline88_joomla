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

$element = JRequest::getVar('element');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="adminform-body">
	<table class="adminlist adminlist table table-striped" cellspacing="1">
	<thead>
		<tr>
			<th class="title" width="20%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_POSITION' );?></th>
			<th class="title" width="20%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_WIDTH' );?></th>
			<th class="title" width="20%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_HEIGHT' );?></th>
			<th class="title" width="15%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_METHOD' );?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="row">
			<td style="text-align:center;">
				<input type="field" class="inputbox full-width" name="newThemePosition" value="" />
			</td>
			<td style="text-align:center;">
				<input type="field" class="inputbox full-width" name="newThemeWidth" value="" />
			</td>
			<td style="text-align:center;">
				<input type="field" class="inputbox full-width" name="newThemeHeight" value="" />
			</td>
			<td style="text-align:center;">
				<select name="newThemeMethod" class="inputbox full-width">
					<option value="fill">- <?php echo JText::_('COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FILL'); ?> -</option>
					<option value="within">- <?php echo JText::_('COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_WITHIN'); ?> -</option>
					<option value="fit">- <?php echo JText::_('COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FIT') ?> -</option>
				</select>
			</td>
		</tr>
	</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="c" value="themes" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="element" value="<?php echo $element ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
