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
																														
												
														<th width="1%" class="nowrap">	
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_TPL_TYPE', 'lf3', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																	<th>
						<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
																																																																																		
												
																					<th width="50%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_FORM_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
								</th>
												
						
																																																																																							
												
																					<th width="5%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'FSJ_MAIN_FORM_TPL_NOEDIT', 'a.noedit', $listDirn, $listOrder); ?>
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
			
												
																	
						
													<td nowrap class="small">
								<?php
									$type = new JFormFieldFSJLookup();
									$type->lookup = json_decode('{"table":"#__fsj_tpl_type","field":"type","display":"title","musthave":0,"alias":"l3","fieldalias":"lf3","warning":"","joinfield":"","displayonly":"","inlineedit":0,"nested":0,"options":[],"use_state":0,"state":"","onchange":"","jtext":0,"tmpl":"","or_sql":"","readonly":"true"}');
									echo $type->AdminDisplay(!empty($item->type) ? $item->type : '', 'type', $item, $i);
								?>
							</td>
												
						
												
						
																																																																					
												<td>
				
								
								
														<?php if ($canEdit || $canEditOwn) : ?>
									<a 
																			href="<?php echo JRoute::_('index.php?option=com_fsj_main&task=tpl.edit&tmpl='.JRequest::getVar('tmpl').'&id='.$item->id);?>">
														
									<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>
								
						
						
											</td>
							
				
												
																																																																	
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjtext'))
									{
										$description = new JFormFieldfsjtext();
										$description->fsjtext = json_decode('{"fullfield":"","introfield":"","readmore":0,"pagebreak":0,"image":1,"width":550,"height":400,"cols":60,"rows":20,"strip":0,"maxsize":0,"preformat":0,"divwidth":0,"divheight":0,"rawtoggle":"","htmledit":1,"style":"width:50%;height:80px;"}');
										echo $description->AdminDisplay(!empty($item->description) ? $item->description : '', 'description', $item, $i);
									} else {
										echo $item->description;
									}
								?>
															</td>
												
						
												
						
																					
												
																																																					
						
													<td class="small">
																<?php
									if (class_exists('JFormFieldfsjyesno'))
									{
										$noedit = new JFormFieldfsjyesno();
										$noedit->fsjyesno = json_decode('{}');
										echo $noedit->AdminDisplay(!empty($item->noedit) ? $item->noedit : '', 'noedit', $item, $i);
									} else {
										echo $item->noedit;
									}
								?>
															</td>
												
						
												
						
																																	
					
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php echo $this->pagination->getListFooter(); ?>

	
      
      <script>
        jQuery(document).ready(function () {
          jQuery('#toolbar-download button').unbind('click');
          jQuery('#toolbar-download button').removeAttr('onclick');
          jQuery('#toolbar-download button').click(function (ev) {
            ev.preventDefault();
            if (document.adminForm.boxchecked.value==0)
            {
              alert('Please first make a selection from the list');
            } else { 
              var tplid = jQuery('#articleList input[type="checkbox"]:checked').val();
              var url = "index.php?option=com_fsj_main&task=tpls.export&id="  +tplid;
              window.location = url;
            }
          });
        });
      </script>
    	
	
	