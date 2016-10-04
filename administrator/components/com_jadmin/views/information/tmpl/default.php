<?php 
/**
 * @package JAdmin!
 * @version 1.5.4.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

defined('_JEXEC') or die('Restricted access');
?>
<script language="javascript" type="text/javascript">
    prepYUI();
</script>
<form action="" method="post" name="adminForm">
    <div id="system_tabs" class="yui-navset" style="width: 99%; margin: 0 auto;">
	<ul class="yui-nav">
	    <li class="selected"><a href="#tab1"><em><?php echo JText::_('SYSTEMCHECK'); ?></em></a></li>
	</ul>
	<div class="yui-content">
	    <div id="tab1">
		<p>
		<table cellpadding="3" cellspacing="0" border="0">
		    <tr>
			<td class="label"><?php echo $this->settings->getAppName().' '.JText::_('JLCVERSION'); ?></td>
			<td class="green">v<?php echo $this->settings->getAppVersion().' <span class="smaller">('.JText::_('GOOD').')</span>';; ?></td>
		    </tr>
		    <tr>
			<td class="label"><?php echo $this->settings->getAppName().' '.JText::_('PLUGINVERSION'); ?></td>
			<td class="<?php
			if($this->plugin_object->version == $this->settings->getAppVersion())
			{
			    echo 'green';
			}
			else
			{
			    echo 'red bold';
			}
			?>"><?php
			if(isset($this->plugin_object->version))
			{
			    echo 'v'.$this->plugin_object->version;
			}
			else
			{
			    echo JText::_('PLUGIN_NOT_INSTALLED');
			}

			if($this->plugin_object->version == $this->settings->getAppVersion())
			{
			    echo ' <span class="smaller">('.JText::_('GOOD').')</span>';
			}
			else
			{
			    echo ' <span class="smaller">('.JText::_('PLEASE_CORRECT_THIS_ISSUE').')</span>';
			}
			?></td>
		    </tr>
		    <tr>
			<td class="label"><?php echo JText::_('PHPVERSION'); ?></td>
			<td class="<?php
			if(version_compare(PHP_VERSION, '4.0.0') >= 0)
			{
			    echo 'green';
			}
			else
			{
			    echo 'red bold';
			}
			?>"><?php
			echo PHP_VERSION;

			if(version_compare(PHP_VERSION, '4.0.0') >= 0)
			{
			    echo ' <span class="smaller">('.JText::_('GOOD').')</span>';
			}
			else
			{
			    echo ' <span class="smaller">('.JText::_('PLEASE_CORRECT_THIS_ISSUE').')</span>';
			}
			?></td>
		    </tr>
		    <tr>
			<td class="label">PHP Display Errors (display_errors):</td>
			<td class="<?php
			if(ini_get('display_errors') == 'On' || ini_get('display_errors') == 1)
			{
			    echo 'red bold';
			}
			else
			{
			    echo 'green';
			}
			?>"><?php
			if(ini_get('display_errors') == 'On' || ini_get('display_errors') == 1)
			{
			    echo 'On';
			}
			else
			{
			    echo 'Off';
			}
			
			if(ini_get('display_errors') == 'On' || ini_get('display_errors') == 1)
			{
			    echo ' <span class="smaller">('.JText::_('PLEASE_CORRECT_THIS_ISSUE').')</span>';
			}
			else
			{
			    echo ' <span class="smaller">('.JText::_('GOOD').')</span>';
			}
			?></td>
		    </tr>
		    <tr>
			<td class="label"><a href="http://us.php.net/manual/en/book.curl.php" target="_blank">PHP cURL:</a></td>
			<td class="<?php
			if(function_exists('curl_init'))
			{
			    echo 'green';
			}
			else
			{
			    echo 'red bold';
			}
			?>"><?php
			if(function_exists('curl_init'))
			{
			    echo 'Enabled';
			}
			else
			{
			    echo 'Not Installed';
			}

			if(function_exists('curl_init'))
			{
			    echo ' <span class="smaller">('.JText::_('GOOD').')</span>';
			}
			else
			{
			    echo ' <span class="smaller">('.JText::_('PLEASE_CORRECT_THIS_ISSUE').')</span>';
			}
			?></td>
		    </tr>
		</table>
		<br />
		</p>
	    </div>
	</div>
    </div>
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script language="javascript" type="text/javascript">
    var tabView = new YAHOO.widget.TabView('system_tabs');
</script>
