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
	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'Num' ); ?></th>
			<th colspan="2" style="text-align:left;"><?php echo JText::_( 'COM_EASYBLOG_THEME_NAME' );?></th>
			<th class="title" width="5%"><?php echo JText::_( 'COM_EASYBLOG_THEME_DEFAULT' );?></th>
			<th class="title" width="5%"><?php echo JText::_( 'COM_EASYBLOG_THEME_VERSION' );?></th>
			<th class="title" width="10%"><?php echo JText::_( 'COM_EASYBLOG_THEME_UPDATED' );?></th>
			<th class="title" width="10%"><?php echo JText::_( 'COM_EASYBLOG_THEME_AUTHOR' );?></th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0; ?>
		<?php foreach( $this->themes as $theme ){ ?>
		<tr class="row0">
			<td style="text-align:center;">
				<?php echo $i + 1; ?>
			</td>
			<td width="1%">
				<input type="radio" class="inputbox" name="element" value="<?php echo $this->escape( $theme->name );?>" />
			</td>
			<td>
				<div class="has-tip">
					<div class="tip" style="text-align:center;"><i></i><img src="<?php echo JURI::root();?>components/com_easyblog/themes/<?php echo $this->escape( $theme->name );?>/preview.png" style="border: 1px solid #ccc;" /></div>
					<a href="index.php?option=com_easyblog&view=theme&element=<?php echo strtolower( $this->escape( $theme->name ) ); ?>"><?php echo JText::_( $this->escape( $theme->name ) );?></a>
				</div>
				
			</td>
			<td style="text-align:center;">
				<a href="<?php echo JRoute::_( 'index.php?option=com_easyblog&c=themes&task=makedefault&element=' . $theme->name . '&' . EasyBlogHelper::getToken() . '=1' );?>">
				<?php if( $this->default == $theme->name ){ ?>
					<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/default.png" />
				<?php } else { ?>
					<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/nodefault.png" />
				<?php } ?>
				</a>
			</td>
			<td style="text-align:center;">
				<?php echo $theme->version; ?>
			</td>
			<td style="text-align:center;">
				<?php echo $theme->updated;?>
			</td>
			<td style="text-align:center;">
				<?php echo $theme->author; ?>
			</td>
		</tr>
			<?php $i += 1; ?>
		<?php }?>
	</tbody>
	</table>
</div>
