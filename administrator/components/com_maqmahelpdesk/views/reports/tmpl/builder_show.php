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
	static function show($report, $sql, $lists, $f_year, $f_month, $f_status, $f_priority, $f_category, $f_workgroup, $f_client, $f_user, $f_staff, $sub_os, $label1, $label2)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();

		include_once(JPATH_SITE . '/components/com_maqmahelpdesk/includes/baaGrid.php'); ?>

	    <script language="javascript" type="text/javascript">
	        var originalOrderOS = '<?php echo $f_user; ?>';
	        var originalPos = '<?php echo $f_client; ?>';

	        var ordersOS = new Array();
				<?php
				$i = 0;
				foreach ($sub_os as $k => $items) {
					foreach ($items as $v) {
						echo "\n	ordersOS[" . $i++ . "] = new Array( '" . $v->value . "', '" . $k . "', '" . $v->text . "' );";
					}
				} ?>
	    </script>

	    <form id="adminForm" name="adminForm" action="index.php" method="POST">
			<?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" name="task" value="reports_builderreport"/>
	            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	            <input type="hidden" name="id" value="<?php echo $report->id; ?>"/>

			<?php
			$GLOBALS['title_showReport'] = $report->title;
			?>

			<?php
			$print = JRequest::getVar('print', 0, '', 'int');
			if ($print == 1) {
				?>
	                <h2 class="contentheading"> <?php echo $report->title; ?>	</h2>
				<?php
			} else {
				?>
	                <table class="adminheading">
	                <tr>
	                <td align="right">
	                <table>
							<?php if ($report->sf_year) { ?>
	                    <td><?php echo $lists['year']; ?></td><?php } ?>
							<?php if ($report->sf_month) { ?>
	                    <td><?php echo $lists['month']; ?></td><?php } ?>
							<?php if ($report->sf_workgroup) { ?>
	                    <td><?php echo $lists['workgroup'];?></td><?php } ?>
							<?php if ($report->sf_category) { ?>
	                    <td><?php echo $lists['category']; ?></td><?php } ?>
							<?php if ($report->sf_priority) { ?>
	                    <td><?php echo $lists['priority']; ?></td><?php } ?>
							<?php if ($report->sf_status) { ?>
	                    <td><?php echo $lists['status'];?></td><?php } ?>
							<?php if ($report->sf_assign) { ?>
	                    <td><?php echo $lists['assign']; ?></td><?php } ?>
							<?php if ($report->sf_client) { ?>
	                    <td><?php echo $lists['client']; ?></td><?php } ?>
							<?php if ($report->sf_user) { ?>
	                    <td>
	                    <script language="javascript" type="text/javascript">
	                <!--
	                    writeDynaList('class="inputbox" name="f_user" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
	                                //-->
	                            </script>
	                        </td>
							<?php } ?>
	                        <td><input type="submit" name="submit" class="btn btn-success" value="Filter"></td>
	                    </table>
	            </tr>
	        </table>
	        <br/>
				<?php
			}
			?>

			<?php	if ($report->type == 1) {
			ob_start();
			$grid = new baaGrid ($sql, DB_MYSQL);
			$grid->setTableAttr('class="table table-striped table-bordered" ');
			$grid->setTotal(1, 0);
			$grid->setDateFormat(_DATE_FORMAT);
			$grid->showErrors(1);
			$grid->display();
			$grid_html = ob_get_contents();
			ob_end_clean();
		} elseif ($report->type == 2) {
			$database->setQuery($sql);
			$rows = $database->loadAssocList();

			ob_start();
			$total = 0; ?>
	    <table width="100%" class="adminlist">
	        <thead>
	        <tr>
	            <th>&nbsp;</th>
	            <th><?php echo JText::_('times'); ?></th>
	        </tr>
	        </thead>
	        <tbody><?php
				for ($i = 0; $i < count($rows); $i++) {
					$row = $rows[$i];
					if ($row[0] != '') {
						$total = $total + $row[1]; ?>
	                <tr>
	                    <td><?php echo $row[0]; ?></td>
	                    <td style="text-align: right"><?php echo HelpdeskDate::ConvertDecimalsToHoursMinutes($row[1]); ?></td>
	                </tr><?php
					}
				} ?>
	        </tbody>
	        <tfoot>
	        <tr>
	            <th>&nbsp;</th>
	            <th style="text-align: right"><?php echo HelpdeskDate::ConvertDecimalsToHoursMinutes($total); ?></th>
	        </tr>
	        </tfoot>
	    </table><?php
			$grid_html = ob_get_contents();
			ob_end_clean();
		}

			$chart_html = '';

			$database->setQuery($sql);
			$rows = $database->loadAssocList();

			$database->setQuery("SELECT FOUND_ROWS()");
			$columns = $database->loadResult();

			echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_maqmahelpdesk/includes/amcharts/ampie/swfobject.js"></script>';

			switch ($report->chart_type) {
				case 'pie':
					ob_start(); ?>
	            <script type="text/javascript" defer="defer">
	                // <![CDATA[
	                var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie.swf", "ampie", "<?php echo $report->chart_width; ?>", "<?php echo $report->chart_height; ?>", "8", "#FFFFFF");
	                so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/");
	                so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie_settings.xml"));				// you can set two or more different settings files here (separated by commas)
	                so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
	                so.addVariable("loading_data", "LOADING DATA");												 // you can set custom "loading data" text here
	                so.addVariable("preloader_color", "#999999");
	                so.addParam("wmode", "transparent");
	                so.write("%CHARTNAME%");
	                // ]]>
	            </script><?php
					$chart_html = ob_get_contents();
					ob_end_clean();
					break;
				case 'column':
					ob_start(); ?>
	            <script type="text/javascript" defer="defer">
	                // <![CDATA[
	                var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amcolumn/amcolumn.swf", "amcolumn", "<?php echo $report->chart_width; ?>", "<?php echo $report->chart_height; ?>", "8", "#FFFFFF");
	                so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amcolumn/");
	                so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amcolumn/amcolumn_settings.xml"));				// you can set two or more different settings files here (separated by commas)
	                //so.addVariable("additional_chart_settings", encodeURIComponent("<settings><graphs>%LABELS%</graphs></settings>"));	  // you can append some chart settings to the loaded ones
	                so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
	                so.addVariable("loading_data", "LOADING DATA");												 // you can set custom "loading data" text here
	                so.addVariable("preloader_color", "#999999");
	                so.addParam("wmode", "transparent");
	                so.write("%CHARTNAME%");
	                // ]]>
	            </script><?php
					$chart_html = ob_get_contents();
					ob_end_clean();
					break;
				case 'bar':
					ob_start(); ?>
	            <script type="text/javascript" defer="defer">
	                // <![CDATA[
	                var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline.swf", "amline", "<?php echo $report->chart_width; ?>", "<?php echo $report->chart_height; ?>", "8", "#FFFFFF");
	                so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/");
	                so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline_settings.xml"));				// you can set two or more different settings files here (separated by commas)
	                so.addVariable("additional_chart_settings", encodeURIComponent("<settings><graphs>%LABELS%</graphs></settings>"));	  // you can append some chart settings to the loaded ones
	                so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
	                so.addVariable("loading_data", "LOADING DATA");												 // you can set custom "loading data" text here
	                so.addVariable("preloader_color", "#999999");
	                so.addParam("wmode", "transparent");
	                so.write("%CHARTNAME%");
	                // ]]>
	            </script><?php
					$chart_html = ob_get_contents();
					ob_end_clean();
					break;
			}

			$div_id = trim(str_replace(' ', '_', $report->title));
			$series = '';
			$label = "<graph id='0'><title>" . ($report->type == 2 ? JText::_('times') : JText::_('tickets')) . "</title><color>#0D8ECF</color><bullet>round</bullet></graph>";

			for ($i = 0, $n = sizeof($rows); $i < $n; $i++) {
				$row = &$rows[$i];

				if ($row[$label1] != '') {
					$series .= $row[$label1] . ';' . $row[JText::_('tickets')] . '\n';
				}
			}

			$chart_html = '<div id="' . $div_id . '"><strong>You need to upgrade your Flash Player</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", $div_id, str_replace("%LABELS%", $label, $chart_html))); ?>

	    <table class="admintable" width="100%">
		    <?php	if ($report->layout == 1) {
		    echo '<tr><td valign="top">' . $grid_html . '</td></tr>';
		    echo '<tr><td align="center">' . $chart_html . '</td></tr>';

	    } elseif ($report->layout == 2) {
		    echo '<tr><td>' . $chart_html . '</td></tr>';
		    echo '<tr><td valign="top"  align="center">' . $grid_html . '</td></tr>';

	    } elseif ($report->layout == 3) {
		    echo '<tr><td width="50%" valign="top">' . $grid_html . '</td>';
		    echo '<td width="50%"  align="center">' . $chart_html . '</td></tr>';

	    } elseif ($report->layout == 4) {
		    echo '<tr><td width="50%">' . $chart_html . '</td>';
		    echo '<td width="50%" valign="top">' . $grid_html . '</td></tr>';

	    } ?>
	    </table>
	    </form>
		<?php
	}
}
