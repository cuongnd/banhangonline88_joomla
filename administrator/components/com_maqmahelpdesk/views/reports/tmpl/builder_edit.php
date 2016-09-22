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
	static function show(&$row, $lists, $sub_os)
	{ ?>
	    <script language="javascript" type="text/javascript">
	        Joomla.submitbutton = function (pressbutton) {
	            var form = document.adminForm;
	            if (pressbutton == 'reports_builder') {
	                Joomla.submitform(pressbutton);
	                return;
	            }

	            if (form.title.value == "") {
	                alert("<?php echo JText::_('title_required'); ?>");
	            } else {
	                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
	            }
	        }

	        var originalOrderOS = '<?php echo ($row->id == 0 ? 0 : $row->f_user); ?>';
	        var originalPos = '<?php echo ($row->id == 0 ? 0 : $row->f_client); ?>';

	        var ordersOS = new Array();
				<?php
				$i = 0;
				foreach ($sub_os as $k => $items) {
					foreach ($items as $v) {
						echo "\n	ordersOS[" . $i++ . "] = new Array( '$v->value', '$k', '$v->text' );";
					}
				} ?>
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<?php
			$GLOBALS['title_editBuilder'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('report');
			?>

	    <table class="adminform">
	        <tr>
	            <th><?php echo JText::_('report_information'); ?></th>
	        </tr>
	    </table>


	    <table class="admintable" cellspacing="1" width="100%">
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('title')); ?>"><?php echo JText::_('title'); ?></span>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="title" value="<?php echo $row->title; ?>" size="50"
	                       maxlength="50"/>
	            </td>
	        </tr>
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"><?php echo JText::_('description'); ?></span>
	            </td>
	            <td>
	                <textarea id="" name="" style="width:500px;height:150px;"><?php echo $row->description;?></textarea>
	            </td>
	        </tr>
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('layout')); ?>"><?php echo JText::_('layout'); ?></span>
	            </td>
	            <td>
	                <table>
	                    <tr>
	                        <td>
	                            <div align="center"><input type="radio" name="layout"
	                                                       value="1" <?php echo $row->layout == 1 ? 'checked' : '' ?>><img
	                                    src="../components/com_maqmahelpdesk/images/layout1.png" align="absmiddle"/>

	                                <div>
	                        </td>
	                        <td>
	                            <div align="center"><input type="radio" name="layout"
	                                                       value="2" <?php echo $row->layout == 2 ? 'checked' : '' ?>><img
	                                    src="../components/com_maqmahelpdesk/images/layout2.png" align="absmiddle"/>

	                                <div>
	                        </td>
	                        <td>
	                            <div align="center"><input type="radio" name="layout"
	                                                       value="3" <?php echo $row->layout == 3 ? 'checked' : '' ?>><img
	                                    src="../components/com_maqmahelpdesk/images/layout3.png" align="absmiddle"/>

	                                <div>
	                        </td>
	                        <td>
	                            <div align="center"><input type="radio" name="layout"
	                                                       value="4" <?php echo $row->layout == 4 ? 'checked' : '' ?>><img
	                                    src="../components/com_maqmahelpdesk/images/layout4.png" align="absmiddle"/>

	                                <div>
	                        </td>
	                    </tr>
	                </table>
	            </td>
	        </tr>
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('report_type')); ?>"><?php echo JText::_('report_type'); ?></span>
	            </td>
	            <td><?php echo $lists['report_type']; ?></td>
	        </tr>
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('report_detail') . '::' . JText::_('extended_desc')); ?>"><?php echo JText::_('report_detail'); ?></span>
	            </td>
	            <td>
	                <input type="radio" name="report_type"
	                       value="S" <?php echo $row->report_type == 'S' ? 'checked' : ''; ?> /><?php echo JText::_('simple'); ?>
	                &nbsp;&nbsp;<input type="radio" name="report_type"
	                                   value="E" <?php echo $row->report_type == 'E' ? 'checked' : ''; ?> /> <?php echo JText::_('extended'); ?>
	            </td>
	        </tr>
	    </table>


	    <br/>


	    <table class="adminform">
	        <tr>
	            <th><?php echo JText::_('chart_options'); ?></th>
	        </tr>
	    </table>

	    <table class="admintable" cellspacing="1" width="100%">
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('chart_type')); ?>"><?php echo JText::_('chart_type'); ?></span>
	            </td>
	            <td><?php echo $lists['chart_type']; ?></td>
	        </tr>
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('show_percs')); ?>"><?php echo JText::_('show_percs'); ?></span>
	            </td>
	            <td><?php echo $lists['chart_percentage']; ?></td>
	        </tr>
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('chart_width')); ?>"><?php echo JText::_('chart_width'); ?></span>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="chart_width" value="<?php echo $row->chart_width; ?>"
	                       size="5" maxlength="3"/>
	            </td>
	        </tr>
	        <tr>
	            <td nowrap valign="top" class="key">
					<span rel="tooltip"
	                      data-original-title="<?php echo htmlspecialchars(JText::_('chart_height')); ?>"><?php echo JText::_('chart_height'); ?></span>
	            </td>
	            <td>
	                <input class="text_area" type="text" name="chart_height" value="<?php echo $row->chart_height; ?>"
	                       size="3" maxlength="3"/>
	            </td>
	        </tr>
	    </table>

	    <br/>

	    <table class="adminform">
	        <tr>
	            <th class="title"><?php echo JText::_('criteria'); ?>:</th>
	            <th class="title"><?php echo JText::_('group_by'); ?>:</th>

	        </tr>
	    </table>

	    <table class="admintable" cellspacing="1" width="100%">
	        <tr>
	            <td valign="top">
	                <table width="100%">
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"><?php echo JText::_('workgroup'); ?></span>
	                        </td>
	                        <td><?php echo $lists['workgroup']; ?> <input type="radio" name="sf_workgroup"
	                                                                      value="1" <?php echo $row->sf_workgroup == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_workgroup"
	                                                     value="0" <?php echo $row->sf_workgroup == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"><?php echo JText::_('category'); ?></span>
	                        </td>
	                        <td><?php echo $lists['category']; ?> <input type="radio" name="sf_category"
	                                                                     value="1" <?php echo $row->sf_category == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_category"
	                                                     value="0" <?php echo $row->sf_category == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('priority')); ?>"><?php echo JText::_('priority'); ?></span>
	                        </td>
	                        <td><?php echo $lists['priority']; ?> <input type="radio" name="sf_priority"
	                                                                     value="1" <?php echo $row->sf_priority == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_priority"
	                                                     value="0" <?php echo $row->sf_priority == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('status')); ?>"><?php echo JText::_('status'); ?></span>
	                        </td>
	                        <td><?php echo $lists['status']; ?> <input type="radio" name="sf_status"
	                                                                   value="1" <?php echo $row->sf_status == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_status"
	                                                     value="0" <?php echo $row->sf_status == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('client')); ?>"><?php echo JText::_('client'); ?></span>
	                        </td>
	                        <td><?php echo $lists['client']; ?> <input type="radio" name="sf_client"
	                                                                   value="1" <?php echo $row->sf_client == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_client"
	                                                     value="0" <?php echo $row->sf_client == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('user')); ?>"><?php echo JText::_('user'); ?></span>
	                        </td>
	                        <td>
	                            <script language="javascript" type="text/javascript">
	                                <!--
	                                writeDynaList('class="inputbox" name="f_user" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
	                                //-->
	                            </script>
	                            <input type="radio" name="sf_user"
	                                   value="1" <?php echo $row->sf_user == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_user"
	                                                     value="0" <?php echo $row->sf_user == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('tkt_chng_stat_nfy_sup')); ?>"><?php echo JText::_('tkt_chng_stat_nfy_sup'); ?></span>
	                        </td>
	                        <td><?php echo $lists['assign']; ?> <input type="radio" name="sf_staff"
	                                                                   value="1" <?php echo $row->sf_staff == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_staff"
	                                                     value="0" <?php echo $row->sf_staff == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('year')); ?>"><?php echo JText::_('year'); ?></span>
	                        </td>
	                        <td><?php echo $lists['year']; ?> <input type="radio" name="sf_year"
	                                                                 value="1" <?php echo $row->sf_year == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_year"
	                                                     value="0" <?php echo $row->sf_year == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('month')); ?>"><?php echo JText::_('month'); ?></span>
	                        </td>
	                        <td><?php echo $lists['month']; ?> <input type="radio" name="sf_month"
	                                                                  value="1" <?php echo $row->sf_month == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
	                            &nbsp;&nbsp;&nbsp;<input type="radio" name="sf_month"
	                                                     value="0" <?php echo $row->sf_month == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
	                        </td>
	                    </tr>
	                </table>
	            </td>
	            <td valign="top">
	                <table width="100%">
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"><?php echo JText::_('workgroup'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="WK" <?php echo $row->groupby == 'WK' ? 'checked' : ''; ?> /></td>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"><?php echo JText::_('category'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="CA" <?php echo $row->groupby == 'CA' ? 'checked' : ''; ?> /></td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('client')); ?>"><?php echo JText::_('client'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="CL" <?php echo $row->groupby == 'CL' ? 'checked' : ''; ?> /></td>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('priority')); ?>"><?php echo JText::_('priority'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="PR" <?php echo $row->groupby == 'PR' ? 'checked' : ''; ?> /></td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('status')); ?>"><?php echo JText::_('status'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="ST" <?php echo $row->groupby == 'ST' ? 'checked' : ''; ?> /></td>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('source')); ?>"><?php echo JText::_('source'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="SO" <?php echo $row->groupby == 'SO' ? 'checked' : ''; ?> /></td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('year')); ?>"><?php echo JText::_('year'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="YR" <?php echo $row->groupby == 'YR' ? 'checked' : ''; ?> /></td>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('month')); ?>"><?php echo JText::_('month'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="MO" <?php echo $row->groupby == 'MO' ? 'checked' : ''; ?> /></td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('day')); ?>"><?php echo JText::_('day'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="DY" <?php echo $row->groupby == 'DY' ? 'checked' : ''; ?> /></td>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('weekday')); ?>"><?php echo JText::_('weekday'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="WD" <?php echo $row->groupby == 'WD' ? 'checked' : ''; ?> /></td>
	                    </tr>
	                    <tr>
	                        <td nowrap valign="top" class="key">
								<span rel="tooltip"
	                                  data-original-title="<?php echo htmlspecialchars(JText::_('tkt_chng_stat_nfy_sup')); ?>"><?php echo JText::_('tkt_chng_stat_nfy_sup'); ?></span>
	                        </td>
	                        <td align="left"><input type="radio" name="groupby"
	                                                value="AS" <?php echo $row->groupby == 'AS' ? 'checked' : ''; ?> /></td>
		                    <td nowrap valign="top" class="key">
								<span rel="tooltip"
								      data-original-title="<?php echo htmlspecialchars(JText::_('CFIELD')); ?>"><?php echo JText::_('CFIELD'); ?></span>
		                    </td>
	                        <td align="left">
		                        <?php echo $lists['cfields']; ?>
	                        </td>
	                    </tr>
		                <tr>
			                <td colspan="4" height="20"></td>
		                </tr>
		                <tr>
			                <td nowrap valign="top" class="key">
								<span rel="tooltip"
								      data-original-title="<?php echo htmlspecialchars(JText::_('SUB_GROUP_BY')); ?>"><?php echo JText::_('SUB_GROUP_BY'); ?></span>
			                </td>
			                <td align="left">
				                <?php echo $lists['cfields2']; ?>
			                </td>
			                <td></td>
			                <td></td>
		                </tr>
	                </table>
	            </td>
	        </tr>
	    </table>

	    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	    <input type="hidden" name="task" value=""/>
	    </form>
		<?php
	}
}
