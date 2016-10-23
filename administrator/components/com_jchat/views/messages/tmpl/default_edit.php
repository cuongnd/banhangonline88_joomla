<?php 
/** 
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage messages
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
JHTML::_('behavior.tooltip'); ?>
 
<form action="index.php" method="post" name="adminForm" id="adminForm"> 
	<div class="accordion-group">
    	<div class="accordion-heading opened">
	    	<div class="accordion-toggle noaccordion">
	      		<h4><?php echo JText::_('COM_JCHAT_MESSAGE_DETAILS' ); ?></h4>
	    	</div>
    	</div>
    	<div id="collapseOne" class="accordion-body collapse in">
	      	<div class="accordion-inner">
	      	
				<table class="admintable">
				<tbody>
					<tr>
						<td class="left_title"> 
							<?php echo JText::_('COM_JCHAT_ID' ); ?>: 
						</td>
						<td class="right_details">
							<?php echo $this->record->id;?> 
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_('COM_JCHAT_SENDER_NAME' ); ?>:
						</td>
						<td class="right_details">
							<?php echo $this->record->actualfrom;?>  
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_('COM_JCHAT_RECEIVER_NAME' ); ?>:
						</td>
						<td class="right_details">
							<?php echo $this->record->actualto ? $this->record->actualto : JText::_('COM_JCHAT_MULTIPLE_RECEIVER_USERS'); ?>
						</td>
					</tr> 
					<tr>
						<td class="left_title">
							<?php echo JText::_('COM_JCHAT_MESSAGE_CONTENTS' ); ?>:
						</td>
						<td class="right_details">
							<?php echo $this->record->message;?>  
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_('COM_JCHAT_SENT' ); ?>:
						</td>
						<td class="right_details">
							<?php echo date('Y-m-d H:i:s', $this->record->sent);?>  
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_('COM_JCHAT_READ' ); ?>:
						</td>
						<td class="right_details">
							<?php 
								if($this->record->actualto):
								$imgRead 	= $this->record->read ? 'icon-16-tick.png' : 'icon-16-publish_x.png';
								$altRead 	= $this->record->read ? JText::_('COM_JCHAT_READ' ) : JText::_('COM_JCHAT_NOTREAD' ); 
							?>  
							<img src="components/com_jchat/images/<?php echo $imgRead;?>" width="16" height="16" border="0" title="<?php echo $altRead; ?>" alt="<?php echo $altRead; ?>" /> 
							<?php 
								else:
								echo JText::_('COM_JCHAT_ND');
								endif;
							?>
						</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_('COM_JCHAT_TYPE' ); ?>:
						</td>
						<td class="right_details">
							<?php 
								$imgFileDownloaded 	= $this->record->status ? 'icon-16-download-tick.png' : 'icon-16-download-notick.png';
								$altFileDownloaded 	= $this->record->status ? JText::_('COM_JCHAT_DOWNLOADED' ) : JText::_('COM_JCHAT_NOTDOWNLOADED' ); 
								echo $this->record->type == 'message' ? JText::_('COM_JCHAT_TIPO_TEXT') : JText::_('COM_JCHAT_TIPO_FILE_DETAILS') . "<img class='inner_spia' src='components/com_jchat/images/$imgFileDownloaded' title='$altFileDownloaded' width='16' height='16' border='0' alt='$altFileDownloaded'/>";
							?>
					 	</td>
					</tr>
					<tr>
						<td class="left_title">
							<?php echo JText::_('COM_JCHAT_IPADDRESS' ); ?>:
						</td>
						<td class="right_details">
							<?php echo $this->record->ipaddress;?>  
						</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>
	</div>
		
			
	
	<div class="clr"></div>
 
	<input type="hidden" name="option" value="<?php echo $this->option;?>" /> 
	<input type="hidden" name="task" value="" /> 
</form>