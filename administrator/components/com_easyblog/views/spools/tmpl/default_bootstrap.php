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

		<div class="span2">
			<h4><?php echo JText::_( 'COM_EASYBLOG_FILTER' ); ?>:</h4>
			<?php echo $this->state; ?>
		</div>

		<div class="span10">
			<div class="filter-bar">
				<div class="filter-search input-append pull-left">
					<label class="element-invisible" for="search"><?php echo JText::_( 'COM_EASYBLOG_SEARCH' ); ?> :</label>
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();"
					placeholder="<?php echo JText::_( 'COM_EASYBLOG_SEARCH' , true ); ?>" />
					<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_SUBMIT_BUTTON' ); ?></button>
					<button class="btn" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYBLOG_RESET_BUTTON' ); ?></button>
				</div>

				<div class="pull-right">
					<pre><?php echo JText::_( 'COM_EASYBLOG_SPOOLS_TIPS' ); ?> <a href="http://stackideas.com/docs/easyblog/how-tos/138-setting-up-cronjobs-in-cpanel" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETUP_CRON' );?></a></pre>
				</div>
			</div>

			<div class="clearfix"></div>

			<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>

					<th><?php echo JText::_( 'COM_EASYBLOG_SUBJECT' ); ?></th>

					<th class="center" width="20%">
						<?php echo JText::_( 'COM_EASYBLOG_RECIPIENT' ); ?>
					</th>

					<th width="1%" class="center nowrap">
						<?php echo JText::_( 'COM_EASYBLOG_STATE' ); ?>
					</th>

					<th width="10%" class="center nowrap">
						<?php echo JText::_( 'COM_EASYBLOG_CREATED' ); ?>
					</th>

					<th width="1%" class="center"><?php echo JText::_( 'COM_EASYBLOG_ID' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if( $this->mails ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $this->mails as $row ){?>
					<?php $date 		= EasyBlogHelper::getHelper( 'Date' )->getDate( $row->created ); ?>
					<tr>
						<td class="center">
							<?php echo JHTML::_('grid.id', $i++, $row->id); ?>
						</td>
						<td>
							<a href="javascript:void(0);" onclick="admin.spools.preview('<?php echo $row->id;?>');"><?php echo $row->subject;?></a>
						</td>
						<td class="center">
							<?php echo $row->recipient;?>
						</td>
						<td class="center">
							<?php if( $row->status ){ ?>
								<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/tick.png" title="<?php echo JText::_( 'COM_EASYBLOG_SENT' );?>">
							<?php } else { ?>
								<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/schedule.png" title="<?php echo JText::_( 'COM_EASYBLOG_PENDING' );?>">
							<?php } ?>
						</td>
						<td class="center">
							<?php echo $date->toMySQL( true ); ?>
						</td>
						<td class="center">
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7" align="center">
							<?php echo JText::_('COM_EASYBLOG_NO_MAILS');?>
						</td>
					</tr>
				<?php } ?>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			</table>
		</div>

	</div>

</div>
