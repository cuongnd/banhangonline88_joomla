<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="dashboard-head clearfix">
	<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
</div>
<div id="write_container">
	<form name="adminForm" id="adminForm">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="team-request reset-table">
			<thead>
				<tr class="page_row_white">
					<th style="text-align:center;" width="1%">#</th>
					<th style="width: 150px;"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_NAME'); ?></th>
					<th style="width: 150px;text-align:center;"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_REQUESTOR'); ?></th>
					<th style="text-align:center;"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_REQUEST_DATE'); ?></th>
					<th style="width: 150px; text-align: center;"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_APPROVAL'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if( count($requests) > 0 )
				{
					for($i = 0; $i< count($requests); $i++)
					{
					    $entry  =& $requests[$i];

						$requestor	= JFactory::getUser( $entry->user_id );

						$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
						$user->setUser($requestor);

						$created	= EasyBlogDateHelper::dateWithOffSet($entry->created);

				?>
				<tr class="entry item-wrapper page_row<?php echo ($i % 2 == 0) ? '_white' : '' ?>" id="td-<?php echo $entry->id; ?>">
					<td style="text-align:center;"><?php echo $i + 1; ?></td>
					<td>
						<?php echo $entry->title; ?>
					</td>
					<td style="text-align:center;">
						<a href="<?php echo $user->getProfileLink();?>" target="_blank"><?php echo $user->getName(); ?></a>
					</td>
					<td style="text-align:center;">
					    <?php echo EasyBlogDateHelper::toFormat($created, $system->config->get('layout_dateformat', '%A, %d %B %Y') ); ?>
					</td>
					<td class="team-respond tdac" style="text-align:center;">
						<a class="team-approve buttons sibling-l" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=teamApproval&id='.$entry->id.'&team='.$entry->team_id.'&approve=1'); ?>" class="text-green"><i><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_APPROVE_REQUEST'); ?></i></a><a class="team-reject buttons sibling-r" href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=teamApproval&id='.$entry->id.'&team='.$entry->team_id.'&approve=0'); ?>" class="text-red"><i><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_REJECT_REQUEST'); ?></i></a>
					</td>
				</tr>
				<?php
					}//end for
				}
				else
				{
				?>
				<tr>
					<td colspan="5" height="30">
						<div style="text-align: center; height: 30px; line-height: 30px;"><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_NO_REQUEST'); ?>.</div>
					</td>
				</tr>
				<?php
				}
				?>
			</tbody>
			<?php if ( $requests && !empty($pagination) ) : ?>
			<tfoot>
				<td colspan="5" align="center">
					<div class="eblog-pagination"><?php echo $pagination->getPagesLinks(); ?></div>
				</td>
			</tfoot>
			<?php endif; ?>
		</table>
	</form>
</div>
