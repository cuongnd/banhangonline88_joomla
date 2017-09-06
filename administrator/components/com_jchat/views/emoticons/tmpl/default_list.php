<?php 
/** 
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage views
 * @subpackage emoticons
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Ordering drag'n'drop management
if ($this->orders['order'] == 'ordering') {
	$saveOrderingUrl = 'index.php?option=com_jchat&task=emoticons.saveOrder&format=json&ajax=1';
	JHtml::_('sortablelist.sortable', 'adminList', 'adminForm', strtolower($this->orders['order_Dir']), $saveOrderingUrl);
	$this->document->addScript ( JUri::root ( true ) . '/administrator/components/com_jchat/js/sortablelist.js', 'text/javascript', true );
}
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="full headerlist">
		<tr>
			<td id="alert_append" class="right">
				<div class="input-prepend active hidden-phone">
					<span class="add-on"><span class="icon-filter"></span> <?php echo JText::_('COM_JCHAT_STATE' ); ?></span>
					<?php
						echo $this->lists['state'];
						echo $this->pagination->getLimitBox();
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="100%">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</table>

	<table id="adminList" class="adminlist emoticons table table-striped table-hover">
	<thead>
		<tr>
			<th style="width:1%">
				<?php echo JText::_('COM_JCHAT_NUM' ); ?>
			</th>
			<th style="width:2%">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th style="width:10%">
				<?php echo JText::_('COM_JCHAT_EMOTICON_IMAGE' ); ?>
			</th>
			<th style="width:48%">
				<?php echo JText::_('COM_JCHAT_EMOTICON_LINKURL' ); ?>
			</th>
			<th style="width:12%" class="hidden-phone">
				<?php echo JText::_('COM_JCHAT_EMOTICON_KEYCODE' ); ?>
			</th>
			<th style="width:12%">
				<?php echo JText::_('COM_JCHAT_EMOTICON_SAVE' ); ?>
			</th>
			<th class="order hidden-phone">
				<?php echo JHTML::_('grid.sort', 'COM_JCHAT_ORDER', 'ordering', @$this->orders['order_Dir'], @$this->orders['order'], 'emoticons.display'); ?>
				<?php 
					if(isset($this->orders['order']) && $this->orders['order'] == 'ordering'):
						echo JHTML::_('grid.order',  $this->items, 'filesave.png', 'emoticons.saveOrder'); 
					endif;
				 ?>
			</th>
			<th style="width:25%">
				<?php echo JText::_('COM_JCHAT_PUBLISHED' ); ?>
			</th>
			<th style="width:1%">
				<?php echo JText::_('COM_JCHAT_ID' ); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
		$row = $this->items[$i];
		$checked = '<input type="checkbox" id="cb' . $i . '" name="cid[]" value="' . $row->id . '"/>';
		?>
		<tr>
			<td align="center">
				<?php echo $i + 1; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<img data-mediapreview="1" src="<?php echo JUri::root(false) . $row->linkurl;?>"/>
			</td>
			<td class="emoticonimage" align="center">
				<?php
					$this->mediaField->value = null;
					if(isset($row->linkurl) && $row->linkurl) {
						$this->mediaField->value = $row->linkurl;
					}
					$this->mediaField->id = 'jform_media_identifier_' . ($row->id);
					$this->mediaField->name = 'jform_media_identifier_' . ($row->id);
					$this->mediaField->dataIdentifier = ($row->id);
					if(method_exists($this->mediaField, 'renderField')) {
						echo $this->mediaField->renderField(); // Joomla 3.3+
					} elseif(method_exists($this->mediaField, 'getControlGroup')) {
						echo $this->mediaField->getControlGroup(); // Joomla 3.2
					} else { }
				?>
			</td>
			<td align="center" class="hidden-phone">
				<input style="width:80px" data-keycode="<?php echo $row->id;?>" value="<?php echo $row->keycode;?>"/>
			</td>
			<td align="center">
				<button class="btn btn-primary" data-action="save_emoticon" data-save="<?php echo $row->id;?>"><span class="icon-save"></span> <?php echo JText::_('COM_JCHAT_EMOTICON_SAVENOW');?></button>
			</td>
			
			<td class="order hidden-phone">
				<?php 
				$ordering = $this->orders['order'] == 'ordering'; 
				$disabled = $ordering ?  '' : 'disabled="disabled"';
				
				$iconClass = '';
				if (!$ordering) {
					$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
				}
				?>
				<div style="display:inline-block" class="sortable-handler<?php echo $iconClass ?>">
					<span class="icon-menu"></span>
				</div>
				
				<span class="moveup"><?php echo $this->pagination->orderUpIcon( $i, true, 'emoticons.moveorder_up', 'COM_JCHAT_MOVE_UP', $ordering); ?></span>
				<span class="movedown"><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'emoticons.moveorder_down', 'COM_JCHAT_MOVE_DOWN', $ordering); ?></span>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  <?php echo $disabled; ?>  class="ordering_input" style="text-align: center" />
			</td>
			
			<td align="center">
				<fieldset class="radio btn-group" data-action="state_emoticon" data-state="<?php echo $row->id;?>">
					<?php 
						$published = isset($row->published) ? $row->published : 1;
						echo JHTML::_ ( 'select.booleanlist', 'published' . $row->id, null, $published);
					?>
				</fieldset>
			</td>
			<td>
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
	}
	?>
	</tbody>
	</table>

	<input type="hidden" name="section" value="view" />
	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="emoticons.display" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="filter_order" value="<?php echo @$this->orders['order'];?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo @$this->orders['order_Dir'];?>" />
</form>