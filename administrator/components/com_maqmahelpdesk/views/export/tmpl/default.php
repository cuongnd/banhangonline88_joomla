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

class export_html
{
	/**
	 * Writes a list of the categories for a section
	 * @param array An array of category objects
	 * @param string The name of the category section
	 */
	static function show(&$rows, &$pageNav)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>

		<table class="adminheading">
			<tr>
				<th class="export">
					<?php JText::_('export_options'); ?>
				</th>
			</tr>
		</table>

		<table class="adminlist">
			<thead>
			<tr>
				<th width="20" align="right">#</th>
				<th width="20">
					<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
						   onClick="Joomla.checkAll(this);"/>
				</th>
				<th class="title"><?php echo JText::_('name'); ?></th>
				<th class="title"><?php echo JText::_('description'); ?></th>
				<th width="70"><?php echo JText::_('default'); ?></th>
			</tr>
			</thead>
			<tbody>
				<?php
				if (count($rows) == 0) {
					?>
				<tr>
					<td colspan="5"><?php echo JText::_('register_not_found'); ?></td>
				</tr><?php
				} else {
					$k = 0;
					$j = 0;
					for ($i = 0, $n = count($rows); $i < $n; $i++) {
						$row = &$rows[$i];
						$img = $row->isdefault ? 'ok' : 'no';
						$alt = $row->isdefault ? 'Default' : '';
						?>
					<tr class="<?php echo "row$k"; ?>">
						<td width="20" align="right"><span class="lbl"><?php echo $row->id; ?></span></td>
						<td width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
						<td>
							<a href="#export_edit" onClick="return listItemTask('cb<?php echo $i;?>','export_edit')">
								<?php echo $row->name; ?>
							</a>
						</td>
						<td><?php echo $row->description; ?></td>
						<td width="70" align="center">
							<img
								src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/<?php echo $img;?>.png"
								border="0" alt="<?php echo $alt;?>"/>
						</td>
					</tr>
						<?php
					} // for loop
				} // if ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value="export"/>
		<input type="hidden" name="boxchecked" value="0"/>
	</form><?php
	}

	/**
	 * Writes the edit form for new and existing workgroups
	 *
	 * A new record is defined when <var>$row</var> is passed witht the <var>id</var>
	 * property set to 0.
	 * @param string Record fields
	 * @param string The component name
	 * @param string The select lists
	 */
	static function edit(&$row, $lists, $sub_os)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'export') {
            Joomla.submitform(pressbutton);
			return;
		}

		if (form.name.value == "") {
			alert("<?php JText::_('exportname_required'); ?>");
		} else {
			Joomla.submitform(pressbutton, document.getElementById('adminForm'));
		}
	}

	function increaseNotesHeight(thisTextarea, add) {
		if (thisTextarea) {
			newHeight = parseInt(thisTextarea.style.height) + add;
			thisTextarea.style.height = newHeight + "px";
		}
	}

	function decreaseNotesHeight(thisTextarea, subtract) {
		if (thisTextarea) {
			if ((parseInt(thisTextarea.style.height) - subtract) > 50) {
				newHeight = parseInt(thisTextarea.style.height) - subtract;
				thisTextarea.style.height = newHeight + "px";
			}
			else {
				newHeight = 50;
				thisTextarea.style.height = "50px";
			}
		}
	}

	var originalOrderOS = '<?php echo $row->filter_userid; ?>';
	var originalPos = '<?php echo $row->filter_clientid; ?>';

	var ordersOS = new Array();
	<?php
	$i = 0;
	foreach ($sub_os as $k => $items) {
		foreach ($items as $v) {
			echo "\n	ordersOS[" . $i++ . "] = new Array( '$v->value', '$k', '$v->text' );";
		}
	} ?>
	</script>

	<form action="index.php" method="POST" id="adminForm" name="adminForm" enctype="multipart/form-data">
		<?php echo JHtml::_('form.token'); ?>

		<table class="adminform">
			<tr>
				<th class="export"><?php echo ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('export_option');?></th>
			</tr>
		</table>

		<table class="admintable" cellspacing="1" width="100%">
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('exportname') . '::' . JText::_('exportname_tooltip')); ?>"><?php echo JText::_('exportname'); ?></span>
				</td>
				<td valign="top" nowrap="nowrap" colspan="3"><input class="text_area" type="text" name="name"
																	value="<?php echo $row->name; ?>" size="75"
																	maxlength="100"/></td>

				<td valign="top" rowspan="2">
					<table class="admintable" cellspacing="1" width="100%">
						<tr>
							<td nowrap valign="top" class="key">
								<span rel="tooltip"
									  data-original-title="<?php echo htmlspecialchars(JText::_('export_type') . '::' . JText::_('export_type_tooltip')); ?>"><?php echo JText::_('export_type'); ?></span>
							</td>
							<td valign="top" nowrap="nowrap"><?php echo $lists['export_type']; ?></td>
						</tr>
						<tr>
							<td nowrap valign="top" class="key">
								<span rel="tooltip"
									  data-original-title="<?php echo htmlspecialchars(JText::_('export_default') . '::' . JText::_('export_default_tooltip')); ?>"><?php echo JText::_('export_default'); ?></span>
							</td>
							<td valign="top" nowrap="nowrap"><?php echo $lists['default']; ?> </td>
						</tr>
						<tr>
							<td nowrap valign="top" class="key">
								<span rel="tooltip"
									  data-original-title="<?php echo htmlspecialchars(JText::_('export_autosave') . '::' . JText::_('export_autosave_tooltip')); ?>"><?php echo JText::_('export_autosave'); ?></span>
							</td>
							<td valign="top" nowrap="nowrap"><?php echo $lists['auto_save']; ?> </td>
						</tr>
						<tr>
							<td nowrap valign="top" class="key">
								<span rel="tooltip"
									  data-original-title="<?php echo htmlspecialchars(JText::_('export_billable') . '::' . JText::_('export_billable_tooltip')); ?>"><?php echo JText::_('export_billable'); ?></span>
							</td>
							<td valign="top" nowrap="nowrap"><?php echo $lists['billableonly']; ?> </td>
						</tr>
						<tr>
							<td nowrap valign="top" class="key">
								<span rel="tooltip"
									  data-original-title="<?php echo htmlspecialchars(JText::_('export_update') . '::' . JText::_('export_update_tooltip')); ?>"><?php echo JText::_('export_update'); ?></span>
							</td>
							<td valign="top" nowrap="nowrap"><?php echo $lists['update_exported']; ?> </td>
						</tr>
					</table>
				</td>

			</tr>
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('description') . '::' . JText::_('export_desc_tooltip')); ?>"><?php echo JText::_('description'); ?></span>
				</td>
				<td colspan="2" valign="top" nowrap="nowrap"><textarea name="description" id="description" cols="50"
																	   rows="3"
																	   style="width:99%;height:100px;"><?php echo $row->description; ?></textarea>
				</td>
			</tr>
		</table>

		<table class="adminform">
			<tr>
				<th><?php echo JText::_('export_filters');?></th>
			</tr>
		</table>

		<!--
			  TODO
			  Pedido:
			  periodo [ultimo mes! ultimo ano! ultima semana] com data final em: d/m/y
				-->
		<table class="admintable" cellspacing="1" width="100%">
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('export_status') . '::' . JText::_('export_status_tooltip')); ?>"><?php echo JText::_('export_status'); ?></span>
				</td>
				<td valign="top" nowrap="nowrap"><?php echo $lists['statuses'];?></td>
				<td rowspan="4" valign="top">

					<table class="admintable" cellspacing="1" width="100%">
						<tr>
							<td nowrap valign="top" class="key" colspan="4">
								<span rel="tooltip"
									  data-original-title="<?php echo htmlspecialchars(JText::_('export_template') . '::' . JText::_('export_template_tooltip')); ?>"><?php echo JText::_('export_template'); ?></span>
							</td>
						</tr>
						<tr>
							<td valign="top" nowrap="nowrap" colspan="4"><textarea name="export_tmpl" id="export_tmpl"
																				   cols="50" rows="3"
																				   style="width:99%;height:100px;"><?php echo $row->export_tmpl; ?></textarea>
							</td>
						</tr>
					</table>

				</td>
			</tr>
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('workgroup') . '::' . JText::_('export_wk_tooltip')); ?>"><?php echo JText::_('workgroup'); ?></span>
				</td>
				<td valign="top" nowrap="nowrap"><?php echo $lists['workgroup'];?></td>
			</tr>
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('client') . '::' . JText::_('export_client_tooltip')); ?>"><?php echo JText::_('client'); ?></span>
				</td>
				<td valign="top" nowrap="nowrap"><?php echo $lists['client'];?></td>
			</tr>
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('user') . '::' . JText::_('export_user_tooltip')); ?>"><?php echo JText::_('user'); ?></span>
				</td>
				<td valign="top" nowrap="nowrap">
					<script language="JavaScript" type="text/javascript">
						<!--
						writeDynaList('class="inputbox" name="filter_userid" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
						//-->
					</script>
				</td>
			</tr>
		</table>


		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
		<input type="hidden" name="task" value=""/>
	</form>

	<script type='text/javascript'>
		changeDynaList('filter_userid', ordersOS, document.adminForm.filter_clientid.options[document.adminForm.filter_clientid.selectedIndex].value, originalPos, originalOrderOS);
	</script><?php
	}
}

?>