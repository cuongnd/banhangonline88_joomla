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

class MaQmaHtmlComments
{
	static function display(&$rows, &$pageNav)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=kb"><?php echo JText::_('kb'); ?></a>
	            <span><?php echo JText::_('moderate_comments'); ?></span>
	        </div>
	        <div class="contentarea">
	            <div id="contentbox">
					<?php if (count($rows) == 0) : ?>
	                <div class="detailmsg">
	                    <h1><?php echo JText::_('register_not_found'); ?></h1>
	                </div>
					<?php else: ?>
	                <table class="table table-striped table-bordered" cellspacing="0">
	                    <thead>
	                    <tr>
	                        <th class="algcnt valgmdl" width="20">#</th>
	                        <th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="checkAll(<?php echo count($rows);?>);"/></th>
	                        <th class="algcnt valgmdl" width="70"><?php echo JText::_('date'); ?></th>
	                        <th class="valgmdl"><?php echo JText::_('title'); ?></th>
	                        <th class="valgmdl"><?php echo JText::_('user'); ?></th>
	                        <th class="valgmdl"><?php echo JText::_('comment'); ?></th>
	                        <th class="algcnt valgmdl"><?php echo JText::_('published'); ?></th>
	                    </tr>
	                    </thead>
	                    <tfoot>
	                    <tr>
	                        <td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
	                    </tr>
	                    </tfoot>
	                    <tbody><?php
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img = $row->publish ? 'ok' : 'remove'; ?>
	                        <tr>
	                            <td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
	                            <td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
	                            <td class="algcnt valgmdl"><?php echo $row->date; ?></td>
	                            <td class="valgmdl">
	                                <a href="index.php?option=com_maqmahelpdesk&task=kb_edit&cid[0]=<?php echo $row->id_kb; ?>">
										<?php echo $row->kbtitle; ?>
	                                </a>
	                            </td>
	                            <td class="valgmdl">
	                                <a href="index.php?option=com_maqmahelpdesk&task=kb_edit&cid[0]=<?php echo $row->id_kb; ?>">
										<?php echo $row->name; ?>
	                                </a>
	                            </td>
	                            <td class="valgmdl"><?php echo $row->comment; ?></td>
	                            <td class="algcnt valgmdl"><span class="btn btn-<?php echo ($row->publish ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span></td>
	                        </tr><?php
							} // for ?>
	                    </tbody>
	                </table>
					<?php endif; ?>
	            </div>
	            <div id="infobox">
	                <span id="infoarrow"></span>
	                <dl class="first">
	                    <dd class="title"><?php echo JText::_('INFO_KBMODERATE_TITLE');?></dd>
	                    <dd class="last">
							<?php echo JText::_('INFO_KBMODERATE_DESC');?>
	                        <p align="center"><a href="http://www.imaqma.com" target="_blank" class="btn"><img
	                                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/help.png"
	                                align="absmiddle" border="0" alt=""/> <?php echo JText::_('more_information');?></a></p>
	                    </dd>
	                </dl>
	            </div>
	            <div class="clr"></div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value="kb_moderate"/>
	        <input type="hidden" name="boxchecked" value="0"/>
	    </form><?php
	}
}
