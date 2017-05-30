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

class MaQmaHtmlEdit
{
	static function display(&$_row, &$_lists, $versions, $groups)
	{
		global $Itemid;

		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$editor = JFactory::getEditor();
		$supportConfig = HelpdeskUtility::GetConfig();
		$formtoken = JSession::getFormToken();?>

		<script language="javascript" type="text/javascript">
			Joomla.submitbutton = function (pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'product') {
					Joomla.submitform(pressbutton);
					return;
				}

				if (form.pname.value == "") {
					alert("<?php echo JText::_('name_required'); ?>");
				} else {
					<?php echo $editor->save('description'); ?>
					<?php echo $editor->save('features'); ?>
					<?php echo $editor->save('requirements'); ?>
					ArrangeFields();
					Joomla.submitform(pressbutton, document.getElementById('adminForm'));
				}
			}

			function ArrangeFields() {
				NEMPS = '';

				for (i = 0; i < document.adminForm.id_group.length; i++) {
					if (document.adminForm.id_group[i].selected == true) {
						NEMPS = NEMPS + document.adminForm.id_group[i].value + ",";
					}
				}

				document.adminForm.groupid.value = NEMPS.substr(0, NEMPS.length - 1);
			}

			function FillValues() {
				EMPS1 = "<?php echo $_row->groupid;?>";

				EMPS = EMPS1.split(",");

				for (i = 0; i < document.adminForm.id_group.length; i++) {
					for (z = 0; z < EMPS.length; z++) {
						if (document.adminForm.id_group[i].value == EMPS[z]) {
							document.adminForm.id_group[i].selected = true;
						}
					}
				}
			}

			function DeleteVersion(DOWNLOAD,VERSION)
			{
				$jMaQma('#adminForm').append($jMaQma('<input/>', {
					type: 'hidden',
					name: 'id_product',
					value: DOWNLOAD
				}));
				$jMaQma('#adminForm').append($jMaQma('<input/>', {
					type: 'hidden',
					name: 'id_version',
					value: VERSION
				}));
				$jMaQma("#task").val('product_delversion');
				$jMaQma("#adminForm").submit();
			}

			$jMaQma(document).ready(function(){
				$jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
			});
		</script>

		<?php $GLOBALS['title_product_form'] = ($_row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('download'); ?>

		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data" class="label-inline">
		<?php echo JHtml::_('form.token'); ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=kb"><?php echo JText::_('kb'); ?></a>
			<span><?php echo JText::_('edit'); ?></span>
		</div>
		<div class="tabbable tabs-left contentarea">
		<ul class="nav nav-tabs equalheight">
			<li class="active"><a href="#tab1" data-toggle="tab"><img src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/files.png" border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('general');?></a></li>
			<li><a href="#tab2" data-toggle="tab"><img src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/forms.png" border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('content');?></a></li>
			<li><a href="#tab3" data-toggle="tab"><img src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png" border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('versions');?></a></li>
		</ul>
		<div class="tab-content contentbar withleft">

		<div id="tab1" class="tab-pane active equalheight pad5">
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span2 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('name')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('name')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('name'); ?>
				                    </span>
					</div>
					<div class="span10">
						<input type="text"
						       class="span10"
						       id="pname"
						       name="pname"
						       value="<?php echo $_row->pname; ?>"
						       maxlength="100"
						       onblur="CreateSlug('pname');" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span2 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('slug')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('slug_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('slug'); ?>
				                    </span>
					</div>
					<div class="span10">
						<input type="text"
						       class="span10"
						       id="slug"
						       name="slug"
						       value="<?php echo $_row->slug; ?>"
						       maxlength="100" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('license')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('license')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('license'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo $_lists['id_license']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('category')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('category'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo $_lists['id_category']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('image')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('image')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('image'); ?>
				                    </span>
					</div>
					<div class="span8">
						<input type="file"
						       id="image2"
						       name="image2" />
						<br />
						<?php echo JText::_("DELETE_IMAGE") . $_lists['delete_image1'];?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('image_view')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('image_view')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('image_view'); ?>
				                    </span>
					</div>
					<div class="span8">
						<input type="file"
						       id="image_view"
						       name="image_view" />
						<br />
						<?php echo JText::_("DELETE_IMAGE") . $_lists['delete_image2'];?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span2 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('url')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('url')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('url'); ?>
				                    </span>
					</div>
					<div class="span10">
						<input type="text"
						       class="span10"
						       id="url"
						       name="url"
						       value="<?php echo $_row->url; ?>"
						       maxlength="250" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span2 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('evaluation')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('evaluation')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('evaluation'); ?>
				                    </span>
					</div>
					<div class="span10">
						<input type="text"
						       class="span10"
						       id="evaluation"
						       name="evaluation"
						       value="<?php echo $_row->evaluation; ?>"
						       maxlength="250" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span2 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('dl_plataforms')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('dl_plataforms')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('dl_plataforms'); ?>
				                    </span>
					</div>
					<div class="span10">
						<input type="text"
						       class="span10"
						       id="plataform"
						       name="plataform"
						       value="<?php echo $_row->plataform; ?>"
						       maxlength="250" />
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('template_file')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('template_file')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('template_file'); ?>
				                    </span>
					</div>
					<div class="span8">
						<input type="text"
						       id="template_file"
						       name="template_file"
						       value="<?php echo $_row->template_file; ?>"
						       maxlength="250" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('date')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('date')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('date'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo JHTML::Calendar($_row->id > 0 ? $_row->date : date("Y-m-d"), 'date', 'date', '%Y-%m-%d', array('class' => 'small', 'maxlength' => '10'));?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('dl_limitations')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('dl_limitations')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('dl_limitations'); ?>
				                    </span>
					</div>
					<div class="span8">
						<input type="text"
						       id="limitations"
						       name="limitations"
						       value="<?php echo $_row->limitations; ?>"
						       maxlength="250" />
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('download_previous')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('download_previous')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('download_previous'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo $_lists['download_previous']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('download')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('download')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('download'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo $_lists['download_version']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('registered_users')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('registered_users')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('registered_users'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo $_lists['registered_only']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('offline')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('offline')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('offline'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo $_lists['offline']; ?>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span4 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('published')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('published')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('published'); ?>
				                    </span>
					</div>
					<div class="span8">
						<?php echo $_lists['published']; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span2 showPopover"
					     data-original-title="<?php echo htmlspecialchars(JText::_('groups')); ?>"
					     data-content="<?php echo htmlspecialchars(JText::_('groups')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('groups'); ?>
				                    </span>
					</div>
					<div class="span10">
						<?php echo $_lists['id_group']; ?>
					</div>
				</div>
			</div>
		</div>
		</div>

		<div id="tab2" class="tab-pane equalheight pad5">
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('description')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('description'); ?>
				                    </span>
						</div>
						<div class="span10">
							<?php echo $editor->display('description', str_replace('\"', '"', $_row->description), '100%', '500', '75', '20');?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('dl_features')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('dl_features')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('dl_features'); ?>
				                    </span>
						</div>
						<div class="span10">
							<?php echo $editor->display('features', str_replace('\"', '"', $_row->features), '100%', '500', '75', '20');?>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span2 showPopover"
						     data-original-title="<?php echo htmlspecialchars(JText::_('dl_requirements')); ?>"
						     data-content="<?php echo htmlspecialchars(JText::_('dl_requirements')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('dl_requirements'); ?>
				                    </span>
						</div>
						<div class="span10">
							<?php echo $editor->display('requirements', str_replace('\"', '"', $_row->requirements), '100%', '500', '75', '20');?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="tab3" class="tab-pane equalheight">
			<table class="table table-striped table-bordered ontop">
				<thead>
				<tr>
					<th class="algcnt valgmdl"><?php echo JText::_('date');?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('version');?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('file');?></th>
					<th class="valgmdl"><?php echo JText::_('description');?></th>
					<th class="algcnt valgmdl" width="150"></th>
				</tr>
				</thead>
				<tfoot>
				</tfoot><?php
				if ($_row->id == 0) { ?>
					<tr>
					<td colspan="5"><?php echo JText::_('create_versions'); ?></td>
					</tr><?php
				} else {
					for ($i = 0; $i < count($versions); $i++)
					{
						$row_note = $versions[$i]; ?>
						<tr>
						<td class="algcnt valgmdl"><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row_note->date));?></td>
						<td class="algcnt valgmdl"><?php echo $row_note->version;?></td>
						<td class="algcnt valgmdl"><a href="index.php?option=com_maqmahelpdesk&task=product_download&id=<?php echo $row_note->id;?>&format=raw"><?php echo $row_note->filename;?></a></td>
						<td class="valgmdl"><?php echo strip_tags($row_note->description);?></td>
						<td class="algcnt valgmdl">
							<div class="btn-group">
								<a class="btn" href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&task=product_editversion&id_product=<?php echo $row_note->id_download;?>&id_version=<?php echo $row_note->id;?>"><i class="ico-pencil"></i> <small><?php echo JText::_("edit");?></small></a>
								<a class="btn btn-danger" href="javascript:;" onclick="DeleteVersion(<?php echo $row_note->id_download;?>,<?php echo $row_note->id;?>);"><i class="ico-trash ico-white"></i>&nbsp;</a>
							</div>
						</td>
						</tr><?php
					}

					if (count($versions) == 0) { ?>
						<tr>
						<td colspan="5"><?php echo JText::_('dl_no_versions'); ?></td>
						</tr><?php
					}
				} ?>
			</table>
			<div class="clr"></div>
		</div>
		</div>
		</div>

		<?php if ($_row->id > 0 ): ?>
		<script type="text/javascript"> FillValues(); </script>
	<?php endif;?>

		<input type="hidden" name="groupid" value=""/>
		<input type="hidden" name="updated" value="<?php echo date("Y-m-d"); ?>"/>
		<input type="hidden" name="image" value="<?php echo $_row->image; ?>"/>
		<input type="hidden" name="published" value="<?php echo $_row->published; ?>"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="id" value="<?php echo $_row->id; ?>"/>
		<input type="hidden" name="id_product" value="<?php echo $_row->id; ?>"/>
		<input type="hidden" id="task" name="task" value=""/>
		</form><?php
	}
}
