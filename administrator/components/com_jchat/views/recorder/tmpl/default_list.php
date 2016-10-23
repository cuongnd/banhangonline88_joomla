<?php 
/** 
 * @package JCHAT::RECORDER::administrator::components::com_jchat
 * @subpackage views
 * @subpackage recorder
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
JHTML::_('behavior.tooltip'); ?>
 
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="headerlist">
		<tr>
			<td class="left">
				<div class="input-prepend">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_FILTER' ); ?>:</span>
					<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->searchword, ENT_COMPAT, 'UTF-8');?>" class="text_area"/>
				</div>
				<button class="btn btn-primary btn-mini" onclick="this.form.submit();"><?php echo JText::_('COM_JCHAT_GO' ); ?></button>
				<button class="btn btn-primary btn-mini" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('COM_JCHAT_RESET' ); ?></button>
				
				<div class="clr vspacer"></div>
				<div class="input-prepend active">
					<span class="add-on"><span class="icon-calendar"></span> <?php echo JText::_('COM_JCHAT_FILTER_BY_DATE_FROM' ); ?>:</span>
					<input type="text" name="fromperiod" id="fromPeriod" data-role="calendar" value="<?php echo $this->dates['start'];?>" class="text_area"/>
				</div>
				
				<div class="input-prepend active">
					<span class="add-on"><span class="icon-calendar"></span> <?php echo JText::_('COM_JCHAT_FILTER_BY_DATE_TO' ); ?>:</span>
					<input type="text" name="toperiod" id="toPeriod" data-role="calendar" value="<?php echo $this->dates['to'];?>" class="text_area"/>
				</div>
				<button class="btn btn-primary btn-mini" onclick="document.adminForm.task.value='recorder.display';this.form.submit();"><?php echo JText::_('COM_JCHAT_GO' ); ?></button>
				<button class="btn btn-primary btn-mini" onclick="document.getElementById('fromPeriod').value='';document.getElementById('toPeriod').value='';this.form.submit();"><?php echo JText::_('COM_JCHAT_RESET' ); ?></button>
			</td>
			<td class="right">
				<div class="input-prepend active hidden-phone">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_STATE' ); ?></span>
					<?php
						echo $this->pagination->getLimitBox();
					?>
				</div>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped table-hover">
		<thead>
			<tr>
				<th width="1%" class="title">
					<?php echo JText::_('COM_JCHAT_NUM' ); ?>
				</th>
				<th width="1%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th width="5%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_RECORD_MEDIA_TITLE', 'a.title', @$this->orders['order_Dir'], @$this->orders['order'], 'recorder.display' ); ?>
				</th>
				<th width="5%" class="title hidden-phone">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_RECORD_MEDIA_SIZE', 'a.size', @$this->orders['order_Dir'], @$this->orders['order'], 'recorder.display' ); ?>
				</th>
				<th width="5%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_RECORD_MEDIA_TIMERECORD', 'a.timerecord', @$this->orders['order_Dir'], @$this->orders['order'], 'recorder.display' ); ?>
				</th>
				<th width="8%" class="title hidden-phone hidden-tablet">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_RECORD_MEDIA_PEER1', 'a.peer1', @$this->orders['order_Dir'], @$this->orders['order'], 'recorder.display' ); ?>
				</th>
				<th width="8%" class="title hidden-phone hidden-tablet" >
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_RECORD_MEDIA_PEER2', 'a.peer2', @$this->orders['order_Dir'], @$this->orders['order'], 'recorder.display' ); ?>
				</th>  
				
				<th width="8%" class="title" >
					<?php echo JText::_('COM_JCHAT_RECORD_MEDIA_PLAY'); ?>
				</th>
				<th width="8%" class="title hidden-phone" >
					<?php echo JText::_('COM_JCHAT_RECORD_MEDIA_DOWNLOAD'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="100%">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
				$row = $this->items[$i]; 
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td>
					<a href="<?php echo JUri::root() . 'media/com_jchat/recordings/' . $row->title;?>" target="_blank">
						<?php echo $row->title; ?>
						<span class="icon-out"></span>
					</a>
				</td>
				<td class="hidden-phone">
					<?php echo $row->size; ?>
				</td>
				<td>
					<?php echo $row->timerecord; ?>
				</td>
				<td class="hidden-phone hidden-tablet">
					<?php echo $row->peer1; ?>
				</td>
				<td class="hidden-phone hidden-tablet">
					<?php echo $row->peer2; ?>
				</td>
				
				<td> 
					<a onclick="window.open('<?php echo JUri::root() . 'media/com_jchat/recordings/' . $row->title;?>', 'video', 'width=640,height=480');return false;">
						<span class="icon-play-2 jchatbtn"></span>
					</a>
				</td>
				<td class="hidden-phone"> 
					<a href="javascript:void(0);" onclick="listItemTask('cb<?php echo $i;?>','recorder.downloadEntity');document.querySelector('#task').value='recorder.display';">
						<span class="icon-download jchatbtn"></span>
					</a>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" id="task" name="task" value="recorder.display" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $this->orders['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orders['order_Dir']; ?>" /> 
</form>