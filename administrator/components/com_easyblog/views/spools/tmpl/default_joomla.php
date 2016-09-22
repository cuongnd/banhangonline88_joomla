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
    		  	<label><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_SEARCH' ); ?> :</label>
    			<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
    		</td>
    		<td width="50%" style="text-align: right;">
                <label><?php echo JText::_( 'COM_EASYBLOG_CATEGORIES_FILTER_BY' ); ?> :</label>
                <?php echo $this->state; ?>
    		</td>
    	</tr>
    </table>
</div>
<div class="adminform-body">
<p class="small"><?php echo JText::_( 'COM_EASYBLOG_SPOOLS_TIPS' ); ?> <a href="http://stackideas.com/docs/easyblog/how-tos/138-setting-up-cronjobs-in-cpanel" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETUP_CRON' );?></a></p>
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1%"><?php echo JText::_( 'Num' ); ?></th>
		<th width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->mails ); ?>);" /></th>
		<th class="title" style="text-align: left;" width="10%"><?php echo JText::_( 'COM_EASYBLOG_RECIPIENT' ); ?></th>
		<th><?php echo JText::_( 'COM_EASYBLOG_SUBJECT' ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_STATE' ); ?></th>
		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYBLOG_CREATED' ); ?></th>
		<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->mails )
{

	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->mails); $i < $n; $i++)
	{
		$row		= $this->mails[$i];
		$date 		= EasyBlogHelper::getHelper( 'Date' )->getDate( $row->created );
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
		<td><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
		<td><?php echo $row->recipient;?></td>
		<td>
			<a href="javascript:void(0);" onclick="admin.spools.preview('<?php echo $row->id;?>');"><?php echo $row->subject;?></a>
		</td>
		<td style="text-align:center;">
			<?php if( $row->status ){ ?>
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/tick.png" title="<?php echo JText::_( 'COM_EASYBLOG_SENT' );?>">
			<?php } else { ?>
				<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/schedule.png" title="<?php echo JText::_( 'COM_EASYBLOG_PENDING' );?>">
			<?php } ?>
		</td>
		<td style="text-align:center;">
			<?php echo $date->toMySQL( true ); ?>
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
			<?php echo JText::_('COM_EASYBLOG_NO_MAILS');?>
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
