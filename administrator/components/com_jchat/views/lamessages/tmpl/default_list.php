<?php 
/** 
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage lamessages
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
?>
 
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
				<button class="btn btn-primary btn-mini" onclick="document.adminForm.task.value='lamessages.display';this.form.submit();"><?php echo JText::_('COM_JCHAT_GO' ); ?></button>
				<button class="btn btn-primary btn-mini" onclick="document.getElementById('fromPeriod').value='';document.getElementById('toPeriod').value='';this.form.submit();"><?php echo JText::_('COM_JCHAT_RESET' ); ?></button>
			</td>
			<td class="right">
				<div class="input-prepend active hidden-phone">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_STATE' ); ?></span>
					<?php
						echo $this->lists['answered'];
						echo $this->lists['closed'];
						echo $this->pagination->getLimitBox();
					?>
				</div>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped" cellpadding="1">
		<thead>
			<tr>
				<th width="1%" class="title">
					<?php echo JText::_( 'COM_JCHAT_NUM' ); ?>
				</th>
				<th width="1%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th width="8%"class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_LAMESSAGE_NAME', 'a.name', @$this->orders['order_Dir'], @$this->orders['order'], 'lamessages.display' ); ?>
				</th>
				<th width="8%" class="title" >
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_LAMESSAGE_EMAIL', 'a.email', @$this->orders['order_Dir'], @$this->orders['order'], 'lamessages.display' ); ?>
				</th>  
				<th width="25%" class="title" nowrap="nowrap">
					<?php echo JText::_('COM_JCHAT_MESSAGE'); ?>
				</th>
				<th width="5%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_SENT', 'a.sentdate', @$this->orders['order_Dir'], @$this->orders['order'], 'lamessages.display' ); ?>
				</th>
				<th width="5%" class="title">
					<?php echo JText::_('COM_JCHAT_NUM_REPLIES');?>
				</th>
				<th width="5%" class="title">
					<?php echo JText::_('COM_JCHAT_LAST_REPLY');?>
				</th>
				<th width="5%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_USERID', 'a.userid', @$this->orders['order_Dir'], @$this->orders['order'], 'lamessages.display' ); ?>
				</th>
				<th width="5%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_WORKED', 'a.worked', @$this->orders['order_Dir'], @$this->orders['order'], 'lamessages.display' ); ?>
				</th> 
				<th width="5%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_CLOSED_TICKET', 'a.closed_ticket', @$this->orders['order_Dir'], @$this->orders['order'], 'lamessages.display' ); ?>
				</th> 
				<th width="1%" class="title" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->orders['order_Dir'], @$this->orders['order'], 'lamessages.display' ); ?>
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
				$row = & $this->items[$i]; 
				
				// Try to find num responses and last response info
				$numResponses = 0;
				$lastResponse = array('date'=>null, 'user'=>null);
				if(!empty($row->responses)) {
					$responses = unserialize($row->responses);
					$numResponses = count($responses);
					foreach ($responses as $response) {
						if($lastResponse['date'] < $response[0]) {
							$lastResponse = array('date'=>$response[0], 'user'=>$response[2]);
						}
					}
				}
				
				// Read status
				$imgRead 	= $row->worked ? 'icon-16-tick.png' : 'icon-16-publish_x.png'; 
				$altRead 	= $row->worked ? JText::_( 'COM_JCHAT_READ' ) : JText::_( 'COM_JCHAT_UNREAD' );
				$taskState 	= $row->worked ? 'lamessages.workedFlagOff()' : 'lamessages.workedFlagOn()';
				
				// Closed status
				$imgClosed 	= $row->closed_ticket ? 'icon-16-tick.png' : 'icon-16-publish_x.png';
				$altClosed 	= $row->closed_ticket ? JText::_( 'COM_JCHAT_CLOSED_TICKET' ) : JText::_( 'COM_JCHAT_NOTCLOSED_TICKET' );
				$taskClosed = $row->closed_ticket ? 'lamessages.closedFlagOff()' : 'lamessages.closedFlagOn()';
				
				// Accorciamento message text
				$row->message = strlen($row->message) > 80 ? substr($row->message, 0, 80) . '...' : $row->message;
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
					<a class="clearopen badge badge-info" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','lamessages.editEntity')">
						<?php echo $i+1+$this->pagination->limitstart;?>
					</a>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td> 
					<?php echo $row->name; ?> 
				</td>
				<td>
					<?php echo $row->email; ?>
				</td> 
				<td>
					<?php echo $row->message; ?>
				</td>    
				<td>
					<?php echo $row->sentdate; ?>
				</td>
				<td>
					<?php echo $numResponses; ?>
				</td>
				<td>
					<?php echo $lastResponse['date'] . ' / ' . $lastResponse['user']; ?>
				</td>
				<td>
					<?php echo isset($row->username_logged) ? $row->username_logged : JText::_('COM_JCHAT_ANONYMOUS'); ?>
				</td>
				<td align="center"> 
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $taskState;?>')">
						<img src="components/com_jchat/images/<?php echo $imgRead;?>" width="16" height="16" border="0" alt="<?php echo $altRead; ?>" />   
					</a>
				</td> 
				<td align="center"> 
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $taskClosed;?>')">
						<img src="components/com_jchat/images/<?php echo $imgClosed;?>" width="16" height="16" border="0" title="<?php echo $altClosed; ?>" alt="<?php echo $altClosed; ?>" />   
					</a>
				</td> 
				<td>
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="lamessages.display" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $this->orders['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orders['order_Dir']; ?>" /> 
</form>