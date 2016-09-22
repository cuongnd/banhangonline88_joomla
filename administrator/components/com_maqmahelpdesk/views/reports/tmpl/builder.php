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
	static function show(&$rows, &$pageNav)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
	        <table class="adminlist">
	            <thead>
	            <tr>
	                <th width="20" align="right">#</th>
	                <th width="20">
	                    <input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
	                           onClick="Joomla.checkAll(this);"/>
	                </th>
	                <th class="title"><?php echo JText::_('title'); ?></th>
	                <th class="title"><?php echo JText::_('description'); ?></th>
	                <th class="title" width="35"><?php echo JText::_('run'); ?></th>
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
						for ($i = 0, $n = count($rows); $i < $n; $i++) {
							$row = &$rows[$i]; ?>
	                    <tr class="<?php echo "row$k"; ?>">
	                        <td width="20" align="right"><span class="lbl"><?php echo $row->id; ?></span></td>
	                        <td width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
	                        <td>
	                            <a href="#reports_builderedit"
	                               onClick="return listItemTask('cb<?php echo $i;?>','reports_builderedit')">
									<?php echo $row->title; ?>
	                            </a>
	                        </td>
	                        <td><?php echo $row->description; ?></td>
	                        <td width="35" align="center"><a
	                                href="index.php?option=com_maqmahelpdesk&task=reports_builderreport&id=<?php echo $row->id; ?>"><img
	                                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/charts.png"
	                                border="0"/></a></td>
							<?php
							$k = 1 - $k;
							?>
	                    </tr>
							<?php
						} // for loop
					} // if ?>
	            </tbody>
	            <tfoot>
	            <tr>
	                <td colspan="5"><?php echo $pageNav->getListFooter(); ?></td>
	            </tr>
	            </tfoot>
	        </table>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value="reports_builder"/>
	        <input type="hidden" name="boxchecked" value="0"/>
	    </form>
		<?php
	}
}
