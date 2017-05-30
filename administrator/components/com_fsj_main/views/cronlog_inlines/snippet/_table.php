<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>


<table class="table table-striped" id="articleList">
	<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
																																																						
												
																					<th width="15%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_MAIN_CRONLOG_EVENT', 'a.event', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																							
												
																					<th width="100" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_MAIN_CRONLOG_WHENTIME', 'a.whentime', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																			
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_MAIN_CRONLOG_SUCCESS', 'a.success', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																							
												
																					<th width="15%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_MAIN_CRONLOG_RESULT', 'a.result', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																							
												
																					<th width="50%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_MAIN_CRONLOG_LOG', 'a.log', $listDirn, $listOrder); ?>
								</th>
												
						
													

		</tr>
	</thead>

	<tbody>

	<?php foreach ($this->items as $i => $item) :
				$canCreate	= 1;//$user->authorise('core.create',		'com_fsj_main.category.'.$item->catid);
		$canEdit	= 1;//$user->authorise('core.edit',			'com_fsj_main.article.'.$item->id);
		$canCheckin	= 1;//$user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
		$canEditOwn	= 1;//$user->authorise('core.edit.own',		'com_fsj_main.article.'.$item->id) && $item->created_by == $userId;
		$canChange	= 1;//$user->authorise('core.edit.state',	'com_fsj_main.article.'.$item->id) && $canCheckin;
			
		if (array_key_exists("debug",$_GET))
			print_p($item);
		?>
								<tr class="row<?php echo $i % 2; ?>">
						
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>
			
												
																																									
						
													<td class="left small">
																<?php //echo empty($item->event) ? 'Not in Data : event' : $item->event; ?>
								<?php echo $item->event; ?>
															</td>
												
						
												
						
																																																																					
												
																																																					
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjcron'))
									{
										$whentime = new JFormFieldfsjcron();
										$whentime->fsjcron = json_decode('{"type":"runat"}');
										echo $whentime->AdminDisplay(!empty($item->whentime) ? $item->whentime : '', 'whentime', $item, $i);
									} else {
										echo $item->whentime;
									}
								?>
															</td>
												
						
												
						
																																																									
												
																																																																													
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjyesno'))
									{
										$success = new JFormFieldfsjyesno();
										$success->fsjyesno = json_decode('{"custom_yes":"Success","custom_no":"Fail"}');
										echo $success->AdminDisplay(!empty($item->success) ? $item->success : '', 'success', $item, $i);
									} else {
										echo $item->success;
									}
								?>
															</td>
												
						
												
						
																																	
												
																																																																																									
						
													<td class="left small">
																<?php //echo empty($item->result) ? 'Not in Data : result' : $item->result; ?>
								<?php echo $item->result; ?>
															</td>
												
						
												
						
																					
												
																																																																																																					
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjcron'))
									{
										$log = new JFormFieldfsjcron();
										$log->fsjcron = json_decode('{"type":"log"}');
										echo $log->AdminDisplay(!empty($item->log) ? $item->log : '', 'log', $item, $i);
									} else {
										echo $item->log;
									}
								?>
															</td>
												
						
												
						
									
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
	
	