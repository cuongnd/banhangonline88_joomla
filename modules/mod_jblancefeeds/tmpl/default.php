<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	15 August, 2012
 * @file name	:	modules/mod_jblancefeeds/tmpl/default.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
// no direct access
 defined('_JEXEC') or die('Restricted access');
 
 $document = JFactory::getDocument();
 $direction = $document->getDirection();
 $config = JblanceHelper::getConfig();
 
 if($config->loadBootstrap){
 	JHtml::_('bootstrap.loadCss', true, $direction);
 }

 $document->addStyleSheet("components/com_jblance/css/style.css");
 $document->addStyleSheet("modules/mod_jblancefeeds/css/style.css"); 
 
 $dformat = $config->dateFormat;
?>
<?php
if($show_type == 'feed'){
	if(count($rows)){
		for ($i=0, $n=count($rows); $i < $n; $i++){
			$row = $rows[$i];
			if(isset($row->user_id))
				$link_detail = JRoute::_('index.php?option=com_jbjobs&view=employer&layout=detailjobseeker&id='.$row->user_id);	?>

	<div class="media jb-borderbtm-dot">
		<?php echo $row->logo; ?>
		<div class="media-body">
			<?php echo $row->title; ?>
			<div>
	        	<i class="icon-calendar"></i> <?php echo $row->daysago; ?>
	        </div>
		</div>
	</div>
		<?php
		}
	}
	else
		echo JText::_('MOD_JBLANCE_NO_ACTIVITIES');
}
elseif($show_type == 'message'){
	if(count($rows)){
		for ($i=0, $n=count($rows); $i < $n; $i++){
			$row = $rows[$i];
			$userDtl = JFactory::getUser($row->idFrom);
		?>
	<div class="media jb-borderbtm-dot">
		<?php
		$attrib = 'class="pull-left img-polaroid" style="width: 36px; height: 36px;"';
		$avatar = JblanceHelper::getLogo($row->idFrom, $attrib);
		echo $avatar; ?>
		<div class="media-body">
			<?php echo LinkHelper::GetProfileLink($row->idFrom, $userDtl->username); ?><br>
			<?php 
			if($row->approved == 1)
				echo $row->message; 
			else
				echo '<small>'.JText::_('COM_JBLANCE_PRIVATE_MESSAGE_WAITING_FOR_MODERATION').'</small>';
			?>
			<div>
	        	<i class="icon-calendar"></i> <?php echo JHtml::_('date', $row->date_sent, $dformat, true); ?>
	        </div>
		</div>
	</div>
		<?php
		}
	}
	else 
		echo JText::_('MOD_JBLANCE_NO_MESSAGES');
}
?>