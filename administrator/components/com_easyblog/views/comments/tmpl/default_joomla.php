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
<div class="adminform-head">
    <table class="adminform">
    	<tr>
    		<td width="50%">
                <label><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_SEARCH' ); ?> :</label>
    			<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
    		</td>
    		<td width="50%" style="text-align: right;">
    			<label><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_FILTER_BY' );?> :</label>
                <?php echo $this->state; ?>
    		</td>
    	</tr>
    </table>
</div>
<div class="adminform-body">
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1%"><?php echo JText::_( 'Num' ); ?></th>
		<th width="1%"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->comments ); ?>);" /></th>
		<th class="title" style="text-align: left;"><?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT');?></th>
		<th class="title" width="20%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_BLOG_TITLE' ), 'blog_name', $this->orderDirection, $this->order ); ?></th>
		<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_PUBLISHED' ); ?></th>
		<th class="title" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_DATE' ), 'created', $this->orderDirection, $this->order ); ?></th>
		<th class="title" width="15%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_COMMENTS_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
		<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->comments )
{
	$k = 0;
	$x = 0;
	$config	= JFactory::getConfig();
	for ($i=0, $n=count($this->comments); $i < $n; $i++)
	{
		$row			= $this->comments[$i];
		$date			= EasyBlogDateHelper::getDate($row->created);
		$link 			= 'index.php?option=com_easyblog&amp;c=comment&amp;task=edit&amp;commentid='. $row->id;
		$userlink		= JURI::root() . 'index.php?option=com_easyblog&amp;view=blogger&amp;layout=listBlogs&amp;id='. $row->created_by;
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td style="text-align:center;">
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<td>
			<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
		</td>
		<td align="left">
			<span class="editlinktip hasTip">
				<?php if( !empty( $row->title) ){ ?>
					<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
				<?php }else{ ?>
					<a href="<?php echo $link; ?>"><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_NO_TITLE'); ?></a>
				<?php } ?>
			</span>
			<div>
				<?php echo $row->comment;?>
			</div>
		</td>
		<td align="center">
			<a href="<?php echo JURI::root() . 'index.php?option=com_easyblog&amp;view=entry&amp;id=' . $row->post_id; ?>" target="_blank"><?php echo $row->blog_name; ?></a>
		</td>
		<td align="center">
			<?php if( $row->isModerate ) { ?>
				<?php if( EasyBlogHelper::getJoomlaVersion() <= '1.5' ){ ?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','publish')"><img src="<?php echo JURI::base() . 'components/com_easyblog/assets/images/pending.png';?>" width="16" height="16" border="0" /></a>
				<?php } else { ?>
					<?php echo JHTML::_( 'grid.boolean' , $i , $row->published , 'publish' , 'publish' ); ?>
				<?php } ?>
			<?php } else { ?>
				<?php echo JHTML::_('grid.published', $row, $i ); ?>
			<?php } ?>
		</td>

		<td align="center">
			<?php echo $date->toMySQL( true );?>
		</td>
		<td align="center">
			<?php if ( $row->created_by == 0 ) : ?>
				<?php echo $row->name; ?>
			<?php else : ?>
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $row->name; ?></a>
			<?php endif; ?>
		</td>
		<td align="center"><?php echo $row->id; ?></td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<td colspan="8" align="center">
			<?php echo JText::_('COM_EASYBLOG_COMMENTS_NO_COMMENT_YET');?>
		</td>
	</tr>
<?php
}
?>
</tbody>

<tfoot>
	<tr>
		<td colspan="11">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>