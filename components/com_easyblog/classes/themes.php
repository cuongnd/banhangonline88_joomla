<?php
/*--------------------------------------------------------------*\
	Description:	HTML template class.
	Author:			Brian Lozier (brian@massassi.net)
	License:		Please read the license.txt file.
	Last Updated:	11/27/2002
\*--------------------------------------------------------------*/

/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'tooltip.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

class CodeThemes
{
	// Holds all the template variables
	var $vars;

	// Determines whether the current browser is a dashboard or not.
	var $dashboard = false;

	// User selected theme
	var $user_theme = '';

	var $params		= null;

	/**
	 * Class Constructor
	 *
	 * @since	3.7
	 * @access	public
	 */
	public function __construct( $sel_theme = null )
	{
		$config = EasyBlogHelper::getConfig();

		$this->user_theme	= $config->get( 'layout_theme' );

		// Default theme
		$theme				= 'default';

		if ( empty($sel_theme) )
		{
			$theme = $config->get( 'layout_theme' );
		}
		elseif ( $sel_theme == 'dashboard' )
		{
			$theme	= $config->get( 'layout_dashboard_theme' );
			$this->dashboard = true;
		}

		$this->_theme	= $theme;

		$obj			= new stdClass();
		$obj->config	= EasyBlogHelper::getConfig();
		$obj->my		= JFactory::getUser();
		$obj->admin		= EasyBlogHelper::isSiteAdmin();

		$profile    = EasyBlogHelper::getTable( 'Profile','Table');
		$profile->load($obj->my->id);
		$profile->setUser($obj->my);
		$obj->profile	= $profile;

		$currentTheme 	= $this->_theme;

		if( JRequest::getVar( 'theme' , '' ) != '' )
		{
			$currentTheme	= JRequest::getVar( 'theme' );
		}

		// Legacy fix
		if( $currentTheme == 'hako - new' )
		{
			$currentTheme	= 'default';
		}

		// @rule: Set the necessary parameters here.
		$rawParams 		= EBLOG_THEMES . DIRECTORY_SEPARATOR . $currentTheme . DIRECTORY_SEPARATOR . 'config.xml';

		if( JFile::exists( $rawParams ) && !$this->dashboard )
		{
			$this->params 		= EasyBlogHelper::getRegistry();

			// @task: Now we bind the default params
			$defaultParams 		= EBLOG_THEMES . DIRECTORY_SEPARATOR . $currentTheme . DIRECTORY_SEPARATOR . 'config.ini';

			if( JFile::exists( $defaultParams ) )
			{
				$this->params->load( JFile::read( $defaultParams ) );
			}

			$themeConfig 		= $this->_getThemeConfig( $currentTheme );

			// @task: Now we override it with the user saved params
			if( !empty( $themeConfig->params ) )
			{
				$extendObj 		= EasyBlogHelper::getRegistry( $themeConfig->params );

				EasyBlogRegistryHelper::extend( $this->params , $extendObj );
			}
		}

		//is blogger mode flag
		$obj->isBloggerMode	= EasyBlogRouter::isBloggerMode();

		$this->set( 'system' , $obj );
		$this->acl		= EasyBlogACLHelper::getRuleSet();
	}

	public function resolve( $namespace )
	{
		$parts 	= explode( '/' , $namespace );
		$path 	= '';

		if( isset( $parts[ 0 ] ) && $parts[ 0 ] == 'media' )
		{
			unset( $parts[ 0 ] );

			$path 	= EASYBLOG_SCRIPTS . '/media/' . implode( '/' , $parts );
		}

		if( isset( $parts[ 0 ] ) && $parts[ 0 ] == 'dashboard' )
		{
			unset( $parts[ 0 ] );

			$path 	= EBLOG_ROOT . '/themes/dashboard/system/' . implode( '/' , $parts );
		}

		return $path;
	}

	private function _getThemeConfig( $theme )
	{
		static $loaded	= array();

		if( empty($loaded[$theme]) )
		{
			$db		= EasyBlogHelper::db();
			$query	= 'SELECT COUNT(*) FROM `#__easyblog_configs` WHERE `name` = ' . $db->quote( $theme );
			$db->setQuery( $query );

			if( $currentTheme = $db->loadResult() )
			{
				$loaded[$theme]	= EasyBlogHelper::getTable( 'Configs' );
				$loaded[$theme]->load( $theme );
			}
			else
			{
				// If theme doesn't exist in db yet, we need to load the config and save it.
				$table 		= EasyBlogHelper::getTable( 'Configs' );
				$content 	= JFile::read( EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'config.ini' );

				$table->set( 'name'		, $theme );
				$table->set( 'params'	, $content );

				$loaded[ $theme ]	= $table;
			}
		}

		return $loaded[$theme];
	}

	/**
	 * Get the path to the current theme.
	 */
	public function getPath()
	{
		$theme	= (string) trim( strtolower( $this->_theme ) );

		return EBLOG_THEMES . DIRECTORY_SEPARATOR . $theme;
	}

	function getDirection()
	{
		$document	= JFactory::getDocument();
		return $document->getDirection();
	}

	function getNouns( $text , $count , $includeCount = false )
	{
		return EasyBlogStringHelper::getNoun( $text , $count , $includeCount );
	}

	public function getParam( $key , $default = null )
	{
		return $this->params->get( $key , $default );
	}

	function chopString( $string , $length )
	{
		return JString::substr( $string , 0 , $length );
	}

	function formatDate( $format , $dateString )
	{
		$date	= EasyBlogDateHelper::dateWithOffSet($dateString);
		return EasyBlogDateHelper::toFormat($date, $format);
	}

	/**
	 * Set a template variable.
	 */
	function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	public function getName()
	{
		return $this->_theme;
	}

	/**
	 * Open, parse, and return the template file.
	 *
	 * @param $file		string	The template file name
	 * @param $vars 	Array	An array of custom variables that needs to exist in the template
	 */
	function fetch( $file , $vars = array() )
	{
		$notfound = false;

		jimport( 'joomla.filesystem.file' );

		$mainframe = JFactory::getApplication();

		// Overrides. Browser can choose different template after load
		$override	= JRequest::getWord( 'theme' , '' );
		$fileName 	= $file;

		if( $override )
		{
			$this->_theme	= $override;
		}

		// load the file based on the theme's config.ini
		// @since 1.1.x
		$info 			= EasyBlogHelper::getThemeInfo( $this->_theme );

		/**
		 * Precedence in order.
		 * 1. Template override
		 * 2. Selected theme
		 * 3. Parent theme
		 * 4. Default system theme
		 */
		if ( !$this->dashboard )
		{
			$overridePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . $file;
			$selectedPath	= EBLOG_THEMES . DIRECTORY_SEPARATOR . $this->_theme . DIRECTORY_SEPARATOR . $file;
			$parentPath		= EBLOG_THEMES . DIRECTORY_SEPARATOR . $info->get( 'parent' ) . DIRECTORY_SEPARATOR . $file;
			$defaultPath	= EBLOG_THEMES . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $file;
		}
		/**
		 * The dashboard theme
		 */
		else
		{
			$overridePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . $file;
			$selectedPath	= EBLOG_THEMES . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . $this->_theme . DIRECTORY_SEPARATOR . $file;
			$parentPath		= EBLOG_THEMES . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . $info->get( 'parent' ) . DIRECTORY_SEPARATOR . $file;
			$defaultPath	= EBLOG_THEMES . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . $file;
		}

		// 1. Template overrides
		if( JFile::exists( $overridePath ) )
		{
			$file	= $overridePath;
		}
		// 2. Selected themes
		elseif( JFile::exists( $selectedPath ) )
		{
			$file	= $selectedPath;
		}
		// 3. Parent themes
		elseif( JFile::exists( $parentPath ) )
		{
			$file	= $parentPath;
		}
		// 4. Default system theme
		else
		{
			$file	= $defaultPath;
		}

		if( !empty( $vars ) )
		{
			extract($vars);
		}

		if( isset( $this->vars ) )
		{
			extract($this->vars);
		}

		ob_start();

		if( !JFile::exists( $file ) )
		{
			echo JText::sprintf( 'Invalid template file <strong>%1s</strong>' , $fileName );
			$notfound = true;
		}
		else
		{
			include($file);
		}
		$data		= ob_get_contents();
		ob_end_clean();

		if( $notfound )
		{
			return false;
		}

		return $data;
	}

	/*
	 * Renders a nice checkbox switch.
	 *
	 * @param	string	$option		Name attribute for the checkbox.
	 * @param	string	$sate		State of the checkbox, checked or not.
	 * @return	string	HTML output.
	 */
	public function renderCheckbox( $option , $state )
	{
		ob_start();
	?>
		<div class="si-optiontap">
			<label class="option-enable<?php echo $state == 1 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_EASYBLOG_NO_SWITCH' );?></span></label>
			<label class="option-disable<?php echo $state == 0 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_EASYBLOG_YES_SWITCH' ); ?></span></label>
			<input name="<?php echo $option; ?>" value="<?php echo $state;?>" type="radio" class="radiobox" checked="checked" style="display: none;" />
		</div>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function json_encode( $value )
	{
		include_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json	= new Services_JSON();

		return $json->encode( $value );
	}

	public function json_decode( $value )
	{
		include_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json	= new Services_JSON();

		return $json->decode( $value );
	}

	function escape( $val )
	{
		return EasyBlogStringHelper::escape( $val );
	}
}
