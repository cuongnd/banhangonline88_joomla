<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	20 March 2012
 * @file name	:	views/admproject/tmpl/about.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	JoomBri Admin Dashboard (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div class="well media">
		<a class="pull-left" href="#"> 
			<img src="http://www.joombri.in/images/documents/joombri-logo.png" border="0" alt="JoomBri!" />
		</a>
		<div class="media-body">
			<h4 class="media-heading">About the Team</h4>
			<p>
				The team behind JoomBri, BriTech Solutions is in the software
				development and maintenance for more than two years. Our aim is to develop softwares to enhance open source technologies like Joomla!,
				and yet agile in emerging technologies as we continuously explore the constantly changing frontier of
				software development.
				
				Please visit <a href="http://www.britech.in">www.britech.in </a>to find out more about us.
			<p>
		</div>
		<hr />
		<p style="font-weight: 700;"><?php echo JText::sprintf('COM_JBLANCE_VERSION', $this->version); ?></p>
	</div>
</div>