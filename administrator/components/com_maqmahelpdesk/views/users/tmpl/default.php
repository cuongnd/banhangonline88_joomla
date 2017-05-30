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

class users_fields_html
{
	static function show(&$rows, &$pageNav)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=users"><?php echo JText::_('users_manager'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=users_fields"><?php echo JText::_('users_cfield_menu'); ?></a>
				<span><?php echo JText::_('manager'); ?></span>
			</div>
			<div class="contentarea">
				<table id="contentTable" class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th width="20" nowrap="nowrap"></th>
						<th class="algcnt valgmdl" width="20">#</th>
						<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
						<th class="valgmdl"><?php echo JText::_('field_name'); ?></th>
						<th class="algcnt valgmdl"><?php echo JText::_('field_type'); ?></th>
						<th class="algcnt valgmdl" width="70"><?php echo JText::_('required'); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						if (count($rows) == 0) {
							?>
							<tr>
								<td colspan="6"><?php echo JText::_('register_not_found'); ?></td>
							</tr><?php
						} else {
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img = $row->required ? 'ok' : 'remove';
								?>
								<tr id="contentTable-row-<?php echo ($row->id);?>">
									<td width="20" class="dragHandle"></td>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td class="valgmdl">
										<a href="#users_fieldsedit"
										   onclick="return listItemTask('cb<?php echo $i;?>','users_fieldsedit')">
											<?php echo $row->caption; ?>
										</a>
									</td>
									<td class="algcnt valgmdl"><?php echo JText::_('formfield_' . $row->ftype); ?></td>
									<td class="algcnt valgmdl">
										<span class="btn btn-<?php echo ($row->required ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span>
									</td>
								</tr><?php
							} // for loop
						} // if ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" id="task" name="task" value="users_fields"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form>

		<script type="text/javascript">
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
						$jMaQma("#task").val('users_saveorder');
						$jMaQma("#adminForm").submit();
					},
					dragHandle:"dragHandle"
				});

				$jMaQma("#contentTable tr").hover(function () {
					$jMaQma(this.cells[0]).addClass('showDragHandle');
				}, function () {
					$jMaQma(this.cells[0]).removeClass('showDragHandle');
				});
			});
		</script><?php
	}

	static function edit(&$row, $lists)
	{
		$GLOBALS['titulo_usersfield_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('cfield'); ?>
		<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'users_fields') {
	            Joomla.submitform(pressbutton);
				return;
			}

			Joomla.submitform(pressbutton, document.getElementById('adminForm'));
		}

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
		</script>

		<form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=users"><?php echo JText::_('users_manager'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=users_fields"><?php echo JText::_('users_cfield_menu'); ?></a>
				<span><?php echo JText::_('manager'); ?></span>
			</div>
			<div class="contentarea pad5">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="span2 showPopover"
                                 data-original-title="<?php echo htmlspecialchars(JText::_('field')); ?>"
                                 data-content="<?php echo htmlspecialchars(JText::_('field')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('field'); ?>
			                    </span>
                            </div>
                            <div class="span10">
								<?php echo $lists['fields']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="span2 showPopover"
                                 data-original-title="<?php echo htmlspecialchars(JText::_('required')); ?>"
                                 data-content="<?php echo htmlspecialchars(JText::_('required')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('required'); ?>
			                    </span>
                            </div>
                            <div class="span10">
								<?php echo $lists['required']; ?>
                            </div>
                        </div>
                    </div>
                </div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
			<input type="hidden" name="ordering" value="<?php echo $lists['ordering']; ?>"/>
			<input type="hidden" name="task" value=""/>
		</form><?php
	}
}

class HTML_users
{
	static function showUsers(&$rows, $pageNav, $search, $lists)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=users"><?php echo JText::_('users_manager'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>

		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png"
																   style="padding:5px;" align="absmiddle"/>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox"
					   onChange="document.adminForm.submit();"/>
				<?php echo JText::_('status') . ': ' . $lists['logged'];?>
			</div>

			<div class="contentarea">
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th width="20">#</th>
						<th width="20">&nbsp;</th>
						<th><?php echo JText::_('name'); ?></th>
						<th width="20%"><?php echo JText::_('tpl_client') ?></th>
						<th width="5%" nowrap="nowrap"><?php echo JText::_('loggedin'); ?></th>
						<th width="5%"><?php echo JText::_('enabled'); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						if (count($rows) == 0) {
							?>
							<tr>
								<td colspan="6"><?php echo JText::_('register_not_found'); ?></td>
							</tr><?php
						} else {
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row =& $rows[$i];
								$img = $row->block ? 'remove' : 'ok';
								$task = $row->block ? 'users_unblock' : 'users_block';
								$alt = $row->block ? JText::_('blocked') : JText::_('enabled');
								$link = 'index.php?option=com_maqmahelpdesk&task=users_edit&cid[0]=' . $row->id; ?>
								<tr>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
									<td class="valgmdl">
										<p>
											<img src="<?php echo HelpdeskUser::GetAvatar($row->id);?>" alt="" class="mqmavatar"
												 align="left" style="margin-right:10px;"/>
											<a href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','users_edit')"><b><?php echo $row->name; ?></b></a><br/>
											<small><em><?php echo $row->username; ?></em></small>
											<br/>
											<small><em><a
												href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></em>
											</small>
										</p>
									</td>
									<td class="valgmdl">
										<?php echo $row->clientname ? $row->clientname : JText::_('no_client'); ?>
									</td>
									<td class="algcnt valgmdl">
										<span class="btn btn-<?php echo ($row->loggedin ? 'success' : 'danger');?>"><i class="ico-<?php echo ($row->loggedin ? 'ok' : 'remove');?>-sign ico-white"></i></span>
									</td>
									<td class="algcnt valgmdl">
										<span class="btn btn-<?php echo ($img=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span>
									</td>
								</tr><?php
							} // for loop
						} // if ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="users"/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="hidemainmenu" value="0"/>
		</form><?php
	}

	static function edituser(&$row, $uid, $userInfo, $cfields, $lists)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$GLOBALS['titulo_usersfields_edit'] = ((isset($row->id)) ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('user');
		$GLOBALS['titulo_users_edit'] = ((isset($row->id)) ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('user'); ?>

		<script language="javascript" type="text/javascript">
		function showMore() {
			$jMaQma('#more').toggle()
		}

		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'users') {
                Joomla.submitform(pressbutton);
				return;
			}
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");<?php

			if ($supportConfig->rf_phone) {
				echo 'if (form.phone.value == "") {' . "\n";
				echo 'alert( "' . JText::_('phone_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_fax) {
				echo 'if (form.fax.value == "") {' . "\n";
				echo 'alert( "' . JText::_('fax_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_mobile) {
				echo 'if (form.mobile.value == "") {' . "\n";
				echo 'alert( "' . JText::_('mobile_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_address1) {
				echo 'if (form.address1.value == "") {' . "\n";
				echo 'alert( "' . JText::_('address1_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_address2) {
				echo 'if (form.address2.value == "") {' . "\n";
				echo 'alert( "' . JText::_('address2_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_zipcode) {
				echo 'if (form.zipcode.value == "") {' . "\n";
				echo 'alert( "' . JText::_('zip_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_location) {
				echo 'if (form.location.value == "") {' . "\n";
				echo 'alert( "' . JText::_('location_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_city) {
				echo 'if (form.city.value == "") {' . "\n";
				echo 'alert( "' . JText::_('city_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}
			if ($supportConfig->rf_country) {
				echo 'if (form.country.value == "") {' . "\n";
				echo 'alert( "' . JText::_('country_required') . '" );' . "\n";
				echo 'return false;' . "\n";
				echo '}' . "\n";
			}

			// Custom fields
			for ($x = 0; $x < count($cfields); $x++) {
				$cfield = $cfields[$x];
				if ($cfield->required) {
					echo 'if (form.custom' . $cfield->id_field . '.value == "") {' . "\n";
					echo 'alert( "' . JText::_('cfield_required') . ' ' . $cfield->caption . '." );' . "\n";
					echo '}' . "\n";
				}
			} ?>

			else
			{
                Joomla.submitform(pressbutton);
			}
		}

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
		</script>

		<form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=users"><?php echo JText::_('users_manager'); ?></a>
				<span><?php echo JText::_('edit'); ?></span>
			</div>
			<div class="tabbable tabs-left contentarea">
				<ul class="nav nav-tabs equalheight">
					<li class="active"><a href="#tab1" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/users.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('general');?></a></li>
					<li><a href="#tab2" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('other');?></a></li>
					<li><a href="#tab3" data-toggle="tab"><img
						src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/forms.png"
						border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('more');?></a></li>
				</ul>
				<div class="tab-content contentbar withleft">
					<div id="tab1" class="tab-pane active equalheight pad5">
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="row-fluid">
                                    <img src="<?php echo HelpdeskUser::GetAvatar($row->id); ?>" class="mqmavatar"/>
                                </div>
                            </div>
                        </div>
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
	                                    <br /><?php echo $row->name; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('username')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('username')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('username'); ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <br /><?php echo $row->username; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('email')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('email')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('email'); ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <br /><?php echo $row->email; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="row-fluid">
                                    <div class="span2 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('client')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('client')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('client'); ?>
					                    </span>
                                    </div>
                                    <div class="span10">
                                        <br /><?php echo $userInfo->clientname; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('last_visit_date')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('last_visit_date')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('last_visit_date'); ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <br /><?php echo HelpdeskDate::ShortDate($row->lastvisitDate); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('register_date')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('register_date')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('register_date'); ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <br /><?php echo HelpdeskDate::ShortDate($row->registerDate); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('schedule')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('schedule_tooltip')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('schedule'); ?>
					                    </span>
                                    </div>
                                    <div class="span8">
				                        <?php echo $lists['schedules']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('vacances')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('vacances_tooltip')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('vacances'); ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <textarea id="vacances"
                                                  name="vacances"
                                                  style="height:100px;"><?php echo $lists['vacances']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
					<div id="tab2" class="tab-pane equalheight pad5">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('phone')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('phone')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('phone'); ?>
						                    <?php echo $supportConfig->rf_phone ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="phone"
                                               name="phone"
                                               value="<?php echo $userInfo->phone; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('fax')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('fax')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('fax'); ?>
						                    <?php echo $supportConfig->rf_fax ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="fax"
                                               name="fax" value="<?php echo $userInfo->fax; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('mobile')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('mobile')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('mobile'); ?>
						                    <?php echo $supportConfig->rf_mobile ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="mobile"
                                               name="mobile"
                                               value="<?php echo $userInfo->mobile; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('address1')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('address1')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('address1'); ?>
						                    <?php echo $supportConfig->rf_address1 ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="address1"
                                               name="address1" value="<?php echo $userInfo->address1; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('address2')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('address2')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('address2'); ?>
						                    <?php echo $supportConfig->rf_address2 ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="address2"
                                               name="address2"
                                               value="<?php echo $userInfo->address2; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('zipcode')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('zipcode')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('zipcode'); ?>
						                    <?php echo $supportConfig->rf_zipcode ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="zipcode"
                                               name="zipcode" value="<?php echo $userInfo->zipcode; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('location')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('location')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('location'); ?>
						                    <?php echo $supportConfig->rf_location ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="location"
                                               name="location"
                                               value="<?php echo $userInfo->location; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('city')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('city')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('city'); ?>
						                    <?php echo $supportConfig->rf_city ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <input type="text"
                                               id="city"
                                               name="city" value="<?php echo $userInfo->city; ?>"
                                               maxlength="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="row-fluid">
                                    <div class="span4 showPopover"
                                         data-original-title="<?php echo htmlspecialchars(JText::_('country')); ?>"
                                         data-content="<?php echo htmlspecialchars(JText::_('country')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('country'); ?>
						                    <?php echo $supportConfig->rf_country ? '<span class="required">*</span>' : ''; ?>
					                    </span>
                                    </div>
                                    <div class="span8">
                                        <select id="country" name="country" class="inputbox">
                                            <option value=""></option>
		                                    <?php include JPATH_SITE . '/components/com_maqmahelpdesk/includes/countries.php'; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p><span class="required">*</span> <?php echo JText::_('field_required_desc_admin');?></p>
					</div>
					<div id="tab3" class="tab-pane equalheight">
						<table class="table table-striped table-bordered ontop" cellspacing="0"><?php
							if (count($cfields) == 0) {
								print '<tr><td>' . JText::_('no_fields') . '</td></tr>';
							}
							for ($x = 0; $x < count($cfields); $x++) {
								$cfield = $cfields[$x]; ?>
								<tr>
									<td nowrap valign="top" class="key">
										<span class="editlinktip hasTip"
											  title="<?php echo htmlspecialchars($cfield->caption); ?>"><?php echo $cfield->caption; ?></span>
									</td>
									<td align="left"><?php
										echo HelpdeskForm::WriteField(0, $cfield->id_field, $cfield->ftype, $cfield->value, $cfield->size, $cfield->maxlength, $row->id);
										echo $cfield->required ? '<span class="required">*</span>' : ''; ?>
									</td>
								</tr><?php
							} ?>
						</table>
						<div class="clr"></div>
						<span class="required">*</span> <?php echo JText::_('field_required_desc_admin');?>
					</div>
				</div>
			</div>

			<input type="hidden" name="avatar" value="<?php echo HelpdeskUser::GetAvatar($row->id); ?>"/>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="contact_id" value=""/>
		</form>

		<script type='text/javascript'>
		function SelectCountry(COUNTRY) {
			$jMaQma("#country").val(COUNTRY);
		}

		$jMaQma(document).ready(function () {
			$jMaQma(".equalheight").equalHeights();
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
			SelectCountry('<?php echo $userInfo->country; ?>');
		});
		</script><?php
	}
}
