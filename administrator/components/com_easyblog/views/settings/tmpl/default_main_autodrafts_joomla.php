<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog.ready(function($)
{
	$( '#truncateType' ).bind( 'change' , function(){
		if( $( this ).val() == 'chars' || $( this ).val() == 'words' )
		{
			$( '#maxchars' ).show();
			$( '#maxtag' ).hide();
		}
		else
		{
			$( '#maxtag' ).show();
			$( '#maxchars' ).hide();
		}
	});
});
</script>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_AUTODRAFTING_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_AUTODRAFTING' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_AUTODRAFTING_DESC' ); ?></div>
							<?php echo $this->renderCheckbox( 'main_autodraft' , $this->config->get( 'main_autodraft' ) );?>
						</div>						
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_AUTODRAFTING_INTERVAL' ); ?>
						</span>
					</td>
					<td class="value">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_AUTODRAFTING_INTERVAL_DESC' ); ?></div>
							<input type="text" name="main_autodraft_interval" class="inputbox" style="width: 30px;text-align: center;" value="<?php echo $this->config->get('main_autodraft_interval', '0' );?>" />
							<?php echo JText::_( 'COM_EASYBLOG_SECONDS' );?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top" valign="top">&nbsp;</td>
	</tr>
</table>
