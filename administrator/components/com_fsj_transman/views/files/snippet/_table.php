<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

	
      
        <?php
          echo "<div style='clear: both;'>";
          echo JText::_("FSJ_TM_CURRENT_FOLDER") . "<b>" . FSJ_TM_File_Helper::$current_path . "</b>";
          echo "</div>";
          $document = JFactory::getDocument();
          $document->addScript( JURI::root() .'administrator/components/com_fsj_transman/assets/js/category.js');
        ?>
      
    
<table class="table table-striped" id="articleList">
	<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
																																																																																										
												
																					<th width="20%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_FILE_FILENAME', 'a.filename', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																																																																																																							
												
																					<th width="20%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_FILE_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																											
												
																					<th width="20%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_FILE_CATEGORY', 'a.category', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																			
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_FILE_TSTATE', 'a.tstate', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																															
												
																					<th width="250" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_FILE_STATUS', 'a.status', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																															
												
																					<th width="90" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_FILE_PHRASES', 'a.phrases', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																																																																																																																																																																																															
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_TRANSMAN_FORM_TRANSMAN_FILE_DOWNLOAD', 'a.download', $listDirn, $listOrder); ?>
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
			
												
																																																																													
						
													<td class="small">
																	<a
																			href="<?php echo JRoute::_('index.php?option=com_fsj_transman&task=file.edit&tmpl='.JRequest::getVar('tmpl').'&id='.$item->id);?>">
																									<?php
									if (class_exists('JFormFieldfsjtmfilename'))
									{
										$filename = new JFormFieldfsjtmfilename();
										$filename->fsjtmfilename = json_decode('{}');
										echo $filename->AdminDisplay(!empty($item->filename) ? $item->filename : '', 'filename', $item, $i);
									} else {
										echo $item->filename;
									}
								?>
																	</a>
															</td>
												
						
												
						
																																																																																																									
												
																																																																																																																																																																	
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmdesc'))
									{
										$description = new JFormFieldfsjtmdesc();
										$description->fsjtmdesc = json_decode('{}');
										echo $description->AdminDisplay(!empty($item->description) ? $item->description : '', 'description', $item, $i);
									} else {
										echo $item->description;
									}
								?>
															</td>
												
						
												
						
																					
												
																																																																																																																																									
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmcat'))
									{
										$category = new JFormFieldfsjtmcat();
										$category->fsjtmcat = json_decode('{}');
										echo $category->AdminDisplay(!empty($item->category) ? $item->category : '', 'category', $item, $i);
									} else {
										echo $item->category;
									}
								?>
															</td>
												
						
												
						
																																													
												
																																																																																									
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmstate'))
									{
										$tstate = new JFormFieldfsjtmstate();
										$tstate->fsjtmstate = json_decode('{}');
										echo $tstate->AdminDisplay(!empty($item->tstate) ? $item->tstate : '', 'tstate', $item, $i);
									} else {
										echo $item->tstate;
									}
								?>
															</td>
												
						
												
						
																																																																																													
												
																																																																																																					
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmstatus'))
									{
										$status = new JFormFieldfsjtmstatus();
										$status->fsjtmstatus = json_decode('{}');
										echo $status->AdminDisplay(!empty($item->status) ? $item->status : '', 'status', $item, $i);
									} else {
										echo $item->status;
									}
								?>
															</td>
												
						
												
						
																																																																																	
												
																																																																																																																	
						
													<td class="center small">
																<?php //echo empty($item->phrases) ? 'Not in Data : phrases' : $item->phrases; ?>
								<?php echo $item->phrases; ?>
															</td>
												
						
												
						
																																																																					
												
																																																																																																																																																																													
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtmdownloadfile'))
									{
										$download = new JFormFieldfsjtmdownloadfile();
										$download->fsjtmdownloadfile = json_decode('{}');
										echo $download->AdminDisplay(!empty($item->download) ? $item->download : '', 'download', $item, $i);
									} else {
										echo $item->download;
									}
								?>
															</td>
												
						
												
						
									
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
      
      <div><span class='badge badge-info'>i</span> Category - Inherited from base language</div>
      <div class='fsj'>
        <div id="category-modal" class="modal hide fsj_modal">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3><?php echo JText::_('FSJ_TM_CHANGE_CATEGORY'); ?></h3>
          </div>
          <div class="modal-body">
            <ul id="category-modal-list" class="nav nav-pills">
            </ul>
            <div class="form-inline" style="margin: 0px">
              <input type="text" id='category-modal-new' class="input-medium" placeholder="<?php echo JText::_('FSJ_TM_NEW_CATEGORY'); ?>">
              <a href='#' class="btn btn-primary" onclick='category_save_new();return false;'><?php echo JText::_('FSJ_TM_ADD_CATEGORY'); ?></a>
            </div>
          </div>
          <div class="modal-footer">
            <a href="#" data-dismiss="modal" class="btn"><?php echo JText::_('FSJ_TM_CANCEL'); ?></a>
          </div>
        </div>
      </div>
      
    	
	
	