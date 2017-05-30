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

<div class="adminform-body">
	<table class="adminlist adminlist table table-striped" cellspacing="1">
	<thead>
		<tr>
			<th width="2%"><?php echo JText::_( 'Num' ); ?></th>
			<th width="2%"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->blogimages ); ?>);" /></th>

			<th class="title" width="20%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_POSITION' );?></th>
			<th class="title" width="20%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_WIDTH' );?></th>
			<th class="title" width="20%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_HEIGHT' );?></th>
			<th class="title" width="20%"><?php echo JText::_( 'COM_EASYBLOG_ADVANCE_THEME_METHOD' );?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$i = 0;
			$x = 0;
		?>
		<?php foreach( $this->blogimages as $theme ){ ?>

		<?php $remainder = ($i % 2); ?>

		<tr class="row<?php echo $remainder; ?>">
			<td style="text-align:center;">
				<?php echo $i + 1; ?>
			</td>
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $theme->name); ?>
			</td>
			<td style="text-align:center;">
				<input type="field" class="inputbox full-width" name="themePosition[]" value="<?php echo $this->escape( $theme->name );?>" />
			</td>
			<td style="text-align:center;">
				<input type="field" class="inputbox full-width" name="themeWidth[]" value="<?php echo $this->escape( $theme->width );?>" />
			</td>
			<td style="text-align:center;">
				<input type="field" class="inputbox full-width" name="themeHeight[]" value="<?php echo $this->escape( $theme->height );?>" />
			</td>
			<td style="text-align:center;">

				<select name="themeMethod[]" class="inputbox " style="width:260px;">
					<option value="fill" <?php echo ($theme->resize == 'fill') ? 'selected="selected"' : '' ?> >- <?php echo JText::_('COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FILL'); ?> -</option>
					<option value="within" <?php echo ($theme->resize == 'within') ?'selected="selected"' : '' ?> >- <?php echo JText::_('COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_WITHIN'); ?> -</option>
					<option value="fit" <?php echo ($theme->resize == 'fit') ? 'selected="selected"' : '' ?> >- <?php echo JText::_('COM_EASYBLOG_THEME_BLOG_IMAGE_RESIZE_FIT') ?> -</option>
				</select>

			</td>
		</tr>
			<?php $i += 1; ?>
		<?php  } ?>
	</tbody>
	<tfoot>
		<td colspan="5"></td>
		<td style="text-align:center;">
			<a class="btn btn-primary" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=theme&layout=form&element=' . $element ); ?>"><?php echo JText::_('COM_EASYBLOG_ADD_NEW'); ?></a>
		</td>
	</tfoot>
	</table>
</div>
