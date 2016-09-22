<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: log.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

switch ($task) {
	default:
		showSMSLog();
		break;
}

function showSMSLog()
{
	global $mosConfig_list_limit;

	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit);
	$limitstart = $mainframe->getUserStateFromRequest("viewcom_maqmahelpdesklimitstart", 'limitstart', 0);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_smslog");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	require_once("includes/pageNavigation.php");
	$pageNav = new mosPageNav($total, $limitstart, $limit);

	$query = "SELECT date_message, id_user, id_user_message, phone_number, id_ticket, action FROM #__support_smslog ORDER BY date_message DESC";
	$database->setQuery($query);
	$rows = $database->loadObjectList();

	smslog_html::show($rows, $pageNav);
}

/**
 * @package MaQma Helpdesk
 */
class smslog_html
{
	/**
	 * Writes a list of the categories
	 * @param array An array of category objects
	 * @param string The name of the category section
	 */
	function show(&$rows, &$pageNav)
	{
		?>
	<form action="index.php" method="post" name="adminForm">

		<table class="adminheading">
			<tr>
				<th class="log">
					SMS Log
				</th>
			</tr>
		</table>

		<table class="adminlist">
			<tr>
				<th width="20">#</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows);?>);"/>
				</th>
				<th class="title">Date</th>
				<th class="title">Phone Number</th>
				<th class="title">Ticket ID</th>
				<th class="title">Log</th>
			</tr>
			<?php
			$k = 0;
			for ($i = 0, $n = count($rows); $i < $n; $i++) {
				$row = &$rows[$i];
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="20" align="right"><?php echo $pageNav->rowNumber($i); ?></td>
					<td width="20"><?php echo mosHTML::idBox($i, $row->id, 0); ?></td>
					<td><?php echo $row->date_message; ?></td>
					<td><?php echo $row->phone_number; ?></td>
					<td><?php echo $row->id_ticket; ?></td>
					<td><?php echo $row->action; ?></td>
					<?php
					$k = 1 - $k;
					?>
				</tr>
				<?php
			} // for loop ?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value="addon-sms_log"/>
		<input type="hidden" name="boxchecked" value="0"/>
	</form>
	<?php
	}
}

?>