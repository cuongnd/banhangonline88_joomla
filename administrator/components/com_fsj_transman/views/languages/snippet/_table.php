<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

	
      
      <div style='margin-top: 8px'>
        <span class='badge badge-success'><?php echo JText::_('FSJ_TM_COMPLETED'); ?></span>
		    <span class='badge badge-info'><?php echo JText::_('FSJ_TM_STATE_UNPUB'); ?></span>
		    <span class='badge badge-warning'><?php echo JText::_('FSJ_TM_PARTIALLY_TRANSLATED'); ?></span>
		    <span class='badge badge-important'><?php echo JText::_('FSJ_TM_STATE_NOT_STARTED'); ?></span>
		    <span class='badge badge-inverse'><?php echo JText::_('FSJ_TM_STATE_NOT_IN_BASE'); ?></span>
      </div>
      
    
<table class="table table-striped" id="articleList">
	<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
																																																																														
												
																					<th width="30%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_LANGUAGE_XPATH', 'a.xpath', $listDirn, $listOrder); ?>
								</th>
												
						
																																																															
												
																					<th width="30%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_LANGUAGE_NAME', 'a.name', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																															
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_LANGUAGE_ELEMENT', 'a.element', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																															
												
																					<th width="25%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_LANGUAGE_FILES', 'a.files', $listDirn, $listOrder); ?>
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
				<?php echo JHtml::_('grid.id', $i, $item->extension_id); ?>
			</td>
			
												
																																																																	
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmpath'))
									{
										$xpath = new JFormFieldfsjtmpath();
										$xpath->fsjtmpath = json_decode('{}');
										echo $xpath->AdminDisplay(!empty($item->xpath) ? $item->xpath : '', 'xpath', $item, $i);
									} else {
										echo $item->xpath;
									}
								?>
															</td>
												
						
												
						
																					
												
																													
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmlangname'))
									{
										$name = new JFormFieldfsjtmlangname();
										$name->fsjtmlangname = json_decode('{}');
										echo $name->AdminDisplay(!empty($item->name) ? $item->name : '', 'name', $item, $i);
									} else {
										echo $item->name;
									}
								?>
															</td>
												
						
												
						
																																																									
												
																																									
						
													<td class="center small">
																<?php //echo empty($item->element) ? 'Not in Data : element' : $item->element; ?>
								<?php echo $item->element; ?>
															</td>
												
						
												
						
																																													
												
																																																					
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmfiles'))
									{
										$files = new JFormFieldfsjtmfiles();
										$files->fsjtmfiles = json_decode('{}');
										echo $files->AdminDisplay(!empty($item->files) ? $item->files : '', 'files', $item, $i);
									} else {
										echo $item->files;
									}
								?>
															</td>
												
						
												
						
																																	
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
	
	