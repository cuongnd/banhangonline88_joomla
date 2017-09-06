<?php 
/** 
 * @package JCHAT::USERS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage users
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
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
			</td>
			<td class="right">
				<div class="input-prepend active hidden-phone">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_STATE' ); ?></span>
					<?php
						echo $this->lists['banstatus'];
						echo $this->pagination->getLimitBox();
					?>
				</div>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped table-hover">
		<thead>
			<tr>
				<th width="5%" class="title">
					<?php echo JText::_('COM_JCHAT_NUM' ); ?>
				</th>
				<th width="5%" class="title hidden-tablet hidden-phone">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_JOOMLA_USERID', 'a.id', @$this->orders['order_Dir'], @$this->orders['order'], 'users.display' ); ?>
				</th>  
				<th width="20%" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_USERNAME', 'a.username', @$this->orders['order_Dir'], @$this->orders['order'], 'users.display' ); ?>
				</th>
				<th width="20%" class="title hidden-phone">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_NAME', 'a.name', @$this->orders['order_Dir'], @$this->orders['order'], 'users.display' ); ?>
				</th>
				<th width="20%" class="title hidden-phone">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_EMAIL', 'a.email', @$this->orders['order_Dir'], @$this->orders['order'], 'users.display' ); ?>
				</th>
				<th width="20%">
					<?php echo JHTML::_('grid.sort', 'COM_JCHAT_BANSTATUS', 'u.banstatus', @$this->orders['order_Dir'], @$this->orders['order'], 'users.display' ); ?>
				</th>
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_('COM_JCHAT_AVATAR'); ?>
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
				$taskPublishing	= !isset($row->banstatus) || !$row->banstatus ? 'users.banEntity' : 'users.unbanEntity';
				$altPublishing 	= !isset($row->banstatus) || !$row->banstatus ? JText::_( 'Ban' ) : JText::_( 'Unban' );
				
				// Access check.
				if($this->user->authorise('core.edit.state', 'com_jchat')) {
					$banStatus = '<a href="index.php?option=com_jchat&task=' . $taskPublishing . '&cid[]=' . $row->id . '">';
					$banStatus .= !isset($row->banstatus) || $row->banstatus == 0 ? '<img alt="' . $altPublishing . '" src="' . JURI::base(true) . '/components/com_jchat/images/icon-16-tick.png" width="16" height="16" border="0"/>' :
																					'<img alt="' . $altPublishing . '" src="' . JURI::base(true) . '/components/com_jchat/images/icon-16-deny.png" width="16" height="16" border="0"/>';
					$banStatus .= '</a>';
				} else {
					$banStatus = '<img alt="' . $altPublishing . '" src="' . JURI::base(true) . '/components/com_jchat/images/icon-16-tick.png" width="16" height="16" border="0" alt="unpublish" />';
				}
				?>
					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						
						<td class="title hidden-phone">
							<?php echo $row->id; ?>
						</td>
						
						<td>
							<?php echo $row->username; ?>
						</td>
						
						<td class="title hidden-phone">
							<?php echo $row->name; ?>
						</td>
						
						<td class="title hidden-phone">
							<?php echo $row->email; ?>
						</td>
						
						<td>
							<?php echo $banStatus;?>
						</td>
						
						<td>
							<img src="<?php echo JChatHelpersUsers::getAvatar($row->id, $row->id);?>" alt="useravatar"/>
						</td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="users.display" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $this->orders['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orders['order_Dir']; ?>" /> 
</form>