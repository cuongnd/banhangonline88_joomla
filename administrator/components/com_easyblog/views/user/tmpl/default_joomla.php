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
<table width="100%" cellpadding="5">
	<tr>
		<td width="60%" valign="top">
			<dl id="subuser" class="tabs">
				<dt class="account open" style="cursor: pointer;">
					<span id="account"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TAB_ACCOUNT_DETAILS' );?></span>
				</dt>
				<dt class="blogger closed" style="cursor: pointer;">
					<span id="blogger"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_TAB_ACCOUNT_INFO' );?></span>
				</dt>
			</dl>

			<div class="user-account tab-details current">
				<?php echo $this->loadTemplate( 'account' ); ?>
			</div>

			<div style="display:none;" class="user-blogger tab-details current">
				<?php echo $this->loadTemplate( 'blogger' ); ?>
			</div>
		</td>
		<td width="38%" valign="top">
		<?php
			$pane	= JPane::getInstance('sliders', array('allowAllClose' => true));

			echo $pane->startPane("content-pane");
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FEEDBURNER' ) , "feedburner-page" );
			echo $this->loadTemplate( 'feedburner' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_FACEBOOK' ), "facebook-page" );
			echo $this->loadTemplate( 'facebook' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_TWITTER' ), "twitter-page" );
			echo $this->loadTemplate( 'twitter' );
			echo $pane->endPanel();
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_LINKEDIN' ), "linkedin-page" );
			echo $this->loadTemplate( 'linkedin' );
			echo $pane->endPanel();

			if( $this->config->get( 'integration_google_adsense_enable' ) )
			{
				echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_ADSENSE' ), "adsense-page" );
				echo $this->loadTemplate( 'adsense' );
				echo $pane->endPanel();
			}
			
			echo $pane->startPanel( JText::_( 'COM_EASYBLOG_BLOGGERS_PARAMS_TITLE_GOOGLE' ), "google-page" );
			echo $this->loadTemplate( 'google' );
			echo $pane->endPanel();
			echo $pane->endPane();
		?>
		</td>
	</tr>
</table>
