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
    		<td width="35%">
    		  	<label><?php echo JText::_( 'COM_EASYBLOG_BLOGS_SEARCH' ); ?> :</label>
    			<input type="text" name="search" id="search" value="<?php echo $this->search; ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
    		</td>
    		<td width="45%">&nbsp;</td>
    		<td width="25%" style="text-align: right;">
    			<?php echo JText::_( 'COM_EASYBLOG_FEEDS_FILTER_BY' ); ?> :
    			<?php echo $this->state; ?>
    		</td>
    	</tr>
    </table>
</div>

<div class="adminform-body">

	<div style="width:750px;">
		<div id="progress-bar" style="display:none;">
		    <div class="bar-holder">
		        <div class="bar-progress" id="bar-progress" style="width:0%;"></div>
		    </div>
		</div>
		<span id="feeds-msg" class="msg-in info" style="display:none;"><?php echo JText::_('COM_EASYBLOG_FEEDS_MIGRATE_NOTES'); ?></span>
	</div>

	<div class="notice"><?php echo JText::_('COM_EASYBLOG_FEEDS_PROCESSING_NOTES');?></div>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="1%"><?php echo JText::_( 'Num' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->feeds ); ?>);" /></th>
			<th class="title" style="text-align: left;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_TITLE' ); ?></th>
			<th width="45%" style="text-align: left;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_URL' ); ?></th>
			<th width="8%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_LAST_IMPORT' ); ?></th>
			<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_PUBLISHED' ); ?></th>
			<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->feeds )
	{

		$k = 0;
		$x = 0;
		for ($i=0, $n=count($this->feeds); $i < $n; $i++)
		{
			$row = $this->feeds[$i];
			$link 			= 'index.php?option=com_easyblog&amp;c=feeds&amp;task=edit&amp;cid='. $row->id;
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
			<td align="left">
				<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			</td>
			<td>
				<a href="<?php echo $row->url; ?>" target="_blank"><?php echo $row->url; ?></a>
			</td>
			<td align="center">
				<?php
				if( $row->last_import == '0000-00-00 00:00:00' )
				{
					echo JText::_( 'COM_EASYBLOG_NEVER' );
				}
				else
				{
					$date	= EasyBlogHelper::getDate();

					echo $date->toMySQL();
				}
				?>
			</td>
			<td align="center">
				<?php echo JHTML::_('grid.published', $row, $i ); ?>
			</td>
			<td align="center"><?php echo $row->id;?></td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php
	}
	else
	{
	?>
		<tr>
			<td colspan="7" align="center">
				<?php echo JText::_('COM_EASYBLOG_FEEDS_NO_FEEDS_YET');?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>

</div>