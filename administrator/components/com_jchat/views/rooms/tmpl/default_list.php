<?php 
/** 
 * @package JCHAT::ROOMS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage rooms
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="full headerlist">
		<tr>
			<td class="left">
				<div class="input-prepend active">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_FILTER' ); ?>:</span>
					<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->searchword, ENT_COMPAT, 'UTF-8');?>" class="text_area"/>
				</div>
				<button class="btn btn-primary btn-mini" onclick="this.form.submit();"><?php echo JText::_('COM_JCHAT_GO' ); ?></button>
				<button class="btn btn-primary btn-mini" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_JCHAT_RESET' ); ?></button>
			</td>
			<td class="right">
				<div class="input-prepend active hidden-phone">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_STATE' ); ?></span>
					<?php
						echo $this->lists['state'];
						echo $this->pagination->getLimitBox();
					?>
				</div>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped table-hover">
	<thead>
		<tr>
			<th style="width:1%">
				<?php echo JText::_('COM_JCHAT_NUM' ); ?>
			</th>
			<th style="width:1%">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th style="width:20%" class="title">
				<?php echo JHTML::_('grid.sort',  'COM_JCHAT_NAME', 's.name', @$this->orders['order_Dir'], @$this->orders['order'], 'rooms.display'); ?>
			</th>
			<th class="title hidden-phone">
				<?php echo JText::_('COM_JCHAT_DESCRIPTION'); ?>
			</th>
			<th class="order hidden-phone">
				<?php echo JHTML::_('grid.sort',   'COM_JCHAT_ORDER', 's.ordering', @$this->orders['order_Dir'], @$this->orders['order'], 'rooms.display'); ?>
				<?php 
					if(isset($this->orders['order']) && $this->orders['order'] == 's.ordering'):
						echo JHTML::_('grid.order',  $this->items, 'filesave.png', 'rooms.saveOrder'); 
					endif;
				 ?>
			</th>
			<th style="width:5%">
				<?php echo JHTML::_('grid.sort',   'COM_JCHAT_PUBLISHED', 's.published', @$this->orders['order_Dir'], @$this->orders['order'], 'rooms.display' ); ?>
			</th>
			<th style="width:5%">
				<?php echo JHTML::_('grid.sort',   'COM_JCHAT_ACCESS', 's.access', @$this->orders['order_Dir'], @$this->orders['order'], 'rooms.display' ); ?>
			</th>
			<th style="width:5%">
				<?php echo JHTML::_('grid.sort',   'COM_JCHAT_ID', 's.id', @$this->orders['order_Dir'], @$this->orders['order'], 'rooms.display' ); ?>
			</th>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
		$row = $this->items[$i];
		$link =  'index.php?option=com_jchat&task=rooms.editEntity&cid[]='. $row->id ;
		$taskPublishing	= !$row->published ? 'rooms.publish' : 'rooms.unpublish';
		$altPublishing 	= !$row->published ? JText::_( 'Publish' ) : JText::_( 'Unpublish' );
		$published = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $taskPublishing . '\')">';
		$published .= $row->published ? '<img alt="' . $altPublishing . '" src="' . JURI::base(true) . '/components/com_jchat/images/icon-16-tick.png" width="16" height="16" border="0" alt="unpublish" />' : JHtml::image('admin/publish_x.png', 'publish', '', true);
		$published .= '</a>';
		
		$checked = null;
		// Access check.
		if($this->user->authorise('core.edit', 'com_jchat')) {
			$checked = $row->checked_out && $row->checked_out != $this->user->id ? JHtml::_('jgrid.checkedout', $i, '', $row->checked_out_time, 'rooms.', false) : JHtml::_('grid.id', $i, $row->id);
		} else {
			$checked = '<input type="checkbox" style="display:none" data-enabled="false" id="cb' . $i . '" name="cid[]" value="' . $row->id . '"/>';
		}
		?>
		<tr>
			<td>
				<?php echo $this->pagination->getRowOffset($i); ?>
			</td>
			
			<td>
				<?php echo $checked; ?>
			</td>
			
			<td>
				<?php
				// Access check.
				if ( ($row->checked_out && ( $row->checked_out != $this->user->get ('id'))) || !$this->user->authorise('core.edit', 'com_jchat') ) {
					echo $row->name;
				} else {
					?>
					<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_JCHAT_EDIT_ROOM' ); ?>">
						<?php echo $row->name; ?></a>
					<?php
				}
				?>
			</td>
			
			<td class="hidden-phone">
				<?php echo $row->description; ?>
			</td>
			
			<td class="order hidden-phone">
				<?php 
				$ordering = $this->orders['order'] == 's.ordering'; 
				$disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<span class="moveup"><?php echo $this->pagination->orderUpIcon( $i, true, 'rooms.moveorder_up', 'COM_JCHAT_MOVE_UP', $ordering); ?></span>
				<span class="movedown"><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'rooms.moveorder_down', 'COM_JCHAT_MOVE_DOWN', $ordering); ?></span>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  <?php echo $disabled; ?>  class="ordering_input" style="text-align: center" />
			</td>
					
			<td>
				<?php echo $published;?>
			</td>
			
			<td>
				<?php echo $row->accesslevel;?>
			</td>
			
			<td>
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
	}
	?>
	<tfoot>
		<td colspan="100%">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tfoot>
	</table>

	<input type="hidden" name="section" value="view" />
	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="rooms.display" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo @$this->orders['order'];?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$this->orders['order_Dir'];?>" />
</form>