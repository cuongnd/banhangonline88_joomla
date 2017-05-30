<?php
/**
* @package		RedactorJS
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* RedactorJS is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * RedactorJS Editor Plugin
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Script file of joomla CMS
 */
class PlgEditorsRedactorjsInstallerScript
{
	protected $installPath = null;

	/**
	 * method to preflight the update of RedactorJS
	 *
	 * @param	string          $route      'update' or 'install'
	 * @param	JInstallerFile  $installer  The class calling this method
	 *
	 * @return void
	 */
	public function preflight($route, $installer)
	{
		$installer	= JInstaller::getInstance();

		$this->installPath 	= $installer->getPath( 'source' );


		return true;
	}

	/**
	 * method to install RedactorJS
	 *
	 * @param	JInstallerFile	$installer	The class calling this method
	 *
	 * @return void
	 */
	public function install($installer)
	{
		return $this->installLatestFoundry();
	}

	/**
	 * method to update RedactorJS
	 *
	 * @param	JInstallerFile	$installer	The class calling this method
	 *
	 * @return void
	 */
	public function update($installer)
	{
		return $this->installLatestFoundry();
	}

	/**
	 * private method to install Foundry Javascript framework
 	 * Overwrite with the newer version of Foundry
 	 *
 	 * @return void
	 */
	protected function installLatestFoundry()
	{
		// Copy media/foundry
		// Overwrite only if version is newer
		$mediaSource 		= $this->installPath . '/foundry';
		$destination 		= JPATH_ROOT . '/media/foundry';
		$overwrite			= false;
		$incomingVersion	= '';
		$installedVersion	= '';

		if( !JFolder::exists( $destination ) )
		{
			// foundry folder not found. just copy foundry folder without need to check.
			if( !JFolder::copy($mediaSource, $destination, '', true) )
			{
				return false;
			}

			return true;
		}

		// We don't have a a constant of Foundry's version, so we'll
		// find the folder name as the version number. We assumed there's
		// only ONE folder in foundry that come with the installer.
		$folder	= JFolder::folders($mediaSource);

		if(	!($incomingVersion = (string) JFile::read( $mediaSource . '/' . $folder[0] . '/version' )) )
		{
			// Can't read the version number
			return false;
		}

		$versionFile 	= $destination . '/' . $folder[0] . '/version';

		if( !JFile::exists( $versionFile ) )
		{
			// Foundry version not exists or need upgrade
			$overwrite = true;
		}
		else
		{
			$installedVersion 	= JFile::read( $versionFile );
		}
		
		$incomingVersion	= preg_replace('/[^0-9\.]/i', '', $incomingVersion);
		$installedVersion	= preg_replace('/[^0-9\.]/i', '', $installedVersion);

		if( $overwrite || version_compare($incomingVersion, $installedVersion) > 0 )
		{
			if( !JFolder::copy($mediaSource . DIRECTORY_SEPARATOR . $folder[0], $destination . DIRECTORY_SEPARATOR . $folder[0], '', true) )
			{
				return false;
			}
		}

		return true;
	}
}
