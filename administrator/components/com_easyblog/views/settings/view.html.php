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

jimport( 'joomla.html.pane' );
require( EBLOG_ADMIN_ROOT . '/views.php');

class EasyBlogViewSettings extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.setting' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();

		$config		= EasyBlogHelper::getConfig();
		$jconfig	= JFactory::getConfig();

		$dstOptions	= array();
		$iteration 	= -12;
		for( $i = 0; $i <= 24; $i++ )
		{
			$dstOptions[]	= JHTML::_('select.option', $iteration, $iteration);
			$iteration++;
		}

		$dstList = JHTML::_('select.genericlist',  $dstOptions, 'main_dstoffset', 'class="inputbox" size="1"', 'value', 'text', $config->get('main_dstoffset', 0));

		//check if jomcomment installed.
		$jcInstalled = false;
		if(file_exists(JPATH_ROOT . '/administrator/components/com_jomcomment/config.jomcomment.php' ) )
		{
			$jcInstalled = true;
		}

		//check if jcomments installed.
		$jcommentInstalled = false;
		$jCommentFile 	= JPATH_ROOT . '/components/com_jcomments/jcomments.php';
		
		if( JFile::exists( $jCommentFile ) )
		{
			$jcommentInstalled = true;
		}

		//check if rscomments installed.
		$rscommentInstalled = false;
		$rsCommentFile 	= JPATH_ROOT . '/components/com_rscomments/rscomments.php';

		if( JFile::exists( $rsCommentFile ) )
		{
			$rscommentInstalled = true;
		}

		// @task: Check if easydiscuss plugin is installed and enabled.
		$easydiscuss	= JPluginHelper::isEnabled( 'content' , 'easydiscuss' );

		$komento		= JPluginHelper::isEnabled( 'content' , 'komento' );

		$defaultSAId	= EasyBlogHelper::getDefaultSAIds();

		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();

		$socialButtonsOrder	= $this->getSocialButtonOrder();

		$this->assignRef( 'jConfig' 			, $jconfig );
		$this->assignRef( 'config' 				, $config );
		$this->assignRef( 'dstList' 			, $dstList );
		$this->assignRef( 'jcInstalled' 		, $jcInstalled );
		$this->assignRef( 'easydiscuss'			, $easydiscuss );
		$this->assignRef( 'komento'				, $komento );
		$this->assignRef( 'jcommentInstalled' 	, $jcommentInstalled );
		$this->assignRef( 'rscommentInstalled' 	, $rscommentInstalled );
		$this->assignRef( 'defaultSAId' 		, $defaultSAId );
		$this->assignRef( 'joomlaversion' 		, $joomlaVersion );
		$this->assignRef( 'socialButtonsOrder' , $socialButtonsOrder );

		parent::display($tpl);
	}

	public function getSocialButtonOrder()
	{
		$config = EasyBlogHelper::getConfig();

		$socialButtons  = explode( ',', EBLOG_SOCIAL_BUTTONS );

		$socialButtonOrders = array();

		foreach($socialButtons as $key)
		{
			$config_key = 'integrations_order_' . $key;
			$socialButtonOrders[$key]   = $config->get( $config_key , '0');
		}

		return $socialButtonOrders;
	}

	function getEditorList( $selected )
	{
		$db		= EasyBlogHelper::db();

		// compile list of the editors
		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$query = 'SELECT `element` AS value, `name` AS text'
					.' FROM `#__extensions`'
					.' WHERE `folder` = "editors"'
					.' AND `type` = "plugin"'
					.' AND `enabled` = 1'
					.' ORDER BY ordering, name'
					;
		}
		else
		{
			$query = 'SELECT element AS value, name AS text'
					.' FROM #__plugins'
					.' WHERE folder = "editors"'
					.' AND published = 1'
					.' ORDER BY ordering, name'
					;
		}

		//echo $query;

		$db->setQuery($query);
		$editors = $db->loadObjectList();

		if(count($editors) > 0)
		{
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
				$lang = JFactory::getLanguage();
				for($i = 0; $i < count($editors); $i++)
				{
					$editor = $editors[$i];
					$lang->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
					$editor->text   = JText::_($editor->text);
				}
			}
		}

		return JHTML::_('select.genericlist',  $editors , 'layout_editor', 'class="inputbox" size="1"', 'value', 'text', $selected );
	}

	function getThemes( $selectedTheme = 'default' )
	{
		$html	= '<select name="layout_theme" class="inputbox">';

		$themes	= $this->get( 'Themes' );

		for( $i = 0; $i < count( $themes ); $i++ )
		{
			$theme		= JString::strtolower( $themes[ $i ] );

			if ( $theme != 'dashboard' ) {
				$selected	= ( $selectedTheme == $theme ) ? ' selected="selected"' : '';
				$html		.= '<option' . $selected . '>' . $theme . '</option>';
			}
		}

		$html	.= '</select>';

		return $html;
	}

	function getDashboardThemes( $selectedTheme = 'system' )
	{
		$html	= '<select name="layout_dashboard_theme" class="inputbox">';

		$model	= $this->getModel( 'Settings' );
		$themes	= $model->getThemes( true );

		for( $i = 0; $i < count( $themes ); $i++ )
		{
			$theme		= JString::strtolower( $themes[ $i ] );

			$selected	= ( $selectedTheme == $theme ) ? ' selected="selected"' : '';
			$html		.= '<option' . $selected . '>' . $theme . '</option>';
		}

		$html	.= '</select>';

		return $html;
	}

	function getBloggerThemes()
	{
		$config = EasyBlogHelper::getConfig();

		$themes	= $this->get( 'Themes' );

		$options = array ();

		foreach ($themes as $theme)
		{
			$options[] = JHTML::_('select.option', $theme, $theme);
		}

		$previouslyAvailable = $config->get('layout_availablebloggertheme');

		return JHTML::_('select.genericlist', $options, 'layout_availablebloggertheme[]', 'multiple="multiple" style="width: 200px;height: 200px;"', 'value', 'text', explode('|', $previouslyAvailable) );
	}

	function getEmailsTemplate()
	{
		JHTML::_('behavior.modal' , 'a.modal' );
		$html	= '';

		$files	= JFolder::files( EBLOG_THEMES . DIRECTORY_SEPARATOR . 'default' );
		$emails	= array();

		foreach( $files as $file )
		{
			if( JString::substr( $file , 0 , 5 ) == 'email' )
			{
				$emails[] 	= $file;
			}
		}


		ob_start();

		foreach($emails as $email)
		{
		?>
			<div>
				<div style="float:left; margin-right:5px; clear:none">
				<?php echo JText::_($email); ?>
				</div>
				<div style="margin-top:5px; clear:none">
				[
				<?php
				if(is_writable(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $email))
				{
				?>
					<a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 500}}" href="index.php?option=com_easyblog&view=settings&layout=editEmailTemplate&file=<?php echo $email; ?>&tmpl=component&browse=1"><?php echo JText::_('COM_EASYBLOG_EDIT');?></a>
				<?php
				}
				else
				{
				?>
					<span style="color:red; font-weight:bold;"><?php echo JText::_('COM_EASYBLOG_UNWRITABLE');?></span>
				<?php
				}
				?>
				]
				</div>
			</div>
		<?php
		}
		$html   = ob_get_contents();
		@ob_end_clean();

		return $html;
	}

	function editEmailTemplate()
	{
		$file		= JRequest::getVar('file', '', 'GET');
		$filepath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $file;
		$content	= '';
		$html		= '';
		$msg		= JRequest::getVar('msg', '', 'GET');
		$msgType	= JRequest::getVar('msgtype', '', 'GET');

		ob_start();

		if(!empty($msg))
		{
			$document = JFactory::getDocument();
			$document->addStyleSheet( JURI::root() . '/components/com_easyblog/assets/css/common.css' );
		?>
			<div id="eblog-message" class="<?php echo $msgType; ?>"><?php echo $msg; ?></div>
		<?php
		}

		if(is_writable($filepath))
		{
			$content = JFile::read($filepath);
		?>
			<form name="emailTemplate" id="emailTemplate" method="POST">
				<textarea rows="28" cols="93" name="content"><?php echo $content; ?></textarea>
				<input type="hidden" name="option" value="com_easyblog">
				<input type="hidden" name="c" value="settings">
				<input type="hidden" name="task" value="saveEmailTemplate">
				<input type="hidden" name="file" value="<?php echo $file; ?>">
				<input type="hidden" name="tmpl" value="component">
				<input type="hidden" name="browse" value="1">
				<input type="submit" name="save" value="<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE' );?>">
				<?php if(EasyBlogHelper::getJoomlaVersion() <= '1.5') : ?>
				<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_CLOSE' );?>" onclick="window.parent.document.getElementById('sbox-window').close();">
				<?php endif; ?>
			</form>
		<?php
		}
		else
		{
		?>
			<div><?php echo JText::_('COM_EASYBLOG_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_UNWRITABLE'); ?></div>
		<?php
		}

		$html = ob_get_contents();
		@ob_end_clean();

		echo $html;
	}

	public function getPaginationSettings( $key , $selected )
	{
		$listLength = array();
		$listLength[] = JHTML::_('select.option', '0', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_USE_JOOMLA_LIST_LENGTH' ) );

		for( $i = 1; $i <= 10; $i++ )
		{
			$listLength[] = JHTML::_('select.option', $i , JText::_( $i ) );	
		}

		$listLength[] = JHTML::_('select.option', '15', JText::_( '15' ) );
		$listLength[] = JHTML::_('select.option', '20', JText::_( '20' ) );
		$listLength[] = JHTML::_('select.option', '25', JText::_( '25' ) );
		$listLength[] = JHTML::_('select.option', '30', JText::_( '30' ) );
		$listLength[] = JHTML::_('select.option', '50', JText::_( '50' ) );
		$listLength[] = JHTML::_('select.option', '100', JText::_( '100' ) );
		return JHTML::_('select.genericlist', $listLength, $key , 'size="1" class="inputbox"', 'value', 'text', $selected );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_HOME_SETTINGS' ), 'settings' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolbarHelper::custom( 'export' , 'export' , '' , JText::_( 'COM_EASYBLOG_EXPORT_SETTINGS' ) , false );
		JToolbarHelper::custom( 'import' , 'import' , '' , JText::_( 'COM_EASYBLOG_IMPORT_SETTINGS' ) , false );
		JToolbarHelper::divider();
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	public function export()
	{
		$this->checkAccess( 'setting' );

		$db 	= JFactory::getDBO();

		$query 	= 'SELECT `params` FROM ' . $db->quoteName( '#__easyblog_configs' ) . ' WHERE `name` = ' . $db->Quote( 'config' );
		$db->setQuery( $query );

		$data 	= $db->loadResult();
		var_dump( $data );exit;
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
