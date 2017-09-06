<?php 
/** 
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage views
 * @subpackage cpanel
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
?>
<!-- CPANEL ICONS -->
<div id="cpanel">
	<?php echo $this->icons; ?>
	
	<div id="updatestatus">
		<?php 
		if(is_object($this->updatesData)) {
			if(version_compare($this->updatesData->latest, $this->currentVersion, '>')) { ?>
				<a href="http://storejextensions.org/extensions/jchatsocial.html" target="_blank" alt="storejextensions link">
					<label data-content="<?php echo JText::sprintf('COM_JCHAT_GET_LATEST', $this->currentVersion, $this->updatesData->latest, $this->updatesData->relevance);?>" class="label label-important hasPopover">
						<span class="icon-warning"></span>
						<?php echo JText::sprintf('COM_JCHAT_OUTDATED', $this->updatesData->latest);?>
					</label>
				</a>
			<?php } else { ?>
				<label data-content="<?php echo JText::sprintf('COM_JCHAT_YOUHAVE_LATEST', $this->currentVersion);?>" class="label label-success hasPopover">
					<span class="icon-checkmark"></span>
					<?php echo JText::sprintf('COM_JCHAT_UPTODATE', $this->updatesData->latest);?>
				</label>	
			<?php }
		}
		?>
	</div>
</div>

<div class="accordion" id="jchat_accordion_cpanel">
	<div class="accordion-group">
    	<div class="accordion-heading">
    		<div class="accordion-toggle" data-toggle="collapse" data-parent="#jchat_accordion_cpanel" href="#jchat_stats">
	      		<h4 class="accordion-title">
	      			<span class="icon-chart"></span>
	      			<?php echo JText::_('COM_JCHAT_CPANEL_STATS');?>
      			</h4>
      		</div>
    	</div>
    	
    	 <div id="jchat_stats" class="accordion-body collapse">
			<div class="accordion-inner">
				<div class="single_stat_container">
					<div class="statcircle">
						<span class="icon-users icon-large"></span>
					</div>
					<ul class="subdescription_stats">
						<li class="es-stat-no"><?php echo $this->infodata['chart_users_canvas']['totalusers']; ?></li>
						<li class="es-stat-title"><?php echo JText::_('COM_JCHAT_TOTAL_USERS');?></li>
					</ul>
				</div>
				
				<div class="single_stat_container">
					<div class="statcircle">
						<span class="icon-users icon-large"></span>
					</div>
					<ul class="subdescription_stats">
						<li class="es-stat-no"><?php echo $this->infodata['chart_users_canvas']['loggedusers']; ?></li>
						<li class="es-stat-title"><?php echo JText::_('COM_JCHAT_TOTAL_LOGGED_USERS');?></li>
					</ul>
				</div>
				
				<div class="chart_container">
					<canvas id="chart_users_canvas"></canvas>
				</div>
				
				<div class="single_stat_container">
					<div class="statcircle">
						<span class="icon-cancel icon-large"></span>
					</div>
					<ul class="subdescription_stats">
						<li class="es-stat-no"><?php echo $this->infodata['chart_videochat_canvas']['totalbannedusers']; ?></li>
						<li class="es-stat-title"><?php echo JText::_('COM_JCHAT_TOTAL_BANNED_USERS');?></li>
					</ul>
				</div>
				
				<div class="single_stat_container">
					<div class="statcircle">
						<span class="icon-camera-2 icon-large"></span>
					</div>
					<ul class="subdescription_stats">
						<li class="es-stat-no"><?php echo $this->infodata['chart_videochat_canvas']['totalvideochatsessions']; ?></li>
						<li class="es-stat-title"><?php echo JText::_('COM_JCHAT_TOTAL_VIDEOCHAT_SESSIONS');?></li>
					</ul>
				</div>
				
				<div class="chart_container">
					<canvas id="chart_videochat_canvas"></canvas>
				</div>
				
				<div class="single_stat_container">
					<div class="statcircle">
						<span class="icon-mail icon-large"></span>
					</div>
					<ul class="subdescription_stats">
						<li class="es-stat-no"><?php echo $this->infodata['chart_messages_canvas']['totalmessages']; ?></li>
						<li class="es-stat-title"><?php echo JText::_('COM_JCHAT_TOTAL_MESSAGES');?></li>
					</ul>
				</div>
				
				<div class="single_stat_container">
					<div class="statcircle">
						<span class="icon-flag-2 icon-large"></span>
					</div>
					<ul class="subdescription_stats">
						<li class="es-stat-no"><?php echo $this->infodata['chart_messages_canvas']['totalfilemessages']; ?></li>
						<li class="es-stat-title"><?php echo JText::_('COM_JCHAT_TOTAL_MESSAGES_FILE');?></li>
					</ul>
				</div>
				
				<div class="chart_container">
					<canvas id="chart_messages_canvas"></canvas>
				</div>
				
			</div>
		</div>
	</div>
	
	<div class="accordion-group">
	    <div class="accordion-heading">
			<div class="accordion-toggle" data-toggle="collapse" data-parent="#jchat_accordion_cpanel" href="#jchat_status">
				<h4 class="accordion-title">
					<span class="icon-help"></span>
					<?php echo JText::_('COM_JCHAT_ABOUT');?>
				</h4>
	      	</div>
    	</div>
	    <div id="jchat_status" class="accordion-body collapse">
	 		<div class="accordion-inner">
				<div class="single_container">
			 		<label class="label label-warning"><?php echo JText::_('COM_JCHAT_CURRENT_VERSION') . $this->currentVersion;?></label>
		 		</div>
		 		
		 		<div class="single_container">
			 		<label class="label label-info"><?php echo JText::_('COM_JCHAT_AUTHOR_COMPONENT');?></label>
		 		</div>
		 		
		 		<div class="single_container">
			 		<label class="label label-info"><?php echo JText::_('COM_JCHAT_SUPPORTLINK');?></label>
		 		</div>
		 		
		 		<div class="single_container">
			 		<label class="label label-info"><?php echo JText::_('COM_JCHAT_DEMOLINK');?></label>
		 		</div>
			</div>
	    </div>
 	</div>
</div>

<form name="adminForm" id="adminForm" action="index.php">
	<input type="hidden" name="option" value="<?php echo $this->option;?>"/>
	<input type="hidden" name="task" value=""/>
</form>