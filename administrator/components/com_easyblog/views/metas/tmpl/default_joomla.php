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
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="adminform-head">
    <table class="adminform">
    	<tr>
    		<td width="50%">
    		  	<label><?php echo JText::_( 'COM_EASYBLOG_BLOGS_SEARCH' ); ?> :</label>
    			<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
    		</td>
    		<td width="50%" style="text-align:right">
                <label><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_FILTER_BY' );?> :</label>
                <?php echo $this->filter->type; ?>
    		</td>
    	</tr>
    </table>
</div>
<div class="adminform-body">
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1%">
			<?php echo JText::_( 'Num' ); ?>
		</th>
		<th width="1%" align="center" style="text-align: center;">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->meta ); ?>);" />
		</th>
		<th class="title" style="text-align: left;" width="30%"><?php echo JText::_('COM_EASYBLOG_META_TITLE'); ?></th>
		<th class="title" style="text-align: center;" width="5%"><?php echo JText::_('COM_EASYBLOG_META_INDEXING'); ?></th>
		<th class="title" style="text-align: left;" width="30%"><?php echo JText::_('COM_EASYBLOG_META_KEYWORDS'); ?></th>
		<th class="title" style="text-align: left;" width="30%"><?php echo JText::_('COM_EASYBLOG_META_DESCRIPTION'); ?></th>
		<th class="title" style="text-align: left;" width="40"><?php echo JHTML::_('grid.sort', 'Type' , 'type', $this->orderDirection, $this->order ); ?></th>
		<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID' , 'id', $this->orderDirection, $this->order ); ?></th>
	</tr>
</thead>
<tbody>

<?php
if( $this->meta )
{
	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->meta); $i < $n; $i++)
	{
		
		$row = $this->meta[$i];
		
		if ( $row->id != 30 ) {
		
		$link 			= 'index.php?option=com_easyblog&amp;view=meta&amp;id='. $row->id;
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="center">
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>
		<td width="7" align="center" style="text-align: center;">
			<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
		</td>
		<td align="left">
			<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
		</td>
		<td align="center">
			 <?php echo $this->getIndexing( $row, $i ); ?>
		</td>
		<td align="left">
			<?php echo $row->keywords; ?>
		</td>
		<td align="left">
			<?php echo $row->description; ?>
		</td>
		<td align="center">
			<?php echo $row->type; ?>
		</td>
		<td align="center">
			<?php echo $row->id;?>
		</td>

	</tr>
	<?php $k = 1 - $k; 
		}
	} 
	?>
<?php
}
else
{
?>
	<tr>
		<td colspan="8" align="center">
			<?php echo JText::_('COM_EASYBLOG_NO_META_TAGS_INDEXED_YET');?>
		</td>
	</tr>
<?php
}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan="8">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easyblog" />
<input type="hidden" name="view" value="metas" />
<input type="hidden" name="c" value="meta" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>