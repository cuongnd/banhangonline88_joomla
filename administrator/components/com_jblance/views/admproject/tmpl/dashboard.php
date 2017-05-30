<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	20 March 2012
 * @file name	:	views/admproject/tmpl/dashboard.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	JoomBri Admin Dashboard (jblance)
 */
 defined('_JEXEC') or die('Restricted access');

 $link_project	 	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showproject');
 $link_service	 	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showservice');
 $link_user 		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showuser');
 $link_subscr		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showsubscr');
 $link_deposit		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showdeposit');
 $link_withdraw		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showwithdraw');
 $link_escrow		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showescrow'); 
 $link_reporting	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showreporting');
 $link_messages		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=managemessage');
 $link_config 		= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=configpanel');
 $link_summary		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=showsummary');
 $link_custom_field = JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showcustomfield');
 $link_about		= JRoute::_('index.php?option=com_jblance&view=admproject&layout=about');
 $link_support	 	= 'http://www.joombri.in/forum';
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div class="row-fluid">
		<div class="span7">
			<div class="well well-small" id="cpanel">
				<h2 class="module-title nav-header"><?php echo JText::_('COM_JBLANCE_TITLE_DASHBOARD'); ?></h2>
				<hr class="hr-condensed">
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_project; ?>"><img src="components/com_jblance/images/project.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_PROJECTS'); ?><?php echo ($this->unappProjs > 0) ? '<br><span class="update-badge">'.$this->unappProjs.'</span>' : ''; ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_service; ?>"><img src="components/com_jblance/images/service.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_SERVICES'); ?><?php echo ($this->unappSvcs > 0) ? '<br><span class="update-badge">'.$this->unappSvcs.'</span>' : ''; ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_user; ?>"><img src="components/com_jblance/images/user.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_USERS'); ?><?php echo ($this->unappUsers > 0) ? '<br><span class="update-badge">'.$this->unappUsers.'</span>' : ''; ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_subscr; ?>"><img src="components/com_jblance/images/plan.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_SUBSCRIPTIONS'); ?><?php echo ($this->unappSubs > 0) ? '<br><span class="update-badge">'.$this->unappSubs.'</span>' : ''; ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_deposit; ?>"><img src="components/com_jblance/images/deposit.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_DEPOSITS'); ?><?php echo ($this->unappDepo > 0) ? '<br><span class="update-badge">'.$this->unappDepo.'</span>' : ''; ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_withdraw; ?>"><img src="components/com_jblance/images/withdraw.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_WITHDRAWALS'); ?><?php echo ($this->unappWdraws > 0) ? '<br><span class="update-badge">'.$this->unappWdraws.'</span>' : ''; ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_escrow; ?>"><img src="components/com_jblance/images/escrow.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_ESCROWS'); ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_reporting; ?>"><img src="components/com_jblance/images/reporting.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_REPORTINGS'); ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_messages; ?>"><img src="components/com_jblance/images/messages.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_PRIVATE_MESSAGES'); ?><?php echo ($this->unappMsgs > 0) ? '<br><span class="update-badge">'.$this->unappMsgs.'</span>' : ''; ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_config; ?>"><img src="components/com_jblance/images/config.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_CONFIGURATION'); ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_summary; ?>"><img src="components/com_jblance/images/report.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_SUMMARY'); ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_custom_field; ?>"><img src="components/com_jblance/images/customfield.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_CUSTOM_FIELDS'); ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_about; ?>"><img src="components/com_jblance/images/about.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_TITLE_ABOUT'); ?></span></a></div>
				</div>
				<div class="jbicon-container">
					<div class="jbicon"> <a href="<?php echo $link_support; ?>"><img src="components/com_jblance/images/support.png" align="middle" border="0" alt="" /><span><?php echo JText::_('COM_JBLANCE_SUPPORT'); ?></span></a></div>
				</div>
			</div>
		</div>
		<div class="span5">
			<div class="row-fluid">
				<div class="well well-small">
					<h2 class="module-title nav-header"><?php echo JText::_('COM_JBLANCE_JOOMBRI_STATISTICS'); ?></h2>
					<div class="row-striped">
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_TOTAL_USERS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->users; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_TOTAL_JOOMBRI_USERS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->jbusers; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_TOTAL_PROJECTS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->projects; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_TOTAL_SERVICES').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->services; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNAPPROVED_PROJECTS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->unappProjs; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNAPPROVED_SERVICES').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->unappSvcs; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNAPPROVED_USERS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->unappUsers; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNAPPROVED_SUBSCRIPTIONS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->unappSubs; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNAPPROVED_DEPOSIT_REQUESTS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->unappDepo; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNAPPROVED_WITHDRAW_REQUESTS').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->unappWdraws; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNAPPROVED_MESSAGES').': '; ?>
							</div>
							<div class="span3">
								<span class="label label-info"><?php echo $this->unappMsgs; ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="well well-small">
					<h2 class="module-title nav-header"><?php echo JText::_('COM_JBLANCE_WELCOME_TO_JOOMBRI'); ?></h2>
					<div class="row-striped">
						<div class="row-fluid">
							<div class="span12">
								<a href="http://www.joombri.in" target="_blank">
						          	<img src="http://www.joombri.in/images/documents/joombri-logo.png" align="middle" alt="JoomBri" style="border: none; margin: 8px;">
						        </a>
								<p style="font-weight:700;">
									Freelance component developed by BriTech Solutions
								</p>
								<p>
									Thank you for choosing JoomBri as your Freelance Web solution. This dashboard will help you manage projects, users, subscriptions and configure the component.
								</p>
								<p>
									If you require professional support just head on to the forum at 
									<a href="http://www.joombri.in/forum" target="_blank">
									http://www.joombri.in
									</a>
									For developers, you can browse through the wiki based documentations at 
									<a href="http://docs.joombri.in" target="_blank">http://docs.joombri.in</a>
								</p>
								<p>
									If you have any queries related to JoomBri component, kindly use our forum at <a href="http://www.joombri.in/forum">http://www.joombri.in/forum</a>
								</p>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span5">
								<?php echo JText::_('COM_JBLANCE_YOUR_VERSION').': '; ?>
							</div>
							<div class="span7">
								<span class="label label-info"><?php echo $this->xmlManifest->{'version'}; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span5">
								<?php echo JText::_('COM_JBLANCE_LATEST_VERSION').': '; ?>
							</div>
							<div class="span7">
								<span class="label label-info"><?php echo $this->newVersion; ?></span>&nbsp;<span class="label label-important"><?php echo $this->tag; ?></span>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span5">
								<?php echo JText::_('COM_JBLANCE_COPYRIGHT').': '; ?>
							</div>
							<div class="span7">
								<?php echo $this->xmlManifest->{'copyright'}; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
