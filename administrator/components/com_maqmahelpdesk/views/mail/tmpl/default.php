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

/**
 * @package MaQma Helpdesk
 */
class mail_html
{
	static function show(&$rows, &$pageNav)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="javascript:;"><?php echo JText::_('addons'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=mail"><?php echo JText::_('email_fetch'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>
		<div class="contentarea">
			<div id="contentbox">
				<?php if (count($rows) == 0) : ?>
				<div class="detailmsg">
					<h1><?php echo JText::_('register_not_found'); ?></h1>

					<p><?php echo JText::_('to_add_new_record_desc'); ?></p>
				</div>
				<script type="text/javascript"> MaQmaJS.AddHelpHand('toolbar-new'); </script>
				<?php else: ?>
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th width="20" align="right">#</th>
						<th width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
						<th class="title"><?php echo JText::_('workgroup'); ?></th>
						<th class="title"><?php echo JText::_('email'); ?></th>
						<th class="title"><?php echo JText::_('username'); ?></th>
						<th class="title"><?php echo JText::_('mail_server'); ?></th>
						<th class="algcnt valgmdl"><?php echo JText::_('published'); ?></th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
					</tfoot>
					<tbody><?php
						$k = 0;
						$j = 0;
						for ($i = 0, $n = count($rows); $i < $n; $i++)
						{
							$row = &$rows[$i];
							$img = $row->published ? 'eye-open' : 'eye-close';
							$task = $row->published ? 'mail_unpublish' : 'mail_publish';
							$alt = $row->published ? JText::_('published') : JText::_('unpublished'); ?>
							<tr class="<?php echo "row$k"; ?>">
								<td width="20" align="right"><span class="lbl"><?php echo $row->id; ?></span></td>
								<td width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
								<td><?php echo $row->wkdesc; ?></td>
								<td>
									<a href="#mail_edit" onClick="return listItemTask('cb<?php echo $i;?>','mail_edit')">
										<?php echo $row->email; ?>
									</a>
								</td>
								<td><?php echo $row->username; ?></td>
								<td><?php echo $row->server; ?></td>
								<td class="algcnt valgmdl"><a class="btn btn-<?php echo ($row->published ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a></td>
							</tr><?php
						} // for ?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			<div id="infobox">
				<span id="infoarrow"></span>
				<dl class="first">
					<dd class="title"><?php echo JText::_('INFO_FETCHING_TITLE');?></dd>
					<dd class="last">
						<?php echo JText::_('INFO_FETCHING_DESC');?>
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

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value="mail"/>
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
		</script><?php
	}

	static function edit(&$row, $lists)
	{
		$GLOBALS['title_edit_mail'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('fetch_account'); ?>

	<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'mail') {
            Joomla.submitform(pressbutton);
			return;
		}

		if (form.id_workgroup.value == "0") {
			alert("<?php echo JText::_('wk_required'); ?>");
		} else if (form.email.value == "") {
			alert("<?php echo JText::_('email_required'); ?>");
		} else if (form.server.value == "") {
			alert("<?php echo JText::_('server_required'); ?>");
		} else if (form.username.value == "") {
			alert("<?php echo JText::_('username_required'); ?>");
		} else if (form.password.value == "") {
			alert("<?php echo JText::_('password_required'); ?>");
		} else {
			Joomla.submitform(pressbutton, document.getElementById('adminForm'));
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
			<a href="javascript:;"><?php echo JText::_('addons'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=mail"><?php echo JText::_('email_fetch'); ?></a>
			<span><?php echo JText::_('edit'); ?></span>
		</div>
		<div class="contentarea pad5">
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('workgroup')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('workgroup'); ?>
			                    </span>
                        </div>
                        <div class="span8">
							<?php echo $lists['workgroup']; ?>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('category_queue')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('category_queue_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('category_queue'); ?>
			                    </span>
                        </div>
                        <div id="categoryField" class="span8">
	                        <?php echo $lists['category']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
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
                            <input type="text"
                                   id="email"
                                   name="email"
                                   value="<?php echo $row->email; ?>"
                                   maxlength="100" />
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('mail_server')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('mail_server')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('mail_server'); ?>
			                    </span>
                        </div>
                        <div class="span8">
                            <input type="text"
                                   id="server"
                                   name="server"
                                   value="<?php echo $row->server; ?>"
                                   maxlength="100" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('mail_port')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('pop_port')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('mail_port'); ?>
			                    </span>
                        </div>
                        <div class="span8">
                            <input type="text"
                                   id="port"
                                   name="port"
                                   value="<?php echo $row->port; ?>"
                                   maxlength="100" />
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('server_type')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('server_type')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('server_type'); ?>
			                    </span>
                        </div>
                        <div class="span8">
	                        <?php echo $lists['type']; ?>
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
                            <input type="text"
                                   id="username"
                                   name="username"
                                   value="<?php echo $row->username; ?>"
                                   maxlength="100" />
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('password')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('password')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('password'); ?>
			                    </span>
                        </div>
                        <div class="span8">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   value="<?php echo $row->password; ?>"
                                   maxlength="100" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('remove_mail')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('remove_mail')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('remove_mail'); ?>
			                    </span>
                        </div>
                        <div class="span8">
	                        <?php echo $lists['remove']; ?>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('label_fetch')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('label_fetch_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('label_fetch'); ?>
			                    </span>
                        </div>
                        <div class="span8">
                            <input type="text"
                                   id="label"
                                   name="label"
                                   value="<?php echo ($row->id ? $row->label : 'INBOX'); ?>"
                                   maxlength="25" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('FETCH_BOX_THRASH')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('FETCH_BOX_THRASH_TOOLTIP')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('FETCH_BOX_THRASH'); ?>
			                    </span>
                        </div>
                        <div class="span8">
                            <input type="text"
                                   id="thrash"
                                   name="thrash"
                                   value="<?php echo ($row->id ? $row->thrash : 'THRASH'); ?>"
                                   maxlength="25" />
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('extra_info')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('extra_info')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('extra_info'); ?>
			                    </span>
                        </div>
                        <div class="span8">
                            <input type="text"
                                   id="extra_info"
                                   name="extra_info"
                                   value="<?php echo $row->extra_info; ?>"
                                   maxlength="100" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('fetch_queue')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('fetch_queue_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('fetch_queue'); ?>
			                    </span>
                        </div>
                        <div class="span8">
	                        <?php echo $lists['queue']; ?>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('fetch_notls')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('FETCH_NOTLS_TOOLTIP')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('fetch_notls'); ?>
			                    </span>
                        </div>
                        <div class="span8">
	                        <?php echo $lists['notls']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('status_queue')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('status_queue_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('status_queue'); ?>
			                    </span>
                        </div>
                        <div class="span8">
	                        <?php echo $lists['status']; ?>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('ssl')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('ssl_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('ssl'); ?>
			                    </span>
                        </div>
                        <div class="span8">
	                        <?php echo $lists['ssl']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('published')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('published_tooltip')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('published'); ?>
			                    </span>
                        </div>
                        <div class="span8">
	                        <?php echo $lists['published']; ?>
                        </div>
                    </div>
                </div>
            </div>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
		<input type="hidden" name="task" value=""/>
	</form>

	<script type='text/javascript'>
		function GetCategories() {
			$jMaQma.ajax({
				url:"index.php?option=com_maqmahelpdesk&task=mail_categories&id_workgroup=" + $jMaQma("#id_workgroup").val() + "&format=raw",
				success:function (data) {
					$jMaQma("#categoryField").html(data);
				}
			});
		}
	</script><?php
	}
}

class mailignore_html
{
	static function show(&$rows, &$pageNav)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="javascript:;"><?php echo JText::_('addons'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=mailignore"><?php echo JText::_('mail_ignore_rules'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>
		<div class="contentarea">
			<div id="contentbox">
				<?php if (count($rows) == 0) : ?>
				<div class="detailmsg">
					<h1><?php echo JText::_('register_not_found'); ?></h1>

					<p><?php echo JText::_('to_add_new_record_desc'); ?></p>
				</div>
				<script type="text/javascript"> MaQmaJS.AddHelpHand('toolbar-new'); </script>
				<?php else: ?>
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th width="20" align="right">#</th>
						<td width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
											  onClick="checkAll(<?php echo count($rows);?>);"/></td>
                        <td class="title"><?php echo JText::_('field'); ?></td>
						<td class="title" width="100"><?php echo JText::_('operator'); ?></td>
						<td class="title"><?php echo JText::_('value'); ?></td>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="5"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
					</tfoot>
					<tbody><?php
						$k = 0;
						$j = 0;
						for ($i = 0, $n = count($rows); $i < $n; $i++) {
							$row = &$rows[$i]; ?>
						<tr class="<?php echo "row$k"; ?>">
							<td width="20" align="right"><span class="lbl"><?php echo $row->id; ?></span></td>
							<td width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
                            <td><?php echo $row->field; ?></td>
							<td><?php echo $row->operator; ?></td>
							<td>
								<a href="#mail_editignore"
								   onClick="return listItemTask('cb<?php echo $i;?>','mail_editignore')">
									<?php echo $row->value; ?>
								</a>
							</td>
						</tr>
							<?php
						} // for ?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			<div id="infobox">
				<span id="infoarrow"></span>
				<dl class="first">
					<dd class="title"><?php echo JText::_('INFO_FETCHING_TITLE');?></dd>
					<dd class="last">
						<?php echo JText::_('INFO_FETCHING_DESC');?>
						<p align="center"><a
							href="http://www.imaqma.com/support/manuals/item/e-mail-fetching.html?category_id=1"
							target="_blank" class="btn"><img
							src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/help.png"
							align="absmiddle" border="0" alt=""/> <?php echo JText::_('more_information');?></a></p>
					</dd>
				</dl>
			</div>
			<div class="clr"></div>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value="mailignore"/>
		<input type="hidden" name="boxchecked" value="0"/>
	</form><?php
	}

	static function edit(&$row, $lists)
	{
		?>
	<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'mailignore') {
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
			<a href="javascript:;"><?php echo JText::_('addons'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=mailignore"><?php echo JText::_('mail_ignore_rules'); ?></a>
			<span><?php echo JText::_('edit'); ?></span>
		</div>
		<div class="contentarea pad5">
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="span2 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('operator')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('operator')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('operator'); ?>
			                    </span>
                        </div>
                        <div class="span10">
							<?php echo $lists['operator']; ?>
                        </div>
                    </div>
                </div>
            </div>
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
                        <div id="categoryField" class="span10">
							<?php echo $lists['field']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <div class="row-fluid">
                        <div class="span4 showPopover"
                             data-original-title="<?php echo htmlspecialchars(JText::_('value')); ?>"
                             data-content="<?php echo htmlspecialchars(JText::_('value')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('value'); ?>
			                    </span>
                        </div>
                        <div class="span8">
                            <input type="text"
                                   id="value"
                                   name="value"
                                   value="<?php echo $row->value; ?>"
                                   maxlength="250" />
                        </div>
                    </div>
                </div>
            </div>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
		<input type="hidden" name="task" value=""/>
	</form><?php
	}
}
