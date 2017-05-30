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
                <label><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_SEARCH' ); ?> : </label>
                <input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
    		</td>
    		<td width="50%" style="text-align: right;">
    			<?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_FILTER_BY' ); ?> :
    			<?php echo $this->state; ?>
    		</td>
    	</tr>
    </table>
</div>

<div class="adminform-body">

<?php if( $this->users && !$this->browse ) : ?>
<div class="notice"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_DELETE_NOTICE');?></div>
<?php endif; ?>

<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="5"><?php echo JText::_( 'Num' ); ?></th>
		<?php if(empty($this->browse)) : ?>
		<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->users ); ?>);" /></th>
		<?php endif; ?>
		<th style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_NAME' ), 'a.name', $this->orderDirection, $this->order ); ?></th>
		<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_FEATURED' ); ?></th>
		<th style="text-align: center;" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_USERNAME' ) , 'a.username', $this->orderDirection, $this->order ); ?></th>
		<th style="text-align: center;" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_USER_GROUP' ) , 'a.usertype', $this->orderDirection, $this->order ); ?></th>
		<th style="text-align: left;" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_EMAIL' ) , 'a.email', $this->orderDirection, $this->order ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_BLOG_ENTRIES' ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_PREVIEW' ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->users )
{
	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->users); $i < $n; $i++)
	{
		$row = $this->users[$i];

		$editLink 		= 'index.php?option=com_easyblog&amp;c=blogs&amp;task=edit&amp;blogid=' . $row->id;
		$previewLink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $row->id, true, true);
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td style="text-align:center;">
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<?php if(empty($this->browse)) : ?>
		<td width="7">
			<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
		</td>
		<?php endif; ?>
		<td>
		<?php
		if( $this->browse )
		{
			if( $this->browseUID )
			{
		?>
			<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo $this->escape( addslashes( $row->name ) );?>', '<?php echo $this->browseUID;?>');"><?php echo $row->name;?></a>
		<?php
			}
			else
			{
		?>
			<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo $this->escape( addslashes( $row->name ) );?>');"><?php echo $row->name;?></a>
		<?php
			}
		}
		else
		{
		?>
			<a href="index.php?option=com_easyblog&c=user&id=<?php echo $row->id;?>&task=edit"><?php echo $row->name;?></a>
		<?php
		}
		?>
		</td>
		<td width="1%" style="text-align:center;">
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->featured ? 'unfeature' : 'feature';?>')">
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/<?php echo $row->featured ? 'default' : 'nodefault';?>.png" width="16" height="16" border="0" />
			</a>
		</td>
		<td style="text-align:center;"><?php echo $row->username;?></td>
		<td style="text-align:center;"><?php echo (EasyBlogHelper::getJoomlaVersion() >= '1.6') ? $row->usergroups : $row->usertype;?></td>
		<td><?php echo $row->email;?></td>
		<td align="center"><?php echo $this->getPostCount( $row->id );?></td>
		<td width="7" align="center"><?php echo $row->id;?></td>
		<td align="center">
			<a href="<?php echo $previewLink;?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYBLOG_PREVIEW');?></a>
		</td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<td colspan="12" align="center">
			<?php echo JText::_('No user created yet.');?>
		</td>
	</tr>
<?php
}
?>
</tbody>

<tfoot>
	<tr>
		<td colspan="12">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>