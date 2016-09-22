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

// Include helpers
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/report.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php';

class SupportReports
{
	function WorkgroupAnalysis($year, $month, $id_workgroup, $id_client, $id_user)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_maqmahelpdesk/includes/amcharts/ampie/swfobject.js"></script>';
		$print = JRequest::getVar('print', 0, '', 'int');

		// LINE CHARTS
		ob_start(); ?>
    <script type="text/javascript" defer="defer">
        // <![CDATA[
        var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline.swf", "amline", "100%", "600", "8", "#FFFFFF");
        so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/");
        so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline_settings.xml"));
        so.addVariable("additional_chart_settings", encodeURIComponent('<settings><graphs>%LABELS%</graphs></settings>'));
        so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
        so.addVariable("loading_data", "<?php echo JText::_('loading_data'); ?>");
        so.addVariable("preloader_color", "#999999");
        so.addParam("wmode", "transparent");
        so.write("%CHARTNAME%");
        // ]]>
    </script><?php
		$lines_html = ob_get_contents();
		ob_end_clean();

		// PIE CHARTS
		ob_start(); ?>
    <script type="text/javascript" defer="defer">
        // <![CDATA[
        var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie.swf", "ampie", "100%", "600", "8", "#FFFFFF");
        so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/");
        so.addVariable("settings_file", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie_settings.xml");
        so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
        so.addVariable("loading_data", "<?php echo JText::_('loading_data'); ?>");
        so.addVariable("preloader_color", "#999999");
        so.addParam("wmode", "transparent");
        so.write("%CHARTNAME%");
        // ]]>
    </script><?php
		$chart_html = ob_get_contents();
		ob_end_clean();

		$html = '';
		$html .= "<tr><td>";
		$days = HelpdeskDate::GetMonthDays($year, $month);

		$database->setQuery("SELECT COUNT(*) FROM #__support_ticket t WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month));
		if ($database->loadResult() > 0) {
			if (!$print) {
				ob_start(); ?>
            <script type="text/javascript">
                function ShowReport(ID) {
                    for (i = 0; i <= 9; i++) {
                        if (i != ID) {
                            $jMaQma("#report-" + i).hide("fade");
                        } else {
                            $jMaQma("#report-" + i).show("fade");
                        }
                    }
                }
            </script>
            <div style="width:20%;float:left;">
                <div class="t">
                    <div class="t">
                        <div class="t"></div>
                    </div>
                </div>
                <div class="m">
                    <p>&bull; <a href="javascript:;" onclick="ShowReport(1);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_day_workgroup'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(2);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_workgroup'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(3);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_status_group'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(4);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_status'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(5);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_priority'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(6);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_source'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(7);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_category'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(8);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_assign_user'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(9);"
                                 class="submenu"><?php echo JText::_('report_year_comparison'); ?></a></p>

                    <div class="clr"></div>
                </div>
                <div class="b">
                    <div class="b">
                        <div class="b"></div>
                    </div>
                </div>
            </div>
				<div style="width:80%;float:left;"><?php
				$html .= ob_get_contents();
				ob_end_clean();
				$html .= '<div id="report-1">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_day_workgroup') . '</h2>';
			}
			$sql = "select id, wkdesc from #__support_workgroup order by wkdesc";
			$database->setQuery($sql);
			$rows = $database->loadObjectList();
			$nrwks = count($rows);
			$arrdays = array();
			$wkslabels = '';

			for ($x = 0; $x < count($rows); $x++) {
				$row = $rows[$x];
				$wkslabels .= '<graph gid="' . ($x + 1) . '"><title>' . addslashes($row->wkdesc) . "</title><line_width>3</line_width><bullet>round_outlined</bullet><bullet_size>10</bullet_size><color_hover>#000000</color_hover></graph>";
				$sql = "SELECT DATE_FORMAT( t.date, '%d' ) AS DAY , count(*) AS total FROM `#__support_ticket` AS t WHERE month(`date`)=" . $database->quote($month) . " and year(`date`)=" . $database->quote($year) . " AND id_workgroup='" . $row->id . "' GROUP BY DATE_FORMAT( t.date, '%d' ) ORDER BY DATE_FORMAT( t.date, '%d' )";
				$database->setQuery($sql);
				$rows_dates = $database->loadObjectList();
				$prev_day = 1;
				for ($i = 1; $i <= count($rows_dates); $i++) {
					$row_dates = $rows_dates[$i - 1];
					$arrdays[$x][number_format($row_dates->DAY, 0)] = $row_dates->total;
				}
			}

			$days_values = '';
			for ($i = 1; $i <= $days; $i++) {
				$days_values .= $i . ';';
				for ($x = 0; $x < $nrwks; $x++) {
					$days_values .= (isset($arrdays[$x][$i]) ? $arrdays[$x][$i] . ($nrwks - $x == 1 ? '\n' : ';') : '0' . ($nrwks - $x == 1 ? '\n' : ';'));
				}
			}

			$html .= '<div id="chart_wkdays"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $days_values, str_replace("%CHARTNAME%", "chart_wkdays", str_replace("%LABELS%", $wkslabels, $lines_html)));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-2" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_workgroup') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets, w.wkdesc FROM #__support_ticket t, #__support_workgroup w WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND w.id=t.id_workgroup " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY w.wkdesc ORDER BY w.wkdesc ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';

			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= $row->wkdesc . ';' . $row->tickets . '\n';
			}

			$html .= '<div id="chart_wk"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_wk", $chart_html));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-3" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_status_group') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets, s.status_group FROM #__support_ticket t, #__support_status s WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND s.id=t.id_status " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY s.status_group ORDER BY s.status_group ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';

			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= ($row->status_group == 'O' ? JText::_('open') : JText::_('closed')) . ';' . $row->tickets . '\n';
			}

			$html .= "<div id='chart_sgroup'><strong>" . JText::_('upgrade_flash') . "</strong></div>" . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_sgroup", $chart_html));

			if (!$print) {
				$html .= '</div>';

				// Tickets per Status
				$html .= '<div id="report-4" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_status') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets, s.description FROM #__support_ticket t, #__support_status s WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND s.id=t.id_status " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY s.status_group ORDER BY s.status_group ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';

			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= $row->description . ';' . $row->tickets . '\n';
			}

			$html .= '<div id="chart_status"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_status", $chart_html));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-5" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_priority') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets, p.description FROM #__support_ticket t, #__support_priority p WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND p.id=t.id_priority " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY p.description ORDER BY p.description ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';

			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= $row->description . ';' . $row->tickets . '\n';
			}

			$html .= '<div id="chart_priority"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_priority", $chart_html));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-6" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_source') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets, t.source FROM #__support_ticket t WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY t.source ORDER BY t.source ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';

			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				switch ($row->source) {
					case 'P':
						$source = JText::_('phone');
						break;
					case 'F':
						$source = JText::_('fax');
						break;
					case 'M':
						$source = JText::_('email');
						break;
					case 'W':
						$source = JText::_('website');
						break;
					case 'O':
						$source = JText::_('other');
						break;
				}
				$series .= $source . ';' . $row->tickets . '\n';
			}

			$html .= '<div id="chart_source"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_source", $chart_html));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-7" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_category') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets, CASE WHEN t.id_category=0 THEN '" . JText::_('uncategorized') . "' ELSE c.name END as category FROM #__support_ticket t LEFT JOIN #__support_category c ON t.id_category=c.id WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY category ORDER BY category ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';

			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= $row->category . ';' . $row->tickets . '\n';
			}

			$html .= '<div id="chart_category"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_category", $chart_html));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-8" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_category') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets, u.name FROM #__support_ticket t, #__users u WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " AND t.assign_to=u.id GROUP BY u.name ORDER BY u.name ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';

			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= $row->name . ';' . $row->tickets . '\n';
			}

			$html .= '<div id="chart_staff"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_staff", $chart_html));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-9" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_year_comparison') . '</h2>';
			}

			$database->setQuery("SELECT COUNT(t.id) AS tickets FROM #__support_ticket t WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . "");
			$cur_year = null;
			$cur_year = $database->loadObject();
			echo $database->getErrorMsg();
			$database->setQuery("SELECT COUNT(t.id) AS tickets FROM #__support_ticket t WHERE year(t.date)=" . ($database->quote($year) - 1) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . "");
			$prv_year = null;
			$prv_year = $database->loadObject();
			echo $database->getErrorMsg();
			$series = '';
			$series .= intval($year) . ';' . intval($cur_year->tickets) . '\n';
			$series .= intval($year - 1) . ';' . intval($prv_year->tickets);
			$html .= '<div id="chart_comparison"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_comparison", $chart_html));

			if (!$print) {
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div style="clear:both;"></div>';
			}
		}
		$html .= "</td></tr>";

		return ($html != '' ? $html : JText::_('no_info_build_graph'));
	}

	function ClientAnalysis($year, $month, $id_workgroup, $id_client, $id_user)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_maqmahelpdesk/includes/amcharts/ampie/swfobject.js"></script>';
		$print = JRequest::getVar('print', 0, '', 'int');

		// LINE CHARTS
		ob_start(); ?>
    <script type="text/javascript" defer="defer">
        // <![CDATA[
        var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline.swf", "amline", "100%", "600", "8", "#FFFFFF");
        so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/");
        so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline_settings.xml"));
        so.addVariable("additional_chart_settings", encodeURIComponent('<settings><graphs>%LABELS%</graphs></settings>'));
        so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
        so.addVariable("loading_data", "<?php echo JText::_('loading_data'); ?>");
        so.addVariable("preloader_color", "#999999");
        so.addParam("wmode", "transparent");
        so.write("%CHARTNAME%");
        // ]]>
    </script><?php
		$lines_html = ob_get_contents();
		ob_end_clean();

		// PIE CHARTS
		ob_start(); ?>
    <script type="text/javascript" defer="defer">
        // <![CDATA[
        var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie.swf", "ampie", "100%", "600", "8", "#FFFFFF");
        so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/");
        so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie_settings.xml"));
        so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
        so.addVariable("loading_data", "<?php echo JText::_('loading_data'); ?>");
        so.addVariable("preloader_color", "#999999");
        so.addParam("wmode", "transparent");
        so.write("%CHARTNAME%");
        // ]]>
    </script><?php
		$chart_html = ob_get_contents();
		ob_end_clean();

		$html = '';
		$html .= "<tr><td>";
		$days = HelpdeskDate::GetMonthDays($year, $month);

		$database->setQuery("SELECT COUNT(*) FROM #__support_ticket t WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month));

		if ($database->loadResult() > 0) {
			if (!$print) {
				ob_start(); ?>
            <script type="text/javascript">
                function ShowReport(ID) {
                    for (i = 0; i <= 9; i++) {
                        if (i != ID) {
                            $jMaQma("#report-" + i).hide("fade");
                        } else {
                            $jMaQma("#report-" + i).show("fade");
                        }
                    }
                }
            </script>
            <div style="width:20%;float:left;">
                <div class="t">
                    <div class="t">
                        <div class="t"></div>
                    </div>
                </div>
                <div class="m">
                    <p>&bull; <a href="javascript:;" onclick="ShowReport(1);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_day_client'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(2);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_client'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(3);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_status_group'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(4);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_status'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(5);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_priority'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(6);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_source'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(7);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_category'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(8);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_assign_user'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(9);"
                                 class="submenu"><?php echo JText::_('report_year_comparison'); ?></a></p>

                    <div class="clr"></div>
                </div>
                <div class="b">
                    <div class="b">
                        <div class="b"></div>
                    </div>
                </div>
            </div>
				<div style="width:80%;float:left;"><?php
				$html .= ob_get_contents();
				ob_end_clean();
				$html .= '<div id="report-1">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_day_client') . '</h2>';
			}
			$sql = "select id, clientname from #__support_client order by clientname";
			$database->setQuery($sql);
			$rows = $database->loadObjectList();
			$nrclients = count($rows);
			$arrdays = array();
			$clientlabels = '';

			for ($x = 0; $x < count($rows); $x++) {
				$row = $rows[$x];
				$clientlabels .= '<graph gid="' . ($x + 1) . '"><title>' . addslashes($row->clientname) . "</title><line_width>3</line_width><bullet>round_outlined</bullet><bullet_size>10</bullet_size><color_hover>#000000</color_hover></graph>";

				$sql = "SELECT DATE_FORMAT( t.date, '%d' ) AS DAY , count(*) AS total FROM `#__support_ticket` AS t WHERE month(`date`)=" . $database->quote($month) . " and year(`date`)=" . $database->quote($year) . " AND id_client='" . $row->id . "' " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY DATE_FORMAT( date, '%d' ) ORDER BY DATE_FORMAT( date, '%d' )";

				$database->setQuery($sql);
				$rows_dates = $database->loadObjectList();
				$prev_day = 1;
				for ($i = 1; $i <= count($rows_dates); $i++) {
					$row_dates = $rows_dates[$i - 1];
					$arrdays[$x][number_format($row_dates->DAY, 0)] = $row_dates->total;
				}
			}

			$days_values = '';
			for ($i = 1; $i <= $days; $i++) {
				$days_values .= $i . ';';
				for ($x = 0; $x < $nrclients; $x++) {
					$days_values .= (isset($arrdays[$x][$i]) ? $arrdays[$x][$i] . ($nrclients - $x == 1 ? '\n' : ';') : '0' . ($nrclients - $x == 1 ? '\n' : ';'));
				}
			}

			$html .= '<div id="chart_clidays"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $days_values, str_replace("%CHARTNAME%", "chart_clidays", str_replace("%LABELS%", $clientlabels, $lines_html)));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-2" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_client') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, c.clientname FROM #__support_ticket t, #__support_client c WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND c.id=t.id_client " . ($id_workgroup > 0 ? 'AND t.id_client=' . $id_client : '') . " GROUP BY c.clientname ORDER BY c.clientname ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= $row->clientname . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_cli"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_cli", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-3" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_status_group') . '</h2>';
			}
			$x_axis = '';
			$database->setQuery("SELECT COUNT(t.id) AS tickets, s.status_group FROM #__support_ticket t, #__support_status s, #__support_client c, #__support_client_users cu WHERE c.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND s.id=t.id_status " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND c.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . " GROUP BY s.status_group ORDER BY s.status_group ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$series .= ($row->status_group == 'O' ? 'Open' : 'Closed') . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_status"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_status", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-4" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_status') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, s.description FROM #__support_ticket t, #__support_status s, #__support_client c, #__support_client_users cu WHERE c.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND s.id=t.id_status " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND c.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . " GROUP BY s.status_group ORDER BY s.status_group ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->description . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_tstatus"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_tstatus", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-5" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_priority') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, p.description FROM #__support_ticket t, #__support_priority p, #__support_client c, #__support_client_users cu WHERE c.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND p.id=t.id_priority " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND c.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . " GROUP BY p.description ORDER BY p.description ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->description . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_tpriotity"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_tpriotity", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-6" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_source') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, t.source FROM #__support_ticket t, #__support_client c, #__support_client_users cu WHERE c.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND c.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . " GROUP BY t.source ORDER BY t.source ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				switch ($row->source) {
					case 'P':
						$source = JText::_('phone');
						break;
					case 'F':
						$source = JText::_('fax');
						break;
					case 'M':
						$source = JText::_('email');
						break;
					case 'W':
						$source = JText::_('website');
						break;
					case 'O':
						$source = JText::_('other');
						break;
				}
				$series .= $source . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_source"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_source", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-7" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_category') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, CASE WHEN t.id_category=0 THEN '" . JText::_('uncategorized') . "' ELSE c.name END as category FROM #__support_ticket t LEFT JOIN #__support_category c ON t.id_category=c.id, #__support_client cl, #__support_client_users cu WHERE cl.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND cl.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . " GROUP BY category ORDER BY category ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->category . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_category"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_category", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-8" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_assign_user') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, u.name FROM #__support_ticket t, #__users u, #__support_client c, #__support_client_users cu WHERE c.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND c.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . " AND t.assign_to=u.id GROUP BY u.name ORDER BY u.name ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->name . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_assign_user"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_assign_user", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-9" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_year_comparison') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets FROM #__support_ticket t, #__support_client c, #__support_client_users cu WHERE c.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND c.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . "");
			$cur_year = null;
			$cur_year = $database->loadObject();
			echo $database->getErrorMsg();
			$database->setQuery("SELECT COUNT(t.id) AS tickets FROM #__support_ticket t, #__support_client c, #__support_client_users cu WHERE c.id=cu.id_client AND cu.id_user=t.id_user AND year(t.date)=" . ($database->quote($year) - 1) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? " AND t.id_workgroup='" . $id_workgroup . "' " : " ") . ($id_client > 0 ? " AND c.id='" . $id_client . "' " : " ") . ($id_user > 0 ? " AND t.id_user='" . $id_user . "' " : " ") . "");
			$prv_year = null;
			$prv_year = $database->loadObject();
			echo $database->getErrorMsg();
			$series = '';
			$series .= intval($year) . ';' . $cur_year->tickets . '\n';
			$series .= intval($year - 1) . ';' . $prv_year->tickets . '\n';
			$html .= '<div id="chart_ycompare"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_ycompare", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div style="clear:both;"></div>';
			}
		}
		$html .= "</td></tr>";

		return ($html != '' ? $html : JText::_('no_info_build_graph'));
	}

	function StaffAnalysis($year, $month, $id_workgroup, $id_client, $id_user)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_maqmahelpdesk/includes/amcharts/ampie/swfobject.js"></script>';
		$print = JRequest::getVar('print', 0, '', 'int');

		// LINE CHARTS
		ob_start(); ?>
    <script type="text/javascript" defer="defer">
        // <![CDATA[
        var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline.swf", "amline", "100%", "600", "8", "#FFFFFF");
        so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/");
        so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline_settings.xml"));
        so.addVariable("additional_chart_settings", encodeURIComponent('<settings><graphs>%LABELS%</graphs></settings>'));
        so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
        so.addVariable("loading_data", "<?php echo JText::_('loading_data'); ?>");
        so.addVariable("preloader_color", "#999999");
        so.addParam("wmode", "transparent");
        so.write("%CHARTNAME%");
        // ]]>
    </script><?php
		$lines_html = ob_get_contents();
		ob_end_clean();

		// PIE CHARTS
		ob_start(); ?>
    <script type="text/javascript" defer="defer">
        // <![CDATA[
        var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie.swf", "ampie", "100%", "600", "8", "#FFFFFF");
        so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/");
        so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie_settings.xml"));
        so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
        so.addVariable("loading_data", "<?php echo JText::_('loading_data'); ?>");
        so.addVariable("preloader_color", "#999999");
        so.addParam("wmode", "transparent");
        so.write("%CHARTNAME%");
        // ]]>
    </script><?php
		$chart_html = ob_get_contents();
		ob_end_clean();

		$html = '';
		$html .= "<tr><td>";
		$days = HelpdeskDate::GetMonthDays($year, $month);

		$database->setQuery("SELECT COUNT(*) FROM #__support_ticket t WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month));
		if ($database->loadResult() > 0) {
			if (!$print) {
				ob_start(); ?>
            <script type="text/javascript">
                function ShowReport(ID) {
                    for (i = 0; i <= 8; i++) {
                        if (i != ID) {
                            $jMaQma("#report-" + i).hide("fade");
                        } else {
                            $jMaQma("#report-" + i).show("fade");
                        }
                    }
                }
            </script>
            <div style="width:20%;float:left;">
                <div class="t">
                    <div class="t">
                        <div class="t"></div>
                    </div>
                </div>
                <div class="m">
                    <p>&bull; <a href="javascript:;" onclick="ShowReport(1);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_day_support_staff'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(2);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_workgroup'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(3);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_status'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(4);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_source'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(5);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_priority'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(6);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_category'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(7);"
                                 class="submenu"><?php echo JText::_('report_tickets_per_assign_user'); ?></a></p>

                    <p>&bull; <a href="javascript:;" onclick="ShowReport(8);"
                                 class="submenu"><?php echo JText::_('report_year_comparison'); ?></a></p>

                    <div class="clr"></div>
                </div>
                <div class="b">
                    <div class="b">
                        <div class="b"></div>
                    </div>
                </div>
            </div>
				<div style="width:80%;float:left;"><?php
				$html .= ob_get_contents();
				ob_end_clean();
				$html .= '<div id="report-1">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_day_support_staff') . '</h2>';
			}

			$prev_day = 1;
			$days = HelpdeskDate::GetMonthDays($year, $month);

			$sql = "select u.id, u.name from #__users AS u INNER JOIN #__support_permission AS p ON u.id=p.id_user GROUP BY u.id order by name";
			$database->setQuery($sql);
			$rows = $database->loadObjectList();
			$nrclients = count($rows);
			$arrdays = array();
			$clientlabels = '';

			for ($x = 0; $x < count($rows); $x++) {
				$row = $rows[$x];
				$clientlabels .= '<graph gid="' . ($x + 1) . '"><title>' . addslashes($row->name) . "</title><line_width>3</line_width><bullet>round_outlined</bullet><bullet_size>10</bullet_size><color_hover>#000000</color_hover></graph>";

				$sql = "SELECT DATE_FORMAT( t.date, '%d' ) AS DAY , count(*) AS total FROM `#__support_ticket` AS t WHERE month(t.`date`)=" . $database->quote($month) . " and year(t.`date`)=" . $database->quote($year) . " AND t.assign_to='" . $row->id . "'" . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " GROUP BY DATE_FORMAT( t.date, '%d' ) ORDER BY DATE_FORMAT( t.date, '%d' )";

				$database->setQuery($sql);
				$rows_dates = $database->loadObjectList();
				$prev_day = 1;
				for ($i = 1; $i <= count($rows_dates); $i++) {
					$row_dates = $rows_dates[$i - 1];
					$arrdays[$x][number_format($row_dates->DAY, 0)] = $row_dates->total;
				}
			}

			$days_values = '';
			for ($i = 1; $i <= $days; $i++) {
				$days_values .= $i . ';';
				for ($x = 0; $x < $nrclients; $x++) {
					$days_values .= (isset($arrdays[$x][$i]) ? $arrdays[$x][$i] . ($nrclients - $x == 1 ? '\n' : ';') : '0' . ($nrclients - $x == 1 ? '\n' : ';'));
				}
			}

			$html .= '<div id="chart_supportdays"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $days_values, str_replace("%CHARTNAME%", "chart_supportdays", str_replace("%LABELS%", $clientlabels, $lines_html)));

			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-2" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_workgroup') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, w.wkdesc FROM #__support_ticket t, #__support_workgroup w WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND w.id=t.id_workgroup " . ($id_workgroup > 0 ? ' AND t.id_workgroup=' . $id_workgroup : '') . ($id_user > 0 ? ' AND t.assign_to=' . $id_user : '') . " GROUP BY w.wkdesc ORDER BY w.wkdesc ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->wkdesc . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_ticketswks"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_ticketswks", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-3" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_status') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, s.description FROM #__support_ticket t, #__support_status s WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND s.id=t.id_status " . ($id_workgroup > 0 ? ' AND t.id_workgroup=' . $id_workgroup : '') . ($id_user > 0 ? ' AND t.assign_to=' . $id_user : '') . " GROUP BY s.status_group ORDER BY s.status_group ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->description . ';' . $row->tickets . '\n';
			}
			$html .= '<h3>Tickets per Status</h3><div id="chart_ticketstatus"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_ticketstatus", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-4" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_source') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, t.source FROM #__support_ticket t WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? ' AND t.id_workgroup=' . $id_workgroup : '') . ($id_user > 0 ? ' AND t.assign_to=' . $id_user : '') . " GROUP BY t.source ORDER BY t.source ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				switch ($row->source) {
					case 'P':
						$source = JText::_('phone');
						break;
					case 'F':
						$source = JText::_('fax');
						break;
					case 'M':
						$source = JText::_('email');
						break;
					case 'W':
						$source = JText::_('website');
						break;
					case 'O':
						$source = JText::_('other');
						break;
				}
				$series .= $source . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_ticketsource"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_ticketsource", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-5" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_priority') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, p.description FROM #__support_ticket t, #__support_priority p WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " AND p.id=t.id_priority " . ($id_workgroup > 0 ? ' AND t.id_workgroup=' . $id_workgroup : '') . ($id_user > 0 ? ' AND t.assign_to=' . $id_user : '') . " GROUP BY p.description ORDER BY p.description ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->description . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_ticket_priority"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_ticket_priority", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-6" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_category') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, CASE WHEN t.id_category=0 THEN '" . JText::_('uncategorized') . "' ELSE c.name END as category FROM #__support_ticket t LEFT JOIN #__support_category c ON t.id_category=c.id WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? ' AND t.id_workgroup=' . $id_workgroup : '') . ($id_user > 0 ? ' AND t.assign_to=' . $id_user : '') . " GROUP BY category ORDER BY category ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->category . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_ticket_category"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_ticket_category", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-7" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_tickets_per_assign_user') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets, u.name FROM #__support_ticket t, #__users u WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? 'AND t.id_workgroup=' . $id_workgroup : '') . " AND t.assign_to=u.id GROUP BY u.name ORDER BY u.name ASC");
			$rows = $database->loadObjectList();
			echo $database->getErrorMsg();
			$z = 0;
			$series = '';
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				$z = $z + 1;
				$series .= $row->name . ';' . $row->tickets . '\n';
			}
			$html .= '<div id="chart_ticket_assign_user"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_ticket_assign_user", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '<div id="report-8" style="display:none;">';
			} else {
				$html .= '<h2>' . JText::_('report_year_comparison') . '</h2>';
			}
			$database->setQuery("SELECT COUNT(t.id) AS tickets FROM #__support_ticket t WHERE year(t.date)=" . $database->quote($year) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? ' AND t.id_workgroup=' . $id_workgroup : '') . ($id_user > 0 ? ' AND t.assign_to=' . $id_user : '') . "");
			$cur_year = null;
			$cur_year = $database->loadObject();
			echo $database->getErrorMsg();
			$database->setQuery("SELECT COUNT(t.id) AS tickets FROM #__support_ticket t WHERE year(t.date)=" . ($database->quote($year) - 1) . " AND month(t.date)=" . $database->quote($month) . " " . ($id_workgroup > 0 ? ' AND t.id_workgroup=' . $id_workgroup : '') . ($id_user > 0 ? ' AND t.assign_to=' . $id_user : '') . "");
			$prv_year = null;
			$prv_year = $database->loadObject();
			echo $database->getErrorMsg();
			$series = '';
			$series .= intval($year) . ';' . $cur_year->tickets . '\n';
			$series .= intval($year - 1) . ';' . $prv_year->tickets . '\n';
			$html .= '<div id="chart_year_compare"><strong>' . JText::_('upgrade_flash') . '</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", "chart_year_compare", $chart_html));
			if (!$print) {
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<div style="clear:both;"></div>';
			}
		}
		$html .= "</td></tr>";
		return ($html != '' ? $html : JText::_('no_info_build_graph'));
	}

	function Timesheet($year, $month, $id_workgroup, $id_client, $type = 'S')
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();
		$month = sprintf('%02d', $month);
		$number_of_days = HelpdeskDate::GetMonthDays($year, $month);

		//$where = " 1=1 ";
		//$where .= ( isset($_GET['client']) & (@$_GET['client'] > 0) )? " AND cu.id_client=".@$_GET['client']." " :"";
		//$where .= ( isset($_GET['id_user']) & (@$_GET['id_user'] > 0) )? " AND u.id=".@$_GET['id_user']." " :"";
		//$where .= ( isset($_GET['id_workgroup']) & (@$_GET['id_workgroup'] > 0) )? " AND w.id_workgroup=".@$_GET['id_workgroup']." " :"";
		$id_user = isset($_GET['id_user']) ? $_GET['id_user'] : 0;

		$sql = "SELECT DISTINCT(p.`id_user`) AS value, u.`name` AS name, u.`username` AS username
				FROM #__users as u INNER JOIN #__support_permission AS p ON p.id_user=u.id
				GROUP BY value
				ORDER BY value";
		$database->setQuery($sql);
		$rows_staff = $database->loadObjectList(); ?>

    <style type="text/css">
        .line {
            text-align: right;
            border-right: 1px #cccccc solid;
        }

        .line_text {
            text-align: left;
            border-right: 1px #cccccc solid;
        }

        .total {
            text-align: center;
            font-weight: bold;
            border-right: 1px #cccccc solid;
            color: #000000;
        }

        .total_text {
            text-align: left;
            font-weight: bold;
            border-right: 1px #cccccc solid;
            color: #000000;
        }
    </style>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <td class="subcabecalho" height="30" colspan="35">
                <b><?php echo JText::_('monthly_timesheet') . " - " . HelpdeskDate::GetMonthName($month) . " " . $year; ?></b>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th></th>
			<?php
			for ($i = 1; $i <= $number_of_days; $i++) {
				print '<th>' . $i . '</th>';
			} ?>
            <th>TOTAL</th>
        </tr>

        <!-- LINE --><?php
			$k = 0;
			for ($x = 0; $x < count($rows_staff); $x++) {
				$row_staff = $rows_staff[$x];

				print '<tr class="row' . $k . '">';
				print '<td nowrap>' . $row_staff->name . '</td>';

				$total = 0;
				for ($i = 1; $i <= $number_of_days; $i++) {
					$value = HelpdeskReport::GetTimeForDayInDecimals(($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i)), $id_client, $id_workgroup, /*$id_user*/$row_staff->value, /*$row_staff->value*/0);
					$total = $total + $value;
					$value = HelpdeskDate::SecondsToHours($value,true,false,false);
					print '<td class="line" width="10">' . ($value!='00:00' ? $value : '&nbsp;') . '</td>';
				}
				$total = HelpdeskDate::SecondsToHours($total,true,false,false);
				print '<td class="line">' . ($total!='00:00' ? $total : '&nbsp;') . '</td>';
				print '</tr>';

				$k = 1 - $k;
			} ?>
        </tbody>
        <!-- LINE -->

        <!-- TOTAL
			<tfoot>
			<tr>
				<th>TOTAL</th>
				<?php
			$total = 0;
			for ($i = 1; $i <= $number_of_days; $i++) {
				$value = HelpdeskReport::GetTimeForDayInDecimals(($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i)), $id_client, $id_workgroup, $id_user, $row_staff->value);
				$total = $total + $value;
				$value = HelpdeskReport::CheckTime($value);
				print '<th width="10"><b>' . ( $value != '00:00' ? $value : '&nbsp;' ) . '</b></th>';
			}
			$total = HelpdeskReport::CheckTime($total); ?>

				<th><b><?php echo ( $total != '00:00' ? $total : '&nbsp;' ); ?></b></th>
			</tr>
			</tfoot> -->
        <!-- TOTAL -->

    </table><?php
		if ($type == 'D') {
			?>
        <br/><br/><?php

			// Get Activity Types
			$sql = "SELECT `id` AS value, `description` AS text FROM #__support_activity_type WHERE published='1' ORDER BY description";
			$database->setQuery($sql);
			$rows_acttype = $database->loadObjectList();

			?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <td class="subcabecalho" height="30" colspan="35">
                    <b><?php echo JText::_('monthly_timesheet_detailed') . " - " . HelpdeskDate::GetMonthName($month) . "&nbsp;" . $year; ?></b>
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th><?php echo JText::_('staff_member'); ?></th>
				<?php
				$k = 0;
				for ($x = 0; $x < count($rows_acttype); $x++) {
					$row_acttype = $rows_acttype[$x];
					print '<th colspan="3">' . $row_acttype->text . '</th>';
				}
				?>
                <th colspan="3" nowrap="nowrap"><?php echo JString::strtoupper(JText::_('total')); ?></th>
            </tr>

            <tr>
                <th>&nbsp;</th>
				<?php
				$k = 0;
				for ($x = 0; $x < count($rows_acttype); $x++) {
					$row_acttype = $rows_acttype[$x];
					print '<th nowrap="nowrap"> ' . JText::_('activities') . '</th>';
					print '<th nowrap="nowrap"> ' . JText::_('hh_mm') . ' </th>';
					print '<th nowrap="nowrap"> ' . $supportConfig->currency . ' ' . JText::_('value') . ' </th>';
				}
				?>
                <th nowrap="nowrap"><?php echo JText::_('activities'); ?></th>
                <th nowrap="nowrap"><?php echo JText::_('hh_mm'); ?></th>
                <th nowrap="nowrap"><?php echo $supportConfig->currency . ' ' . JText::_('value'); ?></th>
            </tr>

            <!-- LINE --><?php
				$k = 0;
				for ($x = 0; $x < count($rows_staff); $x++) {
					$row_staff = $rows_staff[$x];
					print '<tr class="row' . $k . '">';
					print '<td nowrap="nowrap"><b>' . $row_staff->name . '</b></td>';

					$total = 0;
					for ($i = 0; $i < count($rows_acttype); $i++) {
						$row_acttype = $rows_acttype[$i];

						$from_datetime = $year . '-' . $month . '-01 00:00:00';
						$days = HelpdeskDate::GetMonthDays($year, $month);
						$to_datetime = $year . '-' . $month . '-' . $days . ' 23:59:59';

						print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals($row_staff->value, $row_acttype->value, $from_datetime, $to_datetime, 'numrecs', 0, $id_client) . '</td>';
						print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals($row_staff->value, $row_acttype->value, $from_datetime, $to_datetime, 'duration', 0, $id_client) . '</td>';
						print '<td class="line" width="75"><div align="right">' . HelpdeskReport::GetActivitiesTotals($row_staff->value, $row_acttype->value, $from_datetime, $to_datetime, 'dollarval', 0, $id_client) . '</div></td>';
					}
					print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals($row_staff->value, 0, $from_datetime, $to_datetime, 'numrecs', 0, $id_client) . '</td>';
					print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals($row_staff->value, 0, $from_datetime, $to_datetime, 'duration', 0, $id_client) . '</td>';
					print '<td class="line" width="75"><div align="right">' . HelpdeskReport::GetActivitiesTotals($row_staff->value, 0, $from_datetime, $to_datetime, 'dollarval', 0, $id_client) . '</div></td>';
					print '</tr>';

					$k = 1 - $k;
				} ?>
            <!-- LINE -->
            </tbody>
        </table><?php
		}
	}

	function StatusTickets($year, $month, $id_workgroup, $id_client, $f_status, $f_staff, $custom_fields, $detail = 0)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();
		$print = JRequest::getVar('print', 0, '', 'int');

		$GLOBALS['id_workgroup'] = $id_workgroup;
		$GLOBALS['month'] = $month;
		$GLOBALS['year'] = $year;
		$GLOBALS['f_status'] = $f_status;
		$GLOBALS['id_client'] = $id_client;
		$GLOBALS['f_staff'] = $id_client;

		$sql = "
SELECT 
  DISTINCT(s.`description`) as status,
  COUNT(l.`id_ticket`) as tickets,
  SUM(IF(
			l.`time_elapse`='0',
			(TIMEDIFF(NOW(), l.`date_time`)),
			l.`time_elapse`
		)
	  ) AS lenght
FROM 
  #__support_log l
	LEFT JOIN #__support_ticket t
	  ON  l.id_ticket=t.id
	LEFT JOIN #__support_status s
	  ON s.`id` = l.`value`
WHERE 
  YEAR(l.`date_time`)=" . $database->quote($year) . " AND 
  MONTH(l.`date_time`)=" . $database->quote($month) . "  AND 
  l.`field` = 'status'
  " . ($f_staff > 0 ? "AND t.assign_to='" . $f_staff . "'" : "") . "
  " . ($f_status > 0 ? "AND l.value='" . $f_status . "'" : "") . "
  " . ($id_client > 0 ? "AND l.id_user='" . $id_client . "'" : "") . "
  " . ($id_workgroup > 0 ? "AND t.id_workgroup='" . $id_workgroup . "'" : "") . "
GROUP BY 
  l.`value` 
ORDER BY 
  l.`value`, 
  l.`date_time` 
";

		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		// Get workgroup name
		if ($id_workgroup > 0) {
			$database->setQuery("SELECT wkdesc FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
			$wk_desc = $database->loadResult();
		} else {
			$wk_desc = JText::_('all_workgroups');
		}


		// get status id
		if ($f_status > 0) {
			$sql = "SELECT `description` AS text FROM #__support_status WHERE id='" . $f_status . "'";
			$database->setQuery($sql);
			$status_desc = $database->loadResult();
		} else {
			$status_desc = JText::_('all_status');
		}


		// get staff name
		if ($f_staff > 0) {
			$database->setQuery("SELECT name FROM #__users WHERE id='$f_staff'");
			$staff_desc = $database->loadResult();
		} else {
			$staff_desc = JText::_('all_staff');
		}


		?>
    <style type="text/css">
        .line {
            text-align: right;
            border-right: 1px #cccccc solid;
        }

        .line_text {
            text-align: left;
            border-right: 1px #cccccc solid;
        }

        .total {
            text-align: center;
            font-weight: bold;
            border-right: 1px #cccccc solid;
            color: #000000;
        }

        .total_text {
            text-align: left;
            font-weight: bold;
            border-right: 1px #cccccc solid;
            color: #000000;
        }
    </style>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <td class="subcabecalho" height="30" colspan="35"><b><?php echo HelpdeskDate::GetMonthName($month); ?>
                &nbsp;<?php echo $year; ?> / <?php echo $wk_desc; ?> / <?php echo $status_desc; ?>
                / <?php echo $staff_desc; ?></b></td>
        </tr>
        <tr>
            <th nowrap><?php echo JText::_('status'); ?></th>
            <th nowrap><?php echo JText::_('ticket_report'); ?></th>
            <th nowrap
                    ><?php echo JText::_('duedate_explain_length_time') . " " . JText::_('time'); ?></th>
            <th nowrap><?php echo JText::_('average') . " " . JText::_('time'); ?></th>
        </tr>
        </thead>
        <tbody>
        <!-- LINE --><?php

			if (count($rows) == 0) {
				$colspan = 10 + count($custom_fields);
				print '<tr class="row0"><td colspan="' . $colspan . '">' . JText::_('no_results') . '</td></tr>';
			} else {
				$k = 0;
				for ($x = 0; $x < count($rows); $x++) {
					$row = $rows[$x];
					print '<tr class="row' . $k . '">';
					print '<td nowrap>' . $row->status . '</td>';
					print '<td nowrap>' . $row->tickets . '</td>';
					print '<td nowrap>' . HelpdeskDate::FormatDuration($row->lenght) . '</td>';
					$avg = $row->lenght / $row->tickets;
					print '<td nowrap>' . HelpdeskDate::FormatDuration($avg) . '</td>';
					print '</tr>';
					$k = 1 - $k;
				}
			}?>
        </tbody>
        <!-- LINE -->
		<?php
		?>

    </table><?php
	}

	function UserTimesheet($year, $month, $id_workgroup, $id_client, $type = 'S', $user)
	{
		$user = JFactory::getUser();
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();
		$number_of_days = HelpdeskDate::GetMonthDays($year, $month);

		$sql = "SELECT DISTINCT(c.id), c.clientname
				FROM #__support_ticket t
					 LEFT JOIN #__support_client c ON c.id=t.id_client
					 INNER JOIN #__support_ticket_resp AS r ON r.id_ticket=t.id
				WHERE YEAR(r.`date`)=" . $database->quote($year) . "
				  AND MONTH(r.`date`)=" . $database->quote($month) . "
				  AND t.assign_to='" . $user->id . "'
				ORDER BY c.clientname";
		$database->setQuery($sql);
		$rows_clients = $database->loadObjectList(); ?>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <td class="sectiontableheader" height="30" colspan="35">
                <b><?php echo JText::_('monthly_timesheet') . " - " . HelpdeskDate::GetMonthName($month) . "&nbsp;" . $year; ?></b>
            </td>
        </tr>
        <tr class="contentpane">
            <th><?php echo JText::_('tpl_client');?></th>
			<?php
			for ($i = 1; $i <= $number_of_days; $i++) {
				print '<th>' . $i . '</th>';
			} ?>
            <th><?php echo JText::_('total_time');?></th>
        </tr>
        </thead>

        <!-- LINE -->
        <tbody>
			<?php
			if (count($rows_clients) > 0) {
				$k = 0;
				for ($x = 0; $x < count($rows_clients); $x++) {
					$row_staff = $rows_clients[$x];
					print '<tr class="row' . $k . '">';
					print '<td nowrap>' . ($row_staff->clientname != '' ? "(" . $row_staff->id . ') ' . $row_staff->clientname : JText::_('no_customer')) . '</td>';

					$total = 0;
					for ($i = 1; $i <= $number_of_days; $i++) {
						$value = HelpdeskReport::GetTimeForDayInDecimals(($year . '-' . ($month < 10 ? '0' . $month : $month) . '-' . ($i < 10 ? '0' . $i : $i)), ($row_staff->clientname == '' ? '-' : $row_staff->id), $id_workgroup, 0, $user->id);
						$total = $total + $value;
						$value = HelpdeskDate::SecondsToHours($value,true,false,false);
						print '<td class="line" width="10">' . $value . '</td>';
					}
					$total = HelpdeskDate::SecondsToHours($total,true,false,false);
					print '<td class="line">' . $total . '</td>';
					print '</tr>';

					$k = 1 - $k;
				}
			} else {
				$cols = $number_of_days + 2;
				print '<tr class="row0"><td nowrap colspan="' . $cols . '">' . JText::_('tpl_noactivities') . '</td></tr>';
			}
			?>
        <!-- LINE -->
        </tbody>

        <!-- TOTAL -->
        <tfoot>
        <tr style="background-color:#DEDBD3;">
            <th><?php echo JText::_('total_time');?></th>
			<?php
			$total = 0;
			for ($i = 1; $i <= $number_of_days; $i++) {
				$value = HelpdeskReport::GetTimeForDayInDecimals(($year . '-' . ($month < 10 ? '0' . $month : $month) . '-' . ($i < 10 ? '0' . $i : $i)), '-1', $id_workgroup, 0, $user->id);
				$total = $total + $value;
				$value = HelpdeskDate::SecondsToHours($value,true,false,false);
				print '<th width="10"><b>' . $value . '</b></th>';
			}
			$total = HelpdeskDate::SecondsToHours($total,true,false,false); ?>
            <th><b><?php echo $total; ?></b></th>
        </tr>
        </tfoot>
        <!-- TOTAL -->

    </table><?php
		/* customize: hide detail timesheet report tab. start comment . */
		if ($type == 'D') {
			?>
        <br/><br/><?php

			// Get Activity Types
			$sql = "SELECT DISTINCT(t.`id`) AS value, t.`description` AS text FROM #__support_activity_type t INNER JOIN #__support_ticket_resp r ON r.id_activity_type=t.id INNER JOIN #__support_ticket tc ON tc.id=r.id_ticket WHERE t.published='1' AND MONTH(r.date)=" . $database->quote($month) . " AND YEAR(r.date)=" . $database->quote($year) . " AND tc.assign_to='" . $user->id . "' ORDER BY t.description";
			$database->setQuery($sql);
			$rows_acttype = $database->loadObjectList();

			?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class="sectiontableheader" height="30" colspan="35">
                    <b><?php echo JText::_('monthly_timesheet_detailed') . " - " . HelpdeskDate::GetMonthName($month) . "&nbsp;" . $year; ?></b>
                </th>
            </tr>
            </thead>
            <tr class="contentpane">
                <th><?php echo JText::_('tpl_client');?></th>
				<?php
				$k = 0;
				for ($x = 0; $x < count($rows_acttype); $x++) {
					$row_acttype = $rows_acttype[$x];
					print '<th colspan="3">' . $row_acttype->text . '</th>';
				}
				?>
                <th colspan="3" nowrap="nowrap"><?php echo JString::strtoupper(JText::_('total')); ?></th>
            </tr>

            <tr class="contentpane">
                <th>&nbsp;</th>
				<?php
				$k = 0;
				for ($x = 0; $x < count($rows_acttype); $x++) {
					$row_acttype = $rows_acttype[$x];
					print '<th nowrap="nowrap"> ' . JText::_('activities') . '</th>';
					print '<th nowrap="nowrap"> ' . JText::_('hh_mm') . ' </th>';
					print '<th nowrap="nowrap"> ' . $supportConfig->currency . ' ' . JText::_('value') . ' </th>';
				}
				?>
                <th nowrap="nowrap"><?php echo JText::_('activities'); ?></th>
                <th nowrap="nowrap"><?php echo JText::_('hh_mm');?></th>
                <th
                        nowrap="nowrap"><?php echo $supportConfig->currency . ' ' . JText::_('value'); ?> </th>
            </tr>

            <!-- LINE --><?php
			if (count($rows_clients) > 0) {
				$k = 0;
				for ($x = 0; $x < count($rows_clients); $x++) {
					$row_staff = $rows_clients[$x];
					print '<tr class="row' . $k . '">';
					print '<td nowrap="nowrap"><b>' . $row_staff->clientname . '</b></td>';

					$total = 0;
					for ($i = 0; $i < count($rows_acttype); $i++) {
						$row_acttype = $rows_acttype[$i];

						$from_datetime = $year . '-' . ($month < 10 ? '0' . $month : $month) . '-01 00:00:00';
						$days = HelpdeskDate::GetMonthDays($year, $month);
						$to_datetime = $year . '-' . ($month < 10 ? '0' . $month : $month) . '-' . $days . ' 23:59:59';

						print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals(0, $row_acttype->value, $from_datetime, $to_datetime, 'numrecs', $user->id, ($row_staff->clientname == '' ? '-' : $row_staff->id)) . '</td>';
						print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals(0, $row_acttype->value, $from_datetime, $to_datetime, 'duration', $user->id, ($row_staff->clientname == '' ? '-' : $row_staff->id)) . '</td>';
						print '<td class="line" width="75"><div align="right">' . HelpdeskReport::GetActivitiesTotals(0, $row_acttype->value, $from_datetime, $to_datetime, 'dollarval', $user->id, ($row_staff->clientname == '' ? '-' : $row_staff->id)) . '</div></td>';
					}

					if (count($rows_acttype)) {
						print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals(0, 0, $from_datetime, $to_datetime, 'numrecs', $user->id, ($row_staff->clientname == '' ? '-' : $row_staff->id)) . '</td>';
						print '<td class="line" width="75">' . HelpdeskReport::GetActivitiesTotals(0, 0, $from_datetime, $to_datetime, 'duration', $user->id, ($row_staff->clientname == '' ? '-' : $row_staff->id)) . '</td>';
						print '<td class="line" width="75"><div align="right">' . HelpdeskReport::GetActivitiesTotals(0, 0, $from_datetime, $to_datetime, 'dollarval', $user->id, ($row_staff->clientname == '' ? '-' : $row_staff->id)) . '</div></td>';
						print '</tr>';
					}

					$k = 1 - $k;
				}
			} else {
				print '<tr class="row0"><td nowrap colspan="4">' . JText::_('tpl_noactivities') . '</td></tr>';
			}

			?>


            <!-- LINE -->
        </table><?php
		}
		/* customize: hide detail timesheet report tab. end comment . */
	}

	function ClientTickets($year, $month, $id_workgroup, $id_client, $f_status, $f_customfields, $detail = 0)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();
		$print = JRequest::getVar('print', 0, '', 'int');

		$GLOBALS['id_workgroup'] = $id_workgroup;
		$GLOBALS['month'] = $month;
		$GLOBALS['year'] = $year;
		$GLOBALS['id_client'] = $id_client;
		$GLOBALS['f_status'] = $f_status;
		$GLOBALS['f_customfields'] = $f_customfields;

		// 2009.02.25 - Detail Client Month Report
		$imgpath = JURI::root() . 'components/com_maqmahelpdesk/images';

		$sql = "SELECT t.id, t.ticketmask, t.subject, t.date, t.last_update, u.name, (
					(SUM(
						TIME_TO_SEC( REPLACE(r.timeused,'.', ':') )
					))
					+
					(SUM(
						TIME_TO_SEC( REPLACE(r.tickettravel,'.', ':') )
					))
				) AS time_spent, count(r.id) as num_msg, s.description as status, c.name AS category, t.duedate, p.description AS priority, u2.name AS assigned_user
				FROM #__support_ticket t 
					 INNER JOIN #__support_status s ON s.id=t.id_status 
					 LEFT JOIN #__support_ticket_resp r ON r.id_ticket=t.id 
					 LEFT JOIN #__support_category AS c ON c.id=t.id_category
					 LEFT JOIN #__support_priority AS p ON p.id=t.id_priority
					 LEFT JOIN #__users u ON u.id=t.id_user
					 LEFT JOIN #__users AS u2 ON u2.id=t.assign_to
				WHERE YEAR(t.`date`)=" . $database->quote($year) . " " .
				($month != '00' ? "AND MONTH(t.`date`)=" . $database->quote($month) : '') . " " .
				($id_workgroup > 0 ? "AND t.id_workgroup='" . $id_workgroup . "'" : "") . " " .
				($id_client > 0 ? "AND t.id_client='" . $id_client . "'" : "") . " " .
				($f_status > 0 ? "AND t.id_status='" . $f_status . "'" : "") . "
				GROUP BY t.id, t.ticketmask, t.subject, t.date, t.last_update, u.name, c.name , t.duedate, p.description, u2.name
				ORDER BY t.`date`";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		// Get workgroup name
		if ($id_workgroup > 0) {
			$database->setQuery("SELECT wkdesc FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
			$wk_desc = $database->loadResult();
		} else {
			$wk_desc = JText::_('all_workgroups');
		}

		// Get clients name
		if ($id_client > 0) {
			$database->setQuery("SELECT clientname FROM #__support_client WHERE id='" . $id_client . "'");
			$client_desc = $database->loadResult();
		} else {
			$client_desc = JText::_('all_clients');
		}

		// get status id
		if ($f_status > 0) {
			$sql = "SELECT `description` AS text FROM #__support_status WHERE id='" . $f_status . "'";
			$database->setQuery($sql);
			$status_desc = $database->loadResult();
		} else {
			$status_desc = JText::_('all_status');
		}

		// Get custom fields
		$sql = "SELECT `id`, `caption` FROM #__support_custom_fields WHERE cftype='W' ORDER BY `caption`";
		$database->setQuery($sql);
		$custom_fields = $database->loadObjectList(); ?>

    <style type="text/css">
        .line {
            text-align: right;
            border-right: 1px #cccccc solid;
        }

        .line_text {
            text-align: left;
            border-right: 1px #cccccc solid;
        }

        .total {
            text-align: center;
            font-weight: bold;
            border-right: 1px #cccccc solid;
            color: #000000;
        }

        .total_text {
            text-align: left;
            font-weight: bold;
            border-right: 1px #cccccc solid;
            color: #000000;
        }
    </style>

    <!-- 2009.02.25 - Detail Client Month Report -->
    <script type="text/javascript" defer="defer">
        <!--
        function toggle(id) {
            var detail = 'detail' + id;
            var toogle = 'toogle' + id;
            var e = document.getElementById(detail);
            var i = document.getElementById(toogle);

            if (e.style.display == 'block') {
                e.style.display = 'none';
                i.src = '<?php echo $imgpath ?>/order_desc.png';
            } else {
                e.style.display = 'block';
                i.src = '<?php echo $imgpath ?>/order_asc.png';
            }
        }
        //-->
    </script>


    <table class="table table-bordered table-striped">
    <thead>
    <tr>
        <td class="subcabecalho" height="30" colspan="35"><b><?php echo HelpdeskDate::GetMonthName($month); ?>
            &nbsp;<?php echo $year; ?> / <?php echo $wk_desc; ?> / <?php echo $client_desc; ?>
            / <?php echo $status_desc; ?></b></td>
    </tr>
    <tr>
        <th nowrap><?php echo JText::_('id'); ?></th>
        <th nowrap><?php echo JText::_('subject'); ?></th>
        <th nowrap><?php echo JText::_('user'); ?></th>
        <th nowrap><?php echo JText::_('category'); ?></th>
        <th nowrap><?php echo JText::_('priority'); ?></th>
		<?php
		if ($f_customfields == 1) {
			for ($i = 0; $i < count($custom_fields); $i++) {
				$custom_field = $custom_fields[$i];
				print '<th nowrap>' . $custom_field->caption . '</th>';
			}
		}
		?>
        <th nowrap><?php echo JText::_('created_date'); ?></th>
        <th nowrap><?php echo JText::_('duedate'); ?></th>
        <th nowrap><?php echo JText::_('last_update'); ?></th>
        <th nowrap><?php echo JText::_('status'); ?></th>
        <th nowrap><?php echo JText::_('TKT_CHNG_STAT_NFY_SUP'); ?></th>
        <th nowrap><?php echo JText::_('messages'); ?></th>
        <th nowrap><?php echo JText::_('time'); ?></th>
    </tr>
    </thead>
    <tbody>
    <!-- LINE --><?php

		if (count($rows) == 0) {
			$colspan = 13 + count($custom_fields);
			print '<tr class="row0"><td colspan="' . $colspan . '">' . JText::_('no_results') . '</td></tr>';
		} else {
			$k = 0;
			$total = 0;
			$total_msg = 0;
			for ($x = 0; $x < count($rows); $x++) {
				$row = $rows[$x];
				print '<tr class="row' . $k . '">';
				// 2009.02.25 - Detail Client Month Report
				if ($detail == 1) {
					print '<td nowrap><img src="../media/com_maqmahelpdesk/images/dtree/nolines_plus.gif" id="toogle' . $row->ticketmask . '" onclick="$jMaQma(\'#detail' . $row->ticketmask . '\').toggle();" style="cursor:hand;" />   ' . $row->ticketmask . '</td>';
				} else {
					print '<td nowrap>' . $row->ticketmask . '</td>';
				}

				print '<td nowrap>' . $row->subject . '</td>';
				print '<td nowrap>' . $row->name . '</td>';
				print '<td nowrap>' . $row->category . '</td>';
				print '<td nowrap>' . $row->priority . '</td>';
				if ($f_customfields == 1) {
					for ($i = 0; $i < count($custom_fields); $i++) {
						$custom_field = $custom_fields[$i];
						$sql = "SELECT `newfield` AS `value` FROM #__support_field_value WHERE id_field='" . $custom_field->id . "' AND id_ticket='" . $row->id . "'";
						$database->setQuery($sql);
						$custom_field_value = $database->loadObject();
						if (!isset($custom_field_value)) {
							print '<td> &nbsp; </td>';
						} else {
							$v = (trim($custom_field_value->value) != '') ? $custom_field_value->value : '&nbsp;';
							print '<td> ' . $v . ' </td>';
						}
					}
				}
				print '<td nowrap>' . $row->date . '</td>';
				print '<td nowrap>' . $row->duedate . '</td>';
				print '<td nowrap>' . $row->last_update . '</td>';
				print '<td nowrap>' . $row->status . '</td>';
				print '<td nowrap>' . $row->assigned_user . '</td>';
				print '<td nowrap class="algcnt">' . $row->num_msg . '</td>';

				// Get tasks times
				$sql = "SELECT (
					(SUM(
						TIME_TO_SEC( REPLACE(timeused,'.', ':') )
					))
					+
					(SUM(
						TIME_TO_SEC( REPLACE(traveltime,'.', ':') )
					))
				) AS taskstime FROM `#__support_task` WHERE `id_ticket`=" . $row->id;
				$database->setQuery($sql);
				$taskstimes = $database->loadResult();
				$value = $row->time_spent + $taskstimes;

				$total = $total + $value;
				$value = HelpdeskDate::SecondsToHours($value,true,false,false);
				print '<td nowrap>' . ($value!='00:00' ? $value : '&nbsp;') . '</td>';


				$total_msg = $total_msg + $row->num_msg;
				print '</tr>';

				// 2009.02.25 - Detail Client Month Report
				if ($detail == 1) {
					// $colspan = 8 + count($custom_fields);
					$colspan = ($f_customfields == 1) ? 8 + count($custom_fields) : 8;
					print '<tr><td colspan=' . $colspan . ' style="min-height:1px;height:auto !important;height:1px;" >';
					$p = (!$print) ? 'display:none;' : 'display:block;';
					print ('<div id="detail' . $row->ticketmask . '" style="' . $p . '">');

					print '<table>';
					print '<tr style="margin:0;padding:0;min-height:1px;height:auto !important;height:1px;" ><td>';

					// need for last calculation
					$nowtime = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));

					// changed status
					$database->setQuery("SELECT l.id, UNIX_TIMESTAMP(l.date_time) AS DT, l.log, u.name, l.id_status AS status, l.id_user AS id_user FROM #__support_log l, #__users u WHERE l.id_user=u.id AND l.id_ticket='" . $row->id . "' ORDER BY DT ASC");
					$ticketLogs = $database->loadObjectList();
					$status_report = $database->getErrorMsg();
					$numb_status = sizeof($ticketLogs);


					unset($status_detail);
					unset($user_detail);

					if ($numb_status > 0) {
						$temp_status = 0;
						foreach ($ticketLogs as $ticketlog) {
							//if ($ticketlog->status != $temp_status) {
							if (($ticketlog->status != $temp_status) && ($ticketlog->status > 0)) { // correct BUG older system doesnt have status id logged, some logs doesnt keep status!!! (i.e.: rate system)
								$temp_status = ($ticketlog->status > 0) ? $ticketlog->status : JText::_('no_last_status_logged');
								$status_detail[HelpdeskStatus::GetName($temp_status)] = $ticketlog->DT;
								$user_detail[] = $ticketlog->id_user;
							}
						}
					}

					$count_status = (isset($status_detail)) ? count($status_detail) : 0;
					?>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <td class="subcabecalho" height="30" colspan="35"
                            colspan="<?php echo ($count_status > 0) ? ($count_status + 1) : "2"; ?>">
                            <b><?php echo JText::_('status_history') ?></b></td>
                    </tr>
                    <tr>
                        <th>
							<?php echo JText::_('tpl_status'); ?>
                        </th>
						<?php
						if ($count_status > 0) {
							foreach ($status_detail as $status_name => $status_date) {
								echo "<th>" . $status_name . "</th>";
							}
						} else {
							echo "<th colspan='1'>" . JText::_('no_last_status_logged') . "</th>";
						}
						?>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>
							<?php echo JText::_('dl_date'); ?>
                        </th>
						<?php
						if ($count_status > 0) {
							foreach ($status_detail as $status_name => $status_date) {
								echo "<th>" . date($supportConfig->date_long, $status_date) . "</th>";
							}
						} else {
							echo "<th colspan='1'>" . JText::_('no_last_status_logged') . "</th>";
						}
						?>
                    </tr>

                    <tr>
                        <th><?php echo JText::_('duedate_explain_length_time'); ?></th>
						<?php
						// to calc time diference we need to know NEXT time first
						if ($count_status > 0) {
							unset($time_temp);
							foreach ($status_detail as $status_name => $status_date) {
								$time_temp[] = $status_date;
							}
							$time_temp[] = $nowtime;

							$cicle = count($time_temp) - 1;
							if ($cicle > 0) {
								for ($t = 0; $t < $cicle; $t++) {
									$time_dif = $time_temp[$t + 1] - $time_temp[$t];
									echo "<th>" . HelpdeskDate::FormatDuration($time_dif) . " </th>";
								}
							} else {
								echo "<th> - </th>";
							}
						} else {
							echo "<th colspan='1'>" . JText::_('no_last_status_logged') . "</th>";
						}

						?>
                    </tr>

                    <tr>
                        <th><?php echo JText::_('user'); ?></th>
						<?php
						if ($count_status > 0) {
							$cicle = count($user_detail) - 1;
							if ($cicle > 0) {
								for ($t = 0; $t < $cicle; $t++) {
									echo "<th>" . HelpdeskUser::GetName($user_detail[$t]) . "</th>";
								}
							} else {
								echo "<th> - </th>";
							}
						} else {
							echo "<th colspan='1'>" . JText::_('no_last_status_logged') . "</th>";
						}
						?>
                    </tr>

                    </tbody>
                </table>
					<?php


					print '</td></tr>';
					print '</table>';

					print ('</div>');
					print '</td></tr>';
				}


				$k = 1 - $k;
			}
		}?>
    </tbody>
    <!-- LINE -->
		<?php
		if (count($rows) > 0) {
			?>
        <!-- TOTAL -->
        <tfoot>
        <tr>
            <th><b><?php echo JText::_('tmpl_msg17'); ?></b></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
			<?php if ($f_customfields == 1) { ?>
            <th colspan="<?php echo count($custom_fields); ?>">&nbsp;</th>
			<?php } ?>
            <th>&nbsp;</th>
            <th><b><?php echo $total_msg; ?></b></th>
            <th><b><?php echo HelpdeskDate::SecondsToHours($total,true,false,false); ?></b></th>
        </tr>
        </tfoot>
        <!-- TOTAL -->
			<?php
		}
		?>

    </table><?php
	}

	function TicketMonth($year, $month, $id_workgroup, $id_client, $custom_fields)
	{
		$database = JFactory::getDBO();

		$sql = "SELECT t.id, t.ticketmask, t.subject, t.date, t.last_update, u.name, sum(r.timeused) AS time_spent, count(r.id) as num_msg, s.description as status
				FROM #__support_ticket t
					 INNER JOIN #__support_status s ON s.id=t.id_status
					 LEFT JOIN #__support_ticket_resp r ON r.id_ticket=t.id
					 INNER JOIN #__users u ON u.id=t.id_user
				WHERE YEAR(t.`date`)=" . $database->quote($year) . ($month!='00' ? " AND MONTH(t.`date`)=" . $database->quote($month) : '') . " " . ($id_workgroup > 0 ? "AND t.id_workgroup='" . $id_workgroup . "'" : "") . " " . ($id_client > 0 ? "AND t.id_client='" . $id_client . "'" : "") . "
				GROUP BY t.id, t.ticketmask, t.subject, t.date, t.last_update, u.name
				ORDER BY t.`date`";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		// Get workgroup name
		if ($id_workgroup > 0) {
			$database->setQuery("SELECT wkdesc FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
			$wk_desc = $database->loadResult();
		} else {
			$wk_desc = JText::_('all_workgroups');
		}

		// Get clients name
		if ($id_client > 0) {
			$database->setQuery("SELECT clientname FROM #__support_client WHERE id='" . $id_client . "'");
			$client_desc = $database->loadResult();
		} else {
			$client_desc = JText::_('all_clients');
		}

		// Get custom fields
		$sql = "SELECT `id`, `caption` FROM #__support_custom_fields WHERE cftype='W' ORDER BY `caption`";
		$database->setQuery($sql);
		$custom_fields = $database->loadObjectList(); ?>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <td height="30" colspan="35"><b><?php echo HelpdeskDate::GetMonthName($month); ?>
                &nbsp;<?php echo $year; ?> / <?php echo $wk_desc; ?> / <?php echo $client_desc; ?></b></td>
        </tr>
        <tr>
            <th nowrap><?php echo JText::_('id'); ?></th>
            <th nowrap><?php echo JText::_('subject'); ?></th>
            <th nowrap><?php echo JText::_('user'); ?></th><?php
			for ($i = 0; $i < count($custom_fields); $i++) {
				$custom_field = $custom_fields[$i];
				print '<th nowrap>' . $custom_field->caption . '</th>';
			} ?>
            <th nowrap><?php echo JText::_('created_date'); ?></th>
            <th nowrap><?php echo JText::_('last_update'); ?></th>
            <th nowrap><?php echo JText::_('status'); ?></th>
            <th nowrap><?php echo JText::_('messages'); ?></th>
            <th nowrap><?php echo JText::_('time'); ?></th>
        </tr>
        </thead>
        <tbody>
        <!-- LINE --><?php

			if (count($rows) == 0) {
				$colspan = 10 + count($custom_fields);
				print '<tr class="row0"><td colspan="' . $colspan . '">' . JText::_('no_results') . '</td></tr>';
			} else {
				$k = 0;
				$total = 0;
				$total_msg = 0;
				for ($x = 0; $x < count($rows); $x++) {
					$row = $rows[$x];
					print '<tr>';
					print '<td nowrap>' . $row->ticketmask . '</td>';
					print '<td nowrap>' . $row->subject . '</td>';
					print '<td nowrap>' . $row->name . '</td>';
					for ($i = 0; $i < count($custom_fields); $i++) {
						$custom_field = $custom_fields[$i];
						$sql = "SELECT `newfield` AS `value` FROM #__support_field_value WHERE id_field='" . $custom_field->id . "' AND id_ticket='" . $row->id . "'";
						$database->setQuery($sql);
						$custom_field_value = $database->loadObject();
						if (!isset($custom_field_value)) {
							print '<td></td>';
						} else {
							print '<td>' . $custom_field_value->value . '</td>';
						}
					}
					print '<td nowrap>' . $row->date . '</td>';
					print '<td nowrap>' . $row->last_update . '</td>';
					print '<td nowrap>' . $row->status . '</td>';
					print '<td nowrap>' . $row->num_msg . '</td>';

					$value = str_replace('.', ':', ($row->time_spent == '' ? '0:00' : $row->time_spent));
					$total = $total + HelpdeskDate::ConvertHoursMinutesToDecimal(HelpdeskReport::CheckTime($value));
					print '<td nowrap>' . HelpdeskReport::CheckTime($value) . '</td>';


					$total_msg = $total_msg + $row->num_msg;
					print '</tr>';

					$k = 1 - $k;
				}
			}?>
        </tbody>
        <!-- LINE -->
		<?php
		if (count($rows) > 0) {
			?>
            <!-- TOTAL -->
            <tfoot>
            <tr>
                <th><b><?php echo JText::_('tmpl_msg17'); ?></b></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th colspan="<?php echo count($custom_fields); ?>">&nbsp;</th>
                <th>&nbsp;</th>
                <th><b><?php echo $total_msg; ?></b></th>
                <th><b><?php echo HelpdeskDate::ConvertDecimalsToHoursMinutes($total); ?></b></th>
            </tr>
            </tfoot>
            <!-- TOTAL -->
			<?php
		}
		?>

    </table><?php
	}

	function DueDates($id_workgroup, $id_client)
	{
		$database = JFactory::getDBO();
		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));


		$sqlcount = "SELECT count(*)
				FROM `#__support_ticket` AS t 
					 INNER JOIN `#__support_status` AS s ON s.id=t.id_status
					 INNER JOIN `#__users` AS u ON u.id=t.id_user
				WHERE s.status_group = 'O' " . ($id_workgroup > 0 ? "AND t.id_workgroup=" . $id_workgroup : "") . "
				  AND t.duedate <= NOW()
				ORDER BY ( HOUR( SEC_TO_TIME( ( unix_timestamp( t.`duedate` ) - unix_timestamp( t.`last_update` ) ) ) ) /24 ) DESC";
		$database->setQuery($sqlcount);
		$total = $database->loadResult();
		echo $database->getErrorMsg();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		$sql = "SELECT t.id,
					   t.`duedate` , 
					   t.`last_update` , 
					   ( unix_timestamp( NOW() ) - unix_timestamp( t.`duedate` ) ) AS 'time_difference_secs',
					   s.description AS 'status_desc',
					   t.ticketmask,
					   t.subject,
					   u.name AS username,
					   t.date,
					   NOW() AS 'today'
				FROM `#__support_ticket` AS t 
					 INNER JOIN `#__support_status` AS s ON s.id=t.id_status
					 INNER JOIN `#__users` AS u ON u.id=t.id_user
				WHERE s.status_group = 'O' " . ($id_workgroup > 0 ? "AND t.id_workgroup=" . $id_workgroup : "") . "
				  AND t.duedate <= NOW()
				ORDER BY ( HOUR( SEC_TO_TIME( ( unix_timestamp( t.`duedate` ) - unix_timestamp( t.`last_update` ) ) ) ) /24 ) DESC";
		$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
		$rows = $database->loadObjectList();

		// Get workgroup name
		if ($id_workgroup > 0) {
			$database->setQuery("SELECT wkdesc FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
			$wk_desc = $database->loadResult();
		} else {
			$wk_desc = JText::_('all_workgroups');
		}

		// Get clients name
		if ($id_client > 0) {
			$database->setQuery("SELECT clientname FROM #__support_client WHERE id='" . $id_client . "'");
			$client_desc = $database->loadResult();
		} else {
			$client_desc = JText::_('all_clientss');
		} ?>

    <h3><?php echo $wk_desc; ?> / <?php echo $client_desc; ?></h3>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th><?php echo JText::_('id'); ?></th>
            <th><?php echo JText::_('subject'); ?></th>
            <th><?php echo JText::_('user'); ?></th>
            <th><?php echo JText::_('created_date'); ?></th>
            <th><?php echo JText::_('duedate'); ?></th>
            <th><?php echo JText::_('today'); ?></th>
            <th><?php echo JText::_('status'); ?></th>
            <th><?php echo JText::_('time_passed'); ?></th>
        </tr>
        </thead>
        <tbody>
        <!-- LINE --><?php
			$k = 0;
			$total = 0;
			$total_msg = 0;
			for ($x = 0; $x < count($rows); $x++)
			{
				$row = $rows[$x];
				print '<tr>';
				print '<td nowrap>' . $row->ticketmask . '</td>';
				print '<td nowrap>' . $row->subject . '</td>';
				print '<td nowrap>' . $row->username . '</td>';
				print '<td nowrap>' . JString::substr($row->date, 0, 16) . '</td>';
				print '<td nowrap>' . JString::substr($row->duedate, 0, 16) . '</td>';
				print '<td nowrap>' . JString::substr($row->today, 0, 16) . '</td>';
				print '<td nowrap>' . $row->status_desc . '</td>';
				print '<td style="text-weight:bold;color:#ff0000;">' . gmdate("d", $row->time_difference_secs) . ' <small><em>' . JText::_("days") . '</em></small> ' . gmdate("H", $row->time_difference_secs) . ' <small><em>' . JText::_("hours") . '</em></small> ' . gmdate("i", $row->time_difference_secs) . ' <small><em>' . JText::_("minutes") . '</em></small></td>';
				print '</tr>';

				$k = 1 - $k;
			} ?>
        <!-- LINE -->
        </tbody>

        <tr>
            <td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
        </tr>

    </table><?php
	}

}
