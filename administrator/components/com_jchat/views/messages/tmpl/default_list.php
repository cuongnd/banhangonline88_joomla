<?php 
/** 
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage messages
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
				<button class="btn btn-primary btn-mini" onclick="document.adminForm.task.value='messages.display';this.form.submit();"><?php echo JText::_('COM_JCHAT_GO' ); ?></button>
				<button class="btn btn-primary btn-mini" onclick="document.getElementById('fromPeriod').value='';document.getElementById('toPeriod').value='';this.form.submit();"><?php echo JText::_('COM_JCHAT_RESET' ); ?></button>
			</td>
			<td class="right">
				<div class="input-prepend active hidden-phone">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_STATE' ); ?></span>
					<?php
						echo $this->lists['type'];
						echo $this->lists['status'];
						echo $this->lists['rooms'];
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
				<th width="8%"class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_SENDER_NAME', 'a.actualfrom', @$this->orders['order_Dir'], @$this->orders['order'], 'messages.display' ); ?>
				</th>
				<th width="8%" class="title" >
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_RECEIVER_NAME', 'a.actualto', @$this->orders['order_Dir'], @$this->orders['order'], 'messages.display' ); ?>
				</th>  
				<th width="25%" class="title" nowrap="nowrap">
					<?php echo JText::_('COM_JCHAT_MESSAGE'); ?>
				</th>
				<th width="5%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_SENT', 'a.sent', @$this->orders['order_Dir'], @$this->orders['order'], 'messages.display' ); ?>
				</th>
				<th width="2%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_READ', 'a.read', @$this->orders['order_Dir'], @$this->orders['order'], 'messages.display' ); ?>
				</th>
				<th width="5%" class="title hidden-phone">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_TYPE', 'a.type', @$this->orders['order_Dir'], @$this->orders['order'], 'messages.display' ); ?>
				</th>
				<th width="5%" class="title hidden-phone">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_ROOM', 'r.name', @$this->orders['order_Dir'], @$this->orders['order'], 'messages.display' ); ?>
				</th>
				<th width="5%" class="title hidden-phone">
					<?php echo JText::_('COM_JCHAT_IPADDRESS'); ?>
				</th>
				<th width="1%" class="title hidden-phone" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_ID', 'a.id', @$this->orders['order_Dir'], @$this->orders['order'], 'messages.display' ); ?>
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
				
				// Read status
				$imgRead 	= $row->read ? 'icon-16-tick.png' : 'icon-16-publish_x.png'; 
				$altRead 	= $row->read ? JText::_('COM_JCHAT_READ' ) : JText::_('COM_JCHAT_NOTREAD' );
 
				$imgFileDownloaded 	= $row->status ? 'icon-16-download-tick.png' : 'icon-16-download-notick.png';
				$altFileDownloaded 	= $row->status ? JText::_('COM_JCHAT_DOWNLOADED' ) : JText::_('COM_JCHAT_NOTDOWNLOADED' );
				 
				// Sent datetime formatting
				$sentDateTime = JHTML::_('date', $row->sent, 'Y-m-d H:i:s');
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
					<a class="clearopen badge badge-info" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','messages.showEntity')">
						<?php echo $i+1+$this->pagination->limitstart;?>
					</a>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td> 
					<?php echo $row->actualfrom; ?> 
				</td>
				<td>
					<?php echo $row->actualto ? $row->actualto : JText::_('COM_JCHAT_MULTIPLE_RECEIVER_USERS'); ?>
				</td> 
				<td style="position:relative">
					<div style="width:98%; max-height:100px; overflow:auto"><?php echo $row->message; ?></div>
					<?php 
					if($this->componentParams->get('language_translation_enabled', 0)):
						$encodedMessage = rawurlencode(strip_tags($row->message)); 
						$translateToImageFlag = JUri::root(false) . $this->defaultFlagsPath . $this->defaultTranslateToLanguage . '.gif';?>
						<a target="_blank" href="https://translate.google.it/#auto/<?php echo $this->defaultTranslateToLanguage . '/' . $encodedMessage;?>">
							<img class='google_translate' alt="translate" title="<?php echo JText::_('COM_JCHAT_CLICKTO_GOOGLE_TRANSLATE');?>" src='<?php echo $translateToImageFlag;?>' />
						</a>
					<?php endif;?>
				</td>    
				<td>
					<?php echo $sentDateTime; ?>
				</td>
				<td align="center"> 
					<?php if($row->actualto):?>
						<img src="components/com_jchat/images/<?php echo $imgRead;?>" width="16" height="16" border="0" alt="<?php echo $altRead; ?>" />
					<?php else:
						echo JText::_('COM_JCHAT_ND');
						endif;
					?>
				</td>
				<td class="hidden-phone"> 
					<?php echo $row->type == 'message' ? JText::_('COM_JCHAT_TIPO_TEXT') : JText::_('COM_JCHAT_TIPO_FILE') . "<img class='inner_spia' src='components/com_jchat/images/$imgFileDownloaded' title='$altFileDownloaded' width='16' height='16' border='0' alt='$altFileDownloaded'/>";?>
				</td>
				<td class="hidden-phone">
					<?php echo $row->roomname; ?>
				</td>
				<td class="hidden-phone">
					<?php echo $row->ipaddress; ?>
				</td>
				<td class="hidden-phone">
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
	<input type="hidden" name="task" value="messages.display" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $this->orders['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orders['order_Dir']; ?>" /> 
</form>