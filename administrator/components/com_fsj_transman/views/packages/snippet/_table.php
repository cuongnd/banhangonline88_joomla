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
												<th>
						<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
																																		
												
														<th width="1%" class="nowrap">	
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_PACKAGE_LANGCODE', 'lf5', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																																																																																											
												
																					<th width="20%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_PACKAGE_AUTHOR', 'a.author', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																																																																																																																																																																																											
												
																					<th width="200px" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_PACKAGE_FILES', 'a.files', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																																																																															
												
																					<th width="130px" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_PACKAGE_MAKEPACKAGE', 'a.makepackage', $listDirn, $listOrder); ?>
								</th>
												
						
																																																													

		</tr>
	</thead>

	<tbody>

	<?php foreach ($this->items as $i => $item) :
				$canCreate	= 1;//$user->authorise('core.create',		'com_fsj_transman.category.'.$item->catid);
		$canEdit	= 1;//$user->authorise('core.edit',			'com_fsj_transman.article.'.$item->id);
		$canCheckin	= 1;//$user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
		$canEditOwn	= 1;//$user->authorise('core.edit.own',		'com_fsj_transman.article.'.$item->id) && $item->created_by == $userId;
		$canChange	= 1;//$user->authorise('core.edit.state',	'com_fsj_transman.article.'.$item->id) && $canCheckin;
			
		if (array_key_exists("debug",$_GET))
			print_p($item);
		?>
								<tr class="row<?php echo $i % 2; ?>">
						
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>
			
												<td>
				
								
								
														<?php if ($canEdit || $canEditOwn) : ?>
									<a 
																			href="<?php echo JRoute::_('index.php?option=com_fsj_transman&task=package.edit&tmpl='.JRequest::getVar('tmpl').'&id='.$item->id);?>">
														
									<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>
								
						
						
											</td>
							
				
												
																	
						
													<td nowrap class="small">
								<?php
									$langcode = new JFormFieldFSJLookup();
									$langcode->lookup = json_decode('{"table":"(SELECT element, element as text, element as value FROM #__extensions WHERE type = \"language\" GROUP BY element ORDER BY element)","field":"element","display":"text","musthave":0,"alias":"l5","fieldalias":"lf5","warning":"","joinfield":"","displayonly":"","inlineedit":0,"nested":0,"options":[],"use_state":0,"state":"","onchange":"","jtext":0,"tmpl":"","or_sql":"","readonly":0}');
									echo $langcode->AdminDisplay(!empty($item->langcode) ? $item->langcode : '', 'langcode', $item, $i);
								?>
							</td>
												
						
												
						
																																																																																																																																																																																																																					
												
																																									
						
													<td class="center small">
																<?php //echo empty($item->author) ? 'Not in Data : author' : $item->author; ?>
								<?php echo $item->author; ?>
															</td>
												
						
												
						
																																																																																																																																																																																													
												
																																																																																																																																																																	
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmaddfiles'))
									{
										$files = new JFormFieldfsjtmaddfiles();
										$files->fsjtmaddfiles = json_decode('{}');
										echo $files->AdminDisplay(!empty($item->files) ? $item->files : '', 'files', $item, $i);
									} else {
										echo $item->files;
									}
								?>
															</td>
												
						
												
						
																																																																					
												
																																																																																																																																																																													
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmmakepackage'))
									{
										$makepackage = new JFormFieldfsjtmmakepackage();
										$makepackage->fsjtmmakepackage = json_decode('{}');
										echo $makepackage->AdminDisplay(!empty($item->makepackage) ? $item->makepackage : '', 'makepackage', $item, $i);
									} else {
										echo $item->makepackage;
									}
								?>
															</td>
												
						
												
						
																																																									
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
	
	