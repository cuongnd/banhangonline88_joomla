<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class MaQmaHtmlDefault
{
	static function display(&$rows, &$pageNav, $search, $categories)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$database = JFactory::getDBO();?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=kb"><?php echo JText::_('kb'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>

		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png" style="padding:5px;" align="absmiddle"/>
				<input type="text" id="search" name="search" value="<?php echo urldecode(htmlspecialchars($search));?>"/>
				<?php echo $categories; ?>
				<div class="btn-group" style="float:right;">
					<a href="javascript:;" class="btn"
					   onclick="document.getElementById('search').value='';document.adminForm.submit();"><?php echo JText::_('reset');?></a>
					<a href="javascript:;" class="btn btn-success"
					   onclick="document.adminForm.submit();"><?php echo JText::_('filter');?></a>
				</div>
			</div>

			<div class="contentarea">
				<div id="contentbox" class="equalheight mqmclear">
					<?php if (count($rows) == 0) : ?>
					<div class="detailmsg">
						<h1><?php echo JText::_('register_not_found'); ?></h1>

						<p><?php echo JText::_('to_add_new_record_desc'); ?></p>
					</div>
					<script type="text/javascript"> MaQmaJS.AddHelpHand('toolbar-new'); </script>
					<?php else: ?>
					<table id="contentTable" class="table table-striped table-bordered" cellspacing="0">
						<thead>
						<tr>
							<th width="20"></th>
							<th class="algcnt valgmdl" width="20">#</th>
							<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th class="valgmdl"><?php echo JText::_('title'); ?></th>
							<th class="valgmdl"><?php echo JText::_('categories'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('access'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('wk_faq'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('approved'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('published'); ?></th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="10"><?php echo $pageNav->getListFooter(); ?></td>
						</tr>
						</tfoot>
						<tbody><?php
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img = $row->publish ? 'eye-open' : 'eye-close';
								$img_approve = $row->approved ? 'ok' : 'remove';
								$img_faq = $row->faq ? 'ok' : 'remove';
								$task = $row->publish ? 'kb_unpublish' : 'kb_publish';
								$alt = $row->publish ? JText::_('published') : JText::_('unpublished'); ?>

								<tr id="contentTable-row-<?php echo ($row->id);?>">
									<td class="dragHandle" width="20"></td>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td class="valgmdl">
										<a href="#kb_edit" onClick="return listItemTask('cb<?php echo $i;?>','kb_edit')">
											<?php echo str_replace("\'", "'", $row->kbtitle); ?>
										</a><br/>
										<?php echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span>
									</td>
									<td class="valgmdl"><?php
										$database->setQuery("SELECT c.name FROM #__support_category c, #__support_kb k, #__support_kb_category kc WHERE c.id=kc.id_category AND k.id=kc.id_kb AND k.id=" . $row->id);
										$catrows = $database->loadObjectList();
										for ($z = 0; $z < count($catrows); $z++)
										{
											$catrow = &$catrows[$z];
											print $catrow->name;
											if ((count($catrows) - $z) > 1)
											{
												print ", ";
											}
										} ?>
									</td>
									<td class="algcnt valgmdl"><?php
										switch ($row->anonymous_access)
										{
											case 0:
												$access = JText::_('everybody');
												$access_img =  'eye-open';
												break;
											case 1:
												$access = JText::_('registered_users');
												$access_img =  'user';
												break;
											case 2:
												$access = JText::_('support_agents_only');
												$access_img =  'lock';
												break;
										} ?>
										<i class="ico-<?php echo $access_img;?>"></i> <?php echo $access; ?>
									</td>
									<td class="algcnt valgmdl" width="70">
										<span class="btn btn-<?php echo ($row->faq ? 'success' : 'danger');?>"><i class="ico-<?php echo $img_faq;?>-sign ico-white"></i></span>
									</td>
									<td class="algcnt valgmdl" width="70">
										<span class="btn btn-<?php echo ($row->approved ? 'success' : 'danger');?>"><i class="ico-<?php echo $img_approve;?>-sign ico-white"></i></span>
									</td>
									<td class="algcnt valgmdl" width="70">
										<a class="btn btn-<?php echo ($row->publish ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a>
									</td>
								</tr><?php
							} // for ?>
						</tbody>
					</table>
					<?php endif; ?>
				</div>
				<div id="infobox" class="equalheight">
					<span id="infoarrow"></span>
					<dl class="first">
						<dd class="title"><?php echo JText::_('INFO_KB_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_KB_DESC');?>
                            <p>&nbsp;</p>
                            <div class="btn-group">
                                <a href="#" target="_blank" class="btn btn-small"><i class="ico-book"></i> <?php echo JText::_('more_information');?></a>
                                &nbsp;
                                <a id="mqmCloseHelp" href="javascript:;" class="btn btn-small btn-inverse"><i class="ico-off ico-white"></i> <?php echo JText::_('close');?></a>
                            </div>
						</dd>
					</dl>
				</div>
				<div class="clr"></div>
			</div>

			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" id="task" name="task" value="kb_search"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form>

		<script type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'show_help') {
                $jMaQma("#infobox").show();
                return;
            }

            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
        }

		$jMaQma(document).ready(function () {
			$jMaQma('#contentTable').tableDnD({
				onDrop:function (table, row) {
					var rows = table.tBodies[0].rows;
					for (var i=0; i<rows.length; i++) {
						var RowID = rows[i].id;
						$jMaQma('#adminForm').append($jMaQma('<input/>', {
							type: 'hidden',
							name: 'contentTable[]',
							value: RowID.replace('contentTable-row-', '')
						}));
					}
					$jMaQma("#task").val('kb_saveorder');
					$jMaQma("#adminForm").submit();
				},
				dragHandle:"dragHandle"
			});

			$jMaQma("#contentTable tbody tr").hover(function () {
				$jMaQma(this.cells[0]).addClass('showDragHandle');
			}, function () {
				$jMaQma(this.cells[0]).removeClass('showDragHandle');
			});

			$jMaQma("#search").css("width", $jMaQma("#filtersarea").width() - $jMaQma("#search").offset().left - $jMaQma(".btn-group").width() - $jMaQma("#filter_category").width());
			$jMaQma(window).resize(function () {
				$jMaQma("#search").css("width", $jMaQma("#filtersarea").width() - $jMaQma("#search").offset().left - $jMaQma(".btn-group").width() - $jMaQma("#filter_category").width());
			});

			$jMaQma(".equalheight").equalHeights();
		});
		</script><?php
	}
}
