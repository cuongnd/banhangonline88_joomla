<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	13 May 2013
 * @file name	:	views/admproject/tmpl/managemessage.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Manage Private Messages (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 JHtml::_('behavior.framework', true);
 JHtml::_('bootstrap.tooltip');
 
 $doc = JFactory::getDocument();
 $doc->addScript(JURI::root()."components/com_jblance/js/utility.js");
  
 $config = JblanceHelper::getConfig();
 $dformat = $config->dateFormat;
 
 JblanceHelper::setJoomBriToken();
 ?>
 <script type="text/javascript">
<!--
	function showtextarea(msgid){
		//alert($('message_'+msgid).value);
		$('span_message_'+msgid).setStyle('display','none');
		$('btn_save_message_'+msgid).setStyle('display','inline');
		$('btn_edit_message_'+msgid).setStyle('display','none');
		$('message_'+msgid).setStyle('display','block');
		$('chk_attachment_'+msgid).setStyle('display','inline');
	}

	function showSubjectText(msgParentId){
		$('txt_subject_'+msgParentId).setStyle('display','inline');
		$('btn_save_subject_'+msgParentId).setStyle('display','inline-block');
		$('btn_edit_subject_'+msgParentId).setStyle('display','none');
	}
//-->
</script> 
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div class="row-fluid">
		<div class="span6">
			<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admproject&layout=managemessage'); ?>" method="post" name="adminForm" id="adminForm">
				<div id="filter-bar" class="btn-toolbar">
					<div class="filter-search btn-group pull-left">
						<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo htmlspecialchars($this->lists['search']);?>" />
					</div>
					<div class="btn-group pull-left">
						<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
						<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value=''; this.form.submit();"><i class="icon-remove"></i></button>
					</div>
					<div class="btn-group pull-left">
						<?php echo $this->lists['status']; ?>
					</div>
				</div>
				<fieldset>
					<legend><?php echo JText::_('COM_JBLANCE_TITLE_PRIVATE_MESSAGES'); ?></legend>
					<table class="table table-striped">
						<thead>
							<tr>
								<th width="10"><?php echo '#'; ?>
								<th><?php echo JText::_('COM_JBLANCE_FROM'); ?></th>	
								<th><?php echo JText::_('COM_JBLANCE_TO'); ?></th>	
								<th><?php echo JText::_('COM_JBLANCE_SUBJECT'); ?></th>
								<th><?php echo JText::_('COM_JBLANCE_DATE'); ?></th>
								<th><?php echo JText::_('COM_JBLANCE_ACTION'); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="11"><?php echo $this->pageNav->getListFooter(); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
						<?php
						if(count($this->rows) == 0){		//Called if there are no messages -> Shows a text that spreads over the whole table
							?>
							<tr><td colspan='6' align="center"><?php echo JText::_("COM_JBLANCE_INBOX_EMPTY"); ?></td></tr>
						<?php
						}
						$k = 0;
						for ($i=0, $x=count($this->rows); $i < $x; $i++){
							$row = $this->rows[$i];
							$userFrom = JFactory::getUser($row->idFrom);
							$userTo = JFactory::getUser($row->idTo);
							$link_read = JRoute::_('index.php?option=com_jblance&view=admproject&layout=managemessage&cid[]='.$row->id);
							
							$newMsg = JblanceHelper::countUnreadMsg($row->id);
							$unApproved = JblanceHelper::countUnapprovedMsg($row->id);
						?>
							<tr id="jbl_feed_item_<?php echo $row->id; ?>" class="<?php echo "row$k"; ?>">
								<td><?php echo $this->pageNav->getRowOffset($i); ?></td>
						  		<td><a href="<?php echo $link_read; ?>"><?php echo $userFrom->username; ?></a></td>
						  		<td><a href="<?php echo $link_read; ?>"><?php echo $userTo->username; ?></a></td>
								<td><a href="<?php echo $link_read; ?>"><?php echo $row->subject; ?> <?php echo ($newMsg > 0) ? '<span class="label label-info">'.JText::sprintf('COM_JBLANCE_COUNT_NEW', $newMsg).'</span>' : ''; ?><?php echo ($unApproved > 0) ? '<span class="label label-important">'.JText::sprintf('COM_JBLANCE_COUNT_UNAPPROVED', $unApproved).'</span>' : ''; ?></a></td>
								<td nowrap><?php echo JHtml::_('date', $row->date_sent, $dformat, true);?></td>
								<td class="center">
									<a id="feed_hide_<?php echo $row->id; ?>" class="btn btn-micro hasTooltip" onclick="processMessage('<?php echo $row->id; ?>', 'admproject.processmessage');" href="javascript:void(0);" title="<?php echo JText::_('COM_JBLANCE_REMOVE'); ?>"><i class="icon-unpublish"></i></a>
								</td>
							</tr>
						<?php 
							$k = 1 - $k;
						}
						?>
						</tbody>
					</table>
				</fieldset>
				
				<input type="hidden" name="option" value="com_jblance" />
				<input type="hidden" name="view" value="admproject" />
				<input type="hidden" name="layout" value="managemessage" />
				<input type="hidden" name="task" value="" />
			</form>
		</div>
		<?php if(!empty($this->threads)) { ?>
		<div class="span6">
			<fieldset>
				<legend><span id="span_subject_<?php echo $this->threads[0]->id; ?>"><?php echo JText::_('COM_JBLANCE_SUBJECT').': '.$this->threads[0]->subject; ?></span></legend>
				<div class="form-inline">
					<input type="text" class="input-xlarge" id="txt_subject_<?php echo $this->threads[0]->id; ?>" name="txt_subject_<?php echo $this->threads[0]->id; ?>" value="<?php echo $this->threads[0]->subject; ?>" style="display:none; " />
					<a id="btn_edit_subject_<?php echo $this->threads[0]->id; ?>" class="btn btn-mini pull-right" onclick="showSubjectText('<?php echo $this->threads[0]->id; ?>');" href="javascript:void(0);"><?php echo JText::_("COM_JBLANCE_EDIT_SUBJECT"); ?></a>
					<a id="btn_save_subject_<?php echo $this->threads[0]->id; ?>" class="btn btn-mini pull-right" onclick="manageMessage('<?php echo $this->threads[0]->id; ?>', 'subject');" href="javascript:void(0);" style="display:none; "><?php echo JText::_("JAPPLY"); ?></a>
				</div>
				<table class="table table-striped">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_JBLANCE_FROM'); ?></th>	
						<th><?php echo JText::_('COM_JBLANCE_MESSAGE'); ?></th>
						<th><?php echo JText::_('COM_JBLANCE_DATE'); ?></th>
						<th><?php echo JText::_('COM_JBLANCE_ACTION'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$k = 0;
				for ($i=0, $x=count($this->threads); $i < $x; $i++){
					$thread = $this->threads[$i];
					$userFrom = JFactory::getUser($thread->idFrom);
				?>
					<tr id="jbl_feed_item_<?php echo $thread->id; ?>" class="<?php echo "row$k"; ?>">
				  		<td><?php echo $userFrom->username; ?></td>
				  		<td>
				  			<span id="span_message_<?php echo $thread->id; ?>"><?php echo $thread->message; ?></span>
				  			<textarea name="message_<?php echo $thread->id; ?>" id="message_<?php echo $thread->id; ?>" rows="2" cols="10" style="float:left; display: none;"><?php echo $thread->message; ?></textarea>
				  			<a id="btn_edit_message_<?php echo $thread->id; ?>" class="btn btn-micro hasTooltip pull-right" onclick="showtextarea('<?php echo $thread->id; ?>');" href="javascript:void(0);" title="<?php echo JText::_('JACTION_EDIT'); ?>"><i class="icon-edit"></i></a>
				  			<a id="btn_save_message_<?php echo $thread->id; ?>" class="btn btn-micro hasTooltip pull-right" onclick="manageMessage('<?php echo $thread->id; ?>', 'message');" href="javascript:void(0);" style="display: none;" title="<?php echo JText::_('JAPPLY'); ?>"><i class="icon-save"></i></a>	<!-- save button -->
				  			<div class="feed_date small ">
							<!-- Show attachment if found -->
							<?php
							if(!empty($thread->attachment)) : ?>
								<div style="display: inline-block;" id="div_attach_<?php echo $thread->id; ?>">
									<img src="<?php echo JURI::root();?>components/com_jblance/images/attachment.png" />
									<input type="checkbox" name="chk_attachment_<?php echo $thread->id; ?>" id="chk_attachment_<?php echo $thread->id; ?>" value="<?php echo $thread->attachment; ?>" title="<?php echo JText::_('COM_JBLANCE_CHECK_TO_REMOVE_ATTACHMENT'); ?>" class="hasTooltip" style="display:none;" />
									<?php echo LinkHelper::getDownloadLink('message', $thread->id, 'admproject.download'); ?>
								</div>
							<?php	
							endif;
							?>
							</div>
				  		</td>
						<td nowrap><?php echo JHtml::_('date', $thread->date_sent, $dformat, true); ?></td>
						<td>
							<a id="feed_hide_<?php echo $thread->id; ?>" class="btn btn-micro hasTooltip" onclick="processMessage('<?php echo $thread->id; ?>', 'admproject.processmessage');" href="javascript:void(0);" title="<?php echo JText::_('COM_JBLANCE_REMOVE'); ?>"><i class="icon-unpublish"></i></a>
							<?php if($thread->approved == 0) { ?>
							<a id="feed_hide_approve_<?php echo $thread->id; ?>" class="btn btn-micro hasTooltip" onclick="approveMessage('<?php echo $thread->id; ?>');" href="javascript:void(0);" title="<?php echo JText::_('COM_JBLANCE_APPROVE'); ?>"><i class="icon-publish"></i></a>
							<?php } ?>
						</td>
					</tr>
				<?php 
					$k = 1 - $k;
				}
				?>
				</tbody>
				</table>
			</fieldset>
		</div>
		<?php } ?>
	</div>
</div>