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
																														
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_TPL_TYPE_COMPONENT', 'a.component', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																			
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_TPL_TYPE_TYPE', 'a.type', $listDirn, $listOrder); ?>
								</th>
												
						
																																																									<th>
						<?php echo JHtml::_('searchtools.sort', 'Template Set', 'a.title', $listDirn, $listOrder); ?>
					</th>
																																																																																		
												
																					<th width="140px" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_TPL_TYPE_COUNT_TEMPLATES', 'a.count_templates', $listDirn, $listOrder); ?>
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
			
												
																	
						
													<td class="center small">
																<?php //echo empty($item->component) ? 'Not in Data : component' : $item->component; ?>
								<?php echo $item->component; ?>
															</td>
												
						
												
						
																																																									
												
																													
						
													<td class="center small">
																<?php //echo empty($item->type) ? 'Not in Data : type' : $item->type; ?>
								<?php echo $item->type; ?>
															</td>
												
						
												
						
																																													
												<td>
				
								
								
														<?php echo $this->escape($item->title); ?>
								
						
						
											</td>
							
				
												
																																																																	
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjcount'))
									{
										$count_templates = new JFormFieldfsjcount();
										$count_templates->fsjcount = json_decode('{"field":"type","table":"tpl_template","target":"index.php?option=com_fsj_main&view=tpls&filter_type=%TYPE%","key":"type","image":"libraries\/fsj_core\/assets\/images\/general\/edit-16.png","display":"View Templates (%COUNT%)"}');
										echo $count_templates->AdminDisplay(!empty($item->count_templates) ? $item->count_templates : '', 'count_templates', $item, $i);
									} else {
										echo $item->count_templates;
									}
								?>
															</td>
												
						
												
						
									
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
	
	