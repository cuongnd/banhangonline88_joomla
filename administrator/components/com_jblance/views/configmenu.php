<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	24 September 2014
 * @file name	:	views/configmenu.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows config sub-menu (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 $link_dashboard 	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=dashboard');
 $link_confighome	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=configpanel');
 $link_compsetting	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=config');
 $link_usergroup	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showusergroup');
 $link_plan			= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showplan');
 $link_paymode		= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showpaymode');
 $link_customfield	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showcustomfield');
 $link_emailtemp	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=emailtemplate&tempfor=subscr-pending');
 $link_category		= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showcategory');
 $link_budget		= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showbudget');
 $link_duration		= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showduration');
 $link_location		= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showlocation');
 $link_optimise		= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=optimise');
 ?>
<?php echo JHtmlSidebar::render(); ?>
<ul class="nav nav-list">
	<li class="nav-header"><?php echo JText::_('COM_JBLANCE_CONFIG'); ?></li>
	<li class="<?php echo ($layout == 'config') ? 'active' : ''; ?>">
		<a href="<?php echo $link_compsetting; ?>"><?php echo JText::_('COM_JBLANCE_COMPONENT_SETTINGS'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showusergroup') ? 'active' : ''; ?>">
		<a href="<?php echo $link_usergroup; ?>"><?php echo JText::_('COM_JBLANCE_USER_GROUPS'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showplan') ? 'active' : ''; ?>">
		<a href="<?php echo $link_plan; ?>"><?php echo JText::_('COM_JBLANCE_SUBSCRIPTION_PLANS'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showpaymode') ? 'active' : ''; ?>">
		<a href="<?php echo $link_paymode; ?>"><?php echo JText::_('COM_JBLANCE_PAYMENT_GATEWAYS'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showcustomfield') ? 'active' : ''; ?>">
		<a href="<?php echo $link_customfield; ?>"><?php echo JText::_('COM_JBLANCE_CUSTOM_FIELDS'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'emailtemplate') ? 'active' : ''; ?>">
		<a href="<?php echo $link_emailtemp; ?>"><?php echo JText::_('COM_JBLANCE_EMAIL_TEMPLATES'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showcategory') ? 'active' : ''; ?>">
		<a href="<?php echo $link_category; ?>"><?php echo JText::_('COM_JBLANCE_CATEGORIES'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showbudget') ? 'active' : ''; ?>">
		<a href="<?php echo $link_budget; ?>"><?php echo JText::_('COM_JBLANCE_BUDGET_RANGE'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showduration') ? 'active' : ''; ?>">
		<a href="<?php echo $link_duration; ?>"><?php echo JText::_('COM_JBLANCE_TOOLBAR_PROJECT_DURATION'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'showlocation') ? 'active' : ''; ?>">
		<a href="<?php echo $link_location; ?>"><?php echo JText::_('COM_JBLANCE_LOCATION'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'optimise') ? 'active' : ''; ?>">
		<a href="<?php echo $link_optimise; ?>"><?php echo JText::_('COM_JBLANCE_OPTIMISE_DATABASE'); ?></a>
	</li>
	<li class="divider"></li>
	<li class="<?php echo ($layout == 'configpanel') ? 'active' : ''; ?>">
		<a href="<?php echo $link_confighome; ?>"><?php echo JText::_('COM_JBLANCE_TOOLBAR_CONFIGURATION_PANEL'); ?></a>
	</li>
	<li class="<?php echo ($layout == 'dashboard') ? 'active' : ''; ?>">
		<a href="<?php echo $link_dashboard; ?>"><?php echo JText::_('COM_JBLANCE_JOOMBRI_DASHBOARD'); ?></a>
	</li>
</ul>