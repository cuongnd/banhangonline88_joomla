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

class MaQmaHtmlData
{
	static function display(&$rows, &$pageNav, $id, $rowForm, $fields)
	{
		$database = JFactory::getDBO();?>
	    <form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>

	        <table class="adminheading">
	            <tr>
	                <th class="forms"><?php echo $rowForm->name; ?></th>
	            </tr>
	        </table>

	        <table class="adminlist">
	            <thead>
	            <tr>
	                <th width="20" align="right">#</th><?php
					for ($i = 0; $i < count($fields); $i++) {
						print '<th class="title">' . $fields[$i][2] . '</th>';
					} ?>
	                <th class="title"><?php echo JText::_('datetime'); ?></th>
	                <th class="title"><?php echo JText::_('ipaddress'); ?></th>
	            </tr>
	            </thead>
	            <tbody>
					<?php
					if (count($rows) == 0) {
						?>
	                <tr>
	                    <td colspan="3"><?php echo JText::_('register_not_found'); ?></td>
	                </tr><?php
					} else {
						$k = 0;
						for ($i = 0, $n = count($rows); $i < $n; $i++) {
							?>
	                    <tr class="<?php echo "row$k"; ?>">
	                        <td width="20" align="right"><span class="lbl"><?php echo $row->id; ?></span></td><?php
							for ($z = 0; $z < count($fields); $z++) {
								$field_name = JString::strtolower(str_replace(' ', '_', $fields[$z][2]));
								print '<td align="left">' . $rows[$i]["$field_name"] . '</td>';
							}
							$k = 1 - $k; ?>
	                        <td align="left"><?php echo $rows[$i]['sc_recorddate']; ?></td>
	                        <td align="left"><?php echo $rows[$i]['sc_ipaddress']; ?></td>
	                    </tr><?php
						} // for loop
					} // if ?>
	            </tbody>
	            <tfoot>
	            <tr>
	                <td colspan="3">
						<?php echo $pageNav->getListFooter(); ?>
	                </td>
	            </tr>
	            </tfoot>
	        </table>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value="forms_data"/>
	        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
	    </form><?php
	}
}
