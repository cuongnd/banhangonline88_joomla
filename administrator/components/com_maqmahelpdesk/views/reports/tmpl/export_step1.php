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
class reports_html
{
	static function show($lists, $exports, $sub_os)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();

		$supportConfig->date_short ? $tmp_format = $supportConfig->date_short : $tmp_format = 'd/m/Y H:i';
		$imgpath = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/'; ?>

	    <script language="javascript" text="text/javascript">
	        <!--
	        var originalOrderOS = '0';
	        var originalPos = '0';

	        var ordersOS = new Array();
				<?php
				$i = 0;
				foreach ($sub_os as $k => $items)
				{
					foreach ($items as $v)
					{
						echo "\n	ordersOS[" . $i++ . "] = new Array( '$v->value', '$k', '$v->text' );";
					}
				} ?>

		        Joomla.submitbutton = function (pressbutton){
		            var form = document.adminForm;

		            if (form.export_profile_id.value == 0) {
		                alert("<?php echo JText::_('export_required'); ?>");
		            } else {
		                form.submit();
		            }
		        }<?php

				print "\n";
				print 'var a = new Array();' . "\n";

				for ($i = 0; $i < count($lists['profiles']); $i++) {
					$row_profile = $lists['profiles'][$i];

					print 'a[' . ($i + 1) . '] = new Array();' . "\n";
					print 'a[' . ($i + 1) . '][0] = "' . $row_profile->id . '";' . "\n";
					print 'a[' . ($i + 1) . '][1] = "' . $row_profile->workgroup . '";' . "\n";
					print 'a[' . ($i + 1) . '][2] = "' . $row_profile->client . '";' . "\n";
					print 'a[' . ($i + 1) . '][3] = "' . $row_profile->user . '";' . "\n";
					print 'a[' . ($i + 1) . '][4] = "' . $row_profile->status . '";' . "\n";
				} ?>

	        function SelectRecords()
	        {
	            var ProfileObj = document.adminForm.export_profile_id;
	            var StatusObj = document.adminForm.id_export_statuses;
	            var WorkgroupObj = document.adminForm.selwk;
	            var ClientObj = document.adminForm.client;
	            var UserObj = document.adminForm.id_user;

	            for (i = 1; i < WorkgroupObj.length; i++) {
	                for (z = 1; z < a.length; z++) {
	                    if (WorkgroupObj[i].value == a[z][1] && ProfileObj.value == a[z][0]) {
	                        WorkgroupObj[i].selected = true;
	                    }
	                }
	            }

	            for (i = 1; i < ClientObj.length; i++) {
	                for (z = 1; z < a.length; z++) {
	                    if (ClientObj[i].value == a[z][2] && ProfileObj.value == a[z][0]) {
	                        ClientObj[i].selected = true;
	                    }
	                }
	            }

	            for (i = 1; i < UserObj.length; i++) {
	                for (z = 1; z < a.length; z++) {
	                    if (UserObj[i].value == a[z][3] && ProfileObj.value == a[z][0]) {
	                        UserObj[i].selected = true;
	                    }
	                }
	            }

	            for (i = 1; i < StatusObj.length; i++) {
	                for (z = 1; z < a.length; z++) {
	                    if (StatusObj[i].value == a[z][4] && ProfileObj.value == a[z][0]) {
	                        StatusObj[i].selected = true;
	                    }
	                }
	            }
	        }
	        //-->
	    </script>

	    <form id="adminForm" name="adminForm" action="<?php echo JURI::root();?>index.php" method="post"
	          onSubmit="return CheckFields();">
			<?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="user" value="<?php echo $user->id; ?>"/>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value="ajax_cvs"/>
	        <input type="hidden" name="format" value="raw"/>

	        <table class="admintable" cellspacing="1" width="100%">
	            <tr>
	                <td colspan="2">
						<?php echo JText::_('export_desc'); ?>
	                </td>
	            </tr>
	            <tr>
	                <td nowrap valign="top" class="key">
						<span rel="tooltip"
	                          data-original-title="<?php echo htmlspecialchars(JText::_('export_profile') . '::' . JText::_('select_export_profile')); ?>"><?php echo JText::_('export_profile'); ?></span>
	                </td>
	                <td>
						<?php echo $lists['profile'];?>
	                </td>
	            </tr>
	        </table>

	        <br/>

	        <table class="admintable" cellspacing="1" width="100%">
	            <tr>
	                <td nowrap valign="top" class="key">
						<span rel="tooltip"
	                          data-original-title="<?php echo htmlspecialchars(JText::_('workgroup') . '::' . JText::_('export_wk_tooltip')); ?>"><?php echo JText::_('workgroup'); ?></span>
	                </td>
	                <td width="74" align="left" nowrap><?php echo $lists['workgroup'];?></td>
	                <td width="100" align="left">&nbsp;</td>
	                <td nowrap valign="top" class="key">
						<span rel="tooltip"
	                          data-original-title="<?php echo htmlspecialchars(JText::_('client') . '::' . JText::_('export_client_tooltip')); ?>"><?php echo JText::_('client'); ?></span>
	                </td>
	                <td width="100" align="left" nowrap><?php echo $lists['client'];?></td>
	            </tr>
	            <tr>
	                <td nowrap valign="top" class="key">
						<span rel="tooltip"
	                          data-original-title="<?php echo htmlspecialchars(JText::_('status') . '::' . JText::_('export_status_tooltip')); ?>"><?php echo JText::_('status'); ?></span>
	                </td>
	                <td width="100" align="left" nowrap><?php echo $lists['statuses'];?></td>
	                <td width="100" align="left">&nbsp;</td>
	                <td nowrap valign="top" class="key">
						<span rel="tooltip"
	                          data-original-title="<?php echo htmlspecialchars(JText::_('user') . '::' . JText::_('export_user_tooltip')); ?>"><?php echo JText::_('user'); ?></span>
	                </td>
	                <td width="100" align="left" nowrap>
	                    <script language="javascript" type="text/javascript">
	                        <!--
	                        writeDynaList('class="inputbox" name="id_user" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
	                        //-->
	                    </script>
	                </td>
	            </tr>

	            <tr>
	                <td nowrap valign="top" class="key">
	                    Tickets
	                </td>
	                <td width="100" align="left" nowrap>
						<?php echo $lists['month'];?>
						<?php echo $lists['year']; ?>
	                </td>
	                <td width="100" align="left">&nbsp;</td>
	                <td nowrap valign="top" class="key">
	                </td>
	                <td width="100" align="left" nowrap>
	                </td>
	            </tr>

	        </table>
	    </form>


	    <br/><br/>


	    <table class="adminform">
	        <tr>
	            <th><?php echo JText::_('export_history'); ?></th>
	        </tr>
	    </table>


	    <table class="adminlist" cellspacing="1" width="100%">
	        <thead>
	        <tr>
	            <th width="20" nowrap><?php echo JText::_('id'); ?></th>
	            <th width="10" nowrap><?php echo JText::_('export_date'); ?></th>
	            <th nowrap><?php echo JText::_('exported_by'); ?></th>
	            <th nowrap><?php echo JText::_('profile_used'); ?></th>
	            <th nowrap><?php echo JText::_('records'); ?></th>
	            <th nowrap><?php echo JText::_('hits'); ?></th>
	            <th nowrap><?php echo JText::_('tools'); ?></th>
	        </tr>
	        </thead>
	        <tbody>
				<?php

				if (count($exports) == 0) {
					print '<tr><td colspan="7"><br />' . JText::_('empty') . '<br /></td></tr>';

				} else {
					for ($i = 0; $i < count($exports); $i++) {
						$row_exp = $exports[$i];

						switch ($row_exp->export_type) {
							case 'A':
								$export_type_name = JText::_('activities');
								break;
							case 'T':
								$export_type_name = JText::_('tickets');
								break;
							case 'C':
								$export_type_name = JText::_('client');
								break;
							case 'U':
								$export_type_name = JText::_('users');
								break;
						}

						$database->setQuery("SELECT count(*) FROM #__support_ticket WHERE id_export='" . $row_exp->id . "'");
						$export_locked_items = $database->loadResult();

						if ($export_locked_items) {
							$export_locked_status = '<a href="index.php?option=com_maqmahelpdesk&task=reports&report=unlock&id=' . $row_exp->id . '"><img align="absmiddle" hspace="5" src="' . $imgpath . 'lock.png" border="0" alt="' . JText::_('unlock_tickets') . '" name="' . JText::_('unlock_tickets') . '" title="' . JText::_('unlock_tickets') . '" /></a>';
						} else {
							$export_locked_status = '<span rel="tooltip" data-original-title="' . JText::_('export_options_used') . '::' . JText::_('no_locked') . '"><img src="' . $imgpath . 'info.png" align="absmiddle" border="0" hspace="5" style="cursor:help; cursor:hand;" /></span>';
						}

						$export_tools_download = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=view&id=' . $row_exp->id . '"><img align="absmiddle" src="' . $imgpath . 'export.png" border="0" alt="' . JText::_('download') . '" name="' . JText::_('download') . '" title="' . JText::_('download') . '" hspace="5" /></a>';

						$export_tools_template = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=tpl&id=' . $row_exp->id . '"><img align="absmiddle" src="' . $imgpath . 'templates.png" border="0" alt="' . JText::_('download_template') . '" name="' . JText::_('download_template') . '" title="' . JText::_('download_template') . '" hspace="5" /></a>';

						$export_tools_delete = '<a href="index.php?option=com_maqmahelpdesk&task=reports&report=delete&id=' . $row_exp->id . '" onclick="javascript:return confirm(\'Are you sure you want to delete this export history item?\')"><img align="absmiddle" src="' . $imgpath . 'delete.png" border="0" alt="' . JText::_('delete') . '" name="' . JText::_('delete') . '" title="' . JText::_('delete') . '" hspace="5" /></a>';

						$export_date = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=view&id=' . $row_exp->id . '">' . date($tmp_format, HelpdeskDate::ParseDate($row_exp->export_date, "%Y-%m-%d %H:%M:%S")) . '</a>';

						$export_id = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=view&id=' . $row_exp->id . '">' . $row_exp->id . '</a>';


						print '<tr>';
						print '	 <td width="20" nowrap>' . $export_id . '</td>';
						print '	 <td width="10" nowrap>' . $export_date . '</td>';
						print '	 <td nowrap>' . $row_exp->export_author . '</td>';
						print '	 <td nowrap>' . $row_exp->profile_name . ' (' . $export_type_name . ' type)</td>';
						print '	 <td nowrap>' . $row_exp->num_records . '</td>';
						print '	 <td nowrap>' . $row_exp->hits . '</td>';
						print '	 <td>' . $export_tools_download . '&nbsp;' . $export_tools_template . $export_locked_status . $export_tools_delete . '</td>';
						print '</tr>';
					}
				}
				?>
	        </tbody>
	    </table>

	    <script type='text/javascript'>SelectRecords();</script>

		<?php
	}
}
