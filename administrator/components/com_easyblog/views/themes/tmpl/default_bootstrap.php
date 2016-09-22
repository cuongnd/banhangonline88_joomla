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
<div class="row-fluid">

	<div class="span12">
		<table class="table table-striped">
		<thead>
			<tr>
				<th class="center nowrap" width="5%"><?php echo JText::_( 'COM_EASYBLOG_THEME_DEFAULT' );?></th>
				<th width="15%" class="nowrap" colspan="2"><?php echo JText::_( 'Preview' ); ?></th>
				<th><?php echo JText::_( 'COM_EASYBLOG_THEME_NAME' );?></th>
				<th class="center nowrap" width="5%"><?php echo JText::_( 'COM_EASYBLOG_THEME_VERSION' );?></th>
				<th class="center nowrap" width="10%"><?php echo JText::_( 'COM_EASYBLOG_THEME_UPDATED' );?></th>
				<th class="center nowrap" width="10%"><?php echo JText::_( 'COM_EASYBLOG_THEME_AUTHOR' );?></th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; ?>
			<?php foreach( $this->themes as $theme ){ ?>
			<tr>
				<td class="center small">
					<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=themes&task=makedefault&element=' . $theme->element . '&' . EasyBlogHelper::getToken() . '=1' );?>" class="btn btn-micro jgrid">
						<?php if( $this->default == $theme->element ){ ?>
						<i class="icon-star"></i>
						<?php } else { ?>
						<i class="icon-star-empty"></i>
						<?php } ?>
					</a>
				</td>
				<td width="1%">
					<input type="radio" class="inputbox" name="element" value="<?php echo $this->escape( $theme->name );?>" />
				</td>
				<td>
					<img src="<?php echo JURI::root();?>components/com_easyblog/themes/<?php echo $this->escape( $theme->element );?>/preview.png" style="border: 1px solid #ccc;" />
				</td>
				<td>
					<a href="index.php?option=com_easyblog&view=theme&element=<?php echo strtolower( $this->escape( $theme->element ) ); ?>"><?php echo JText::_($this->escape( $theme->name ) );?></a>						
					<div class="small">
						<?php echo strip_tags( JText::_( $theme->desc ) ); ?>
					</div>
				</td>
				<td class="center small">
					<?php echo $theme->version; ?>
				</td>
				<td class="center small">
					<?php echo $theme->updated;?>
				</td>
				<td class="center small">
					<?php echo $theme->author; ?>
				</td>
			</tr>
				<?php $i += 1; ?>
			<?php }?>
		</tbody>
		</table>

	</div>

</div>
