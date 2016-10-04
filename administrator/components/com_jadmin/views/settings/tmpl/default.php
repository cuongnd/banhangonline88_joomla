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

jimport('joomla.filesystem.file');
?>
<script language="javascript" type="text/javascript">
    prepYUI();
</script>
<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <div id="settings" class="yui-navset">
	<ul class="yui-nav">
	    <li class="selected"><a href="#tab1"><em><?php echo JText::_('GENERAL'); ?></em></a></li>
	    <li><a href="#tab2"><em><?php echo JText::_('ADVANCED'); ?></em></a></li>
	    <li><a href="#tab3"><em><?php echo JText::_('PROXY'); ?></em></a></li>
	</ul>
	<div class="yui-content">
	    <div id="tab1">
		<p>
		<table cellpadding="3" cellspacing="0" border="0">
		    <tr>
			<td class="label"><span class="hasTip" title="<?php echo JText::_('SITENAME_HELPTIP'); ?>"><?php echo JText::_('SITENAME'); ?></span></td>
			<td>
			    <input type="text" id="site_name" name="site_name" value="<?php echo $this->settings->getSiteName(); ?>" size="30" />
			</td>
		    </tr>
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('ACTIVITY_MONITORING_HELPTIP'); ?>"><?php echo JText::_('ACTIVITY_MONITORING'); ?></td>
			<td>
			    <label id="activity-monitor-container">
				<select id="activity_monitor_selector" name="activity_monitor_selector">
				    <option value="0"<?php if($this->settings->getSetting('activity_monitor') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('DISABLED'); ?></option>
				    <option value="1"<?php if($this->settings->getSetting('activity_monitor') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('ENABLED'); ?></option>
				</select>
			    </label>
			    <input type="hidden" id="activity_monitor_value" name="activity_monitor_value" value="<?php echo $this->settings->getSetting('activity_monitor'); ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('MONITORING_TIMEOUT_HELPTIP'); ?>"><?php echo JText::_('MONITORING_TIMEOUT'); ?></td>
			<td>
			    <input type="text" size="4" id="activity_monitor_expiration" name="activity_monitor_expiration" value="<?php echo $this->settings->getSetting('activity_monitor_expiration'); ?>" /> <?php echo JText::_('IN_MINUTES'); ?>
			</td>
		    </tr>
		</table>
		<br />
		    
		
		<script language="Javascript" type="text/javascript">
		    var activityMonitorMenu = new YAHOO.widget.Button({
			id: "activity-monitor-menubutton",
			name: "activity-monitor-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('activity_monitor') == 0) { echo JText::_('DISABLED'); } elseif($this->settings->getSetting('activity_monitor') == 1) { echo JText::_('ENABLED'); }; ?></em>",
			type: "menu",
			menu: "activity_monitor_selector",
			container: "activity-monitor-container"
		    });

		    //	Register a "selectedMenuItemChange" event handler that will sync the
		    //	Button's "label" attribute to the MenuItem that was clicked.
		    activityMonitorMenu.on("selectedMenuItemChange", onActivityMonitorSelectedMenuItemChange);
		</script>
		</p>
	    </div>
	    <div id="tab2">
		<p>
		    <table cellpadding="3" cellspacing="0" border="0">
			<tr>
			    <td class="label hasTip" title="Compress all data transmitted to/from operators using GZip compression.<br />(Default: Yes)">GZip Compression:</td>
			    <td>
				<label id="use-gzip-container">
				    <select id="use_gzip_selector" name="use_gzip_selector">
					    <option value="0"<?php if($this->settings->getSetting('use_gzip') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('use_gzip') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="use_gzip" name="use_gzip" value="<?php echo $this->settings->getSetting('use_gzip'); ?>" />
			    </td>
			</tr>
		    </table>
		    <br />
		    <script language="Javascript" type="text/javascript">
			var useGZipMenu = new YAHOO.widget.Button({
			    id: "use-gzip-menubutton",
			    name: "use-gzip-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('use_gzip') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('use_gzip') == 1) { echo JText::_('JYES'); }; ?></em>",
			    type: "menu",
			    menu: "use_gzip_selector",
			    container: "use-gzip-container"
			});

			useGZipMenu.on("selectedMenuItemChange", onUseGZipMenuItemChange);
		    </script>
		</p>
	    </div>
	    <div id="tab3">
		<p>
		    <table cellpadding="3" cellspacing="0" border="0">
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('USE_PROXY_HELPTIP'); ?>"><?php echo JText::_('USE_PROXY_LBL'); ?></td>
			    <td>
				<label id="use-proxy-container">
				    <select id="use_proxy_selector" name="use_proxy_selector">
					    <option value="0"<?php if($this->settings->getSetting('use_proxy') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('use_proxy') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="use_proxy" name="use_proxy" value="<?php echo $this->settings->getSetting('use_proxy'); ?>" />
			    </td>
			</tr>

			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('USE_SOCKS_HELPTIP'); ?>"><?php echo JText::_('USE_SOCKS_LBL'); ?></td>
			    <td>
				<label id="use-socks-container">
				    <select id="use_socks_selector" name="use_socks_selector">
					    <option value="0"<?php if($this->settings->getSetting('use_socks') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('use_socks') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="use_socks" name="use_socks" value="<?php echo $this->settings->getSetting('use_socks'); ?>" />
			    </td>
			</tr>

			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('PROXY_HOST_HELPTIP'); ?>"><?php echo JText::_('PROXY_HOST_LBL'); ?></td>
			    <td>
				<input type="text" id="proxy_uri" name="proxy_uri" value="<?php echo $this->settings->getSetting('proxy_uri'); ?>" size="30" />
			    </td>
			</tr>
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('PROXY_PORT_HELPTIP'); ?>"><?php echo JText::_('PROXY_PORT_LBL'); ?></td>
			    <td>
				<input type="text" id="proxy_port" name="proxy_port" value="<?php if($this->settings->getSetting('proxy_port') > 0) { echo $this->settings->getSetting('proxy_port'); } ?>" size="5" />
			    </td>
			</tr>
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('PROXY_AUTH_HELPTIP'); ?>"><?php echo JText::_('PROXY_AUTH_LBL'); ?></td>
			    <td>
				<input type="text" id="proxy_auth" name="proxy_auth" value="<?php echo $this->settings->getSetting('proxy_auth'); ?>" size="15" />
			    </td>
			</tr>
		    </table>
		    <br />
		    <script language="Javascript" type="text/javascript">
			var useProxyMenu = new YAHOO.widget.Button({
			    id: "use-proxy-menubutton",
			    name: "use-proxy-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('use_proxy') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('use_proxy') == 1) { echo JText::_('JYES'); }; ?></em>",
			    type: "menu",
			    menu: "use_proxy_selector",
			    container: "use-proxy-container"
			});

			var useSocksMenu = new YAHOO.widget.Button({
			    id: "use-socks-menubutton",
			    name: "use-socks-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('use_socks') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('use_socks') == 1) { echo JText::_('JYES'); }; ?></em>",
			    type: "menu",
			    menu: "use_socks_selector",
			    container: "use-socks-container"
			});
			
			//	Register a "selectedMenuItemChange" event handler that will sync the
			//	Button's "label" attribute to the MenuItem that was clicked.
			useProxyMenu.on("selectedMenuItemChange", onUseProxySelectedMenuItemChange);
			useSocksMenu.on("selectedMenuItemChange", onUseSocksSelectedMenuItemChange);
		    </script>
		</p>
	    </div>
	</div>
    </div>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="task" value="save" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script language="javascript" type="text/javascript">
    var tabView = new YAHOO.widget.TabView('settings');
    var popupLanguagesTabView = new YAHOO.widget.TabView('popup_languages');
    var languagesTabView = new YAHOO.widget.TabView('language_strings');
</script>


