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

jimport( 'joomla.filesystem.archive' );

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewUpdater extends EasyBlogAdminView
{
	public function getUniqueName( $storage )
	{
		$original	= $storage;

		$i			= 0;
		while( JFolder::exists( $storage ) )
		{
			$i++;
			$storage	= $original . '_' . $i;
		}

		return $storage;
	}

	public function download( $version )
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . '/administrator' );

		$ajax	= new Ejax();

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . '/administrator' );

		$storage	= JPATH_ROOT . '/tmp/foundry';
		$storage 	= $this->getUniqueName( $storage );

		// If folder doesn't exist, create them first.
		if( !JFolder::exists( $storage ) )
		{
			JFolder::create( $storage );
		}

		$destination	= $storage . '/foundry.zip';

		// Get foundry's version
		$versionFile 	= JPATH_ROOT . '/media/foundry/3.1/version';
		$localVersion 	= JFile::read( $versionFile );

		// Remove newline from foundry's version
		$localVersion 	= trim( $localVersion );
		
		$connector		= EasyBlogHelper::getHelper( 'Connectors' );
		$infoServer 	= EBLOG_UPDATER_SERVER . 'foundry/' . $localVersion . '/info';
		$connector->addUrl( $infoServer );
		$connector->addQuery( 'apikey' , EasyBlogHelper::getConfig()->get( 'apikey' ) );
		$connector->setMethod( 'POST' );
		$connector->execute();

		$result 		= $connector->getResult( $infoServer );

		if( $result == '#Foundry no updates' || $result == 'Invalid api key provided')
		{
			// @TODO: Show nothing to be updated in foundry.
		}
		else
		{
			// Write the zip file to the path.
			JFile::write( $destination , $result );

			// Extract foundry zip.
			JArchive::extract( $destination , $storage );

			// Rename the old foundry folder.
			$foundryPath 	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'foundry' . DIRECTORY_SEPARATOR . '3.1';

			// Try to delete the foundry path.
			$deleteState 	= JFolder::delete( $foundryPath );

			if( !$deleteState )
			{
				$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'There was some error deleting the foundry framework due to permission issues.' ) . '</div>");' );
			}

			// Move the new updated foundry to the correct path.
			$state			= JFolder::move( $storage . DIRECTORY_SEPARATOR . '3.1' , $foundryPath );

			if( !$state )
			{
				$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'There was some error copying the foundry framework over to the media folder.' ) . '</div>");' );
			}
		}

		$ajax->script( '$("#bar-progress").css("width" , "10%");' );
		$ajax->script( '$("#bar-progress #progress-indicator").html("10%");' );
		$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'Updating Foundry Framework' ) . '</div>");' );
		$ajax->script( 'ejax.load( "updater", "updateEasyBlog" , "' . $version . '");' );
		$ajax->send();
	}

	public function updateEasyBlog( $version )
	{
		$ajax	= new Ejax();

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );

		$folderName	= 'easyblog_patches_' . $version;
		$storage	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $folderName;

		$original	= $storage;

		$i			= 0;
		while( JFolder::exists( $storage ) )
		{
			$i++;
			$storage	= $original . '_' . $i;

		}

		// @rule: Test if file name has changed
		if( $i > 0 )
		{
			// We need to minus one because i is now
			$folderName	= $folderName . '_' . $i;
		}

		// If folder doesn't exist, create them first.
		if( !JFolder::exists( $storage ) )
		{
			JFolder::create( $storage );
		}

		$filename	= 'patch_' . $version . '.zip';
		$filepath	= $storage . DIRECTORY_SEPARATOR . $filename;

		$connector		= EasyBlogHelper::getHelper( 'Connectors' );
		$localbuild		= EasyBlogHelper::getLocalVersion( true );
		$infoServer		= EBLOG_UPDATER_SERVER . 'from/' . $localbuild . '/download';

		$connector->addUrl( $infoServer );
		$connector->addQuery( 'apikey' , EasyBlogHelper::getConfig()->get( 'apikey' ) );
		$connector->setMethod( 'POST' );
		$connector->execute();

		$result			= $connector->getResult( $infoServer );

		if( $result == 'Invalid api key provided' )
		{
			$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_INVALID_API_KEY' ) . '</div>");' );
			return $ajax->send();
		}

		if( $result === false )
		{
			$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_CANT_CONNECT' ) . '</div>");' );
			return $ajax->send();
		}

		JFile::write( $filepath , $result );
		$ajax->script( '$("#bar-progress").css("width" , "20%");' );
		$ajax->script( '$("#bar-progress #progress-indicator").html("20%");' );
		$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_EXTRACTING_PATCH_FILES' ) . '</div>");' );
		$ajax->script( 'ejax.load( "updater", "extractFiles" , "' . $folderName . '" , "' . $filename . '");' );
		$ajax->send();
	}

	public function extractFiles( $folder , $filename )
	{
		$ajax	= new Ejax();

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );

		// This is where the patch files are stored.
		$storage		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $folder;

		// This is where the path file should be extracted into.
		$filePath		= $storage . DIRECTORY_SEPARATOR . $filename;

		$connector		= EasyBlogHelper::getHelper( 'Connectors' );
		$localbuild		= EasyBlogHelper::getLocalVersion( true );
		$infoServer		= EBLOG_UPDATER_SERVER . 'from/' . $localbuild . '/info';


		$connector->addUrl( $infoServer );
		$connector->addQuery( 'apikey' , EasyBlogHelper::getConfig()->get( 'apikey' ) );
		$connector->setMethod( 'POST' );
		$connector->execute();
		$result			= $connector->getResult( $infoServer );

		// @rule: Store info file in the path.
		JFile::write( $storage . DIRECTORY_SEPARATOR . 'info.json' , $result );

		require_once(EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

		$json		= new Services_JSON();
		$info		= $json->decode( $result );

		

		// @rule: Get the md5 of the zip and test the validity of the zipped contents.
		if( $info->md5 != md5_file( $filePath ) )
		{
			// @TODO: Download corrupted
			$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_MD5_CHECKSUM_NOT_MATCH' ) . '</div>");' );
			return $ajax->send();
		}

		// @rule: Extract the archive
		if( !JArchive::extract( $filePath , $storage ) )
		{
			$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_EXTRACTING_PATCH_FILE_ERROR' ) . '</div>");' );
			return $ajax->send();
		}

		// @rule: Delete the archive once the extraction is completed.
		JFile::delete( $filePath );

		$ajax->script( '$("#bar-progress").css("width" , "25%");' );
		$ajax->script( '$("#bar-progress #progress-indicator").html("25%");' );
		$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_EXTRACTING_PATCH_FILE_SUCCESSFULLY' ) . '</div>");' );
		$ajax->script( 'ejax.load("updater", "copyFiles" , "' . $folder . '","' . $filename . '");');
		$ajax->send();
	}

	public function copyFiles( $folder , $filename , $fileCounter = 0 )
	{
		$ajax	= new Ejax();

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );

		// This is where the patch files are stored.
		$storage		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $folder;

		// This is where the path file should be extracted into.
		$filePath		= $storage . DIRECTORY_SEPARATOR . $filename;

		$result			= JFile::read( $storage . DIRECTORY_SEPARATOR . 'info.json' );
		require_once(EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

		require_once(EBLOG_CLASSES . DS . 'json.php' );

		$json = new Services_JSON();

		$info = $json->decode( $result );

		$files = $info->files;

		$each			= 75 / (count( $files) );

		if( $fileCounter == 0 )
		{
			$startProgress	= 25 + $each;
		}
		else
		{
			$startProgress	= ( $fileCounter * $each ) + $each + 25;
		}

		$startProgress = min( round( $startProgress ), 100 );

		if( !isset( $files[ $fileCounter ] ) )
		{
			$ajax->script( '$("#progress-box").find("i.bar-loader").remove();' );
			$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_PATCH_PROCESS_COMPLETED' ) . '</div>");' );
			$ajax->script( '$("#bar-progress").css("width" , "' . $startProgress . '%");' );
			$ajax->send();
		}

		// @rule: Now we got the info, copy the files and replace them accordingly.
		if( is_array( $files ) )
		{
			$file = $files[$fileCounter];

			$filePath		= $file->file;

			// Replace all forward slashes (/) to proper directory separator.
			$filePath		= str_ireplace( '/', DIRECTORY_SEPARATOR, $filePath );

			$localSource	= JPATH_ROOT . DIRECTORY_SEPARATOR . $filePath;
			$patchSource	= $storage . DIRECTORY_SEPARATOR . $filePath;
			$backupSource	= JPATH_ROOT . DIRECTORY_SEPARATOR . $filePath . '.backup';

			// @rule: Test for sql queries that needs to be executed.
			if( stristr( $file->file , 'administrator/components/com_easyblog/upgrades/' ) !== false )
			{
				// We just want to include this file so that the script get's executed.
				include_once( $patchSource );

				$ajax->script( '$("#result-holder").append("<div>' . JText::sprintf( 'COM_EASYBLOG_UPDATER_EXECUTE_SCRIPT' , $file->file ) . '</div>");' );
			}
			else if( stristr( $file->file , 'administrator/components/com_easyblog/query/' ) !== false )
			{
				$query		= JFile::read( $patchSource );

				$db			= EasyBlogHelper::db();
				$db->setQuery( $query );
				$db->queryBatch();

				$ajax->script( '$("#result-holder").append("<div>' . JText::sprintf( 'COM_EASYBLOG_UPDATER_EXECUTE_SQL' , $file->file ) . '</div>");' );
			}
			else
			{
				switch( $file->status )
				{
					// File is modified, update accordingly.
					case 'M':
						// @rule: Make backups first.
						JFile::copy( $localSource , $backupSource );

						// Overwrite the files
						if( JFile::copy( $patchSource , $localSource ) )
						{
							$ajax->script( '$("#result-holder").append("<div>' . JText::sprintf( 'COM_EASYBLOG_UPDATER_FILE_UPDATED' , $file->file ) . '</div>");' );
						}

						// @task: For language files, we need to replace it with the appropriate quote fixes.
						if( stristr( $file->file , 'language/' ) !== false && EasyBlogHelper::getJoomlaVersion() < '1.6' )
						{
							// @task: We need to do a search and replace of the local file for "_QQ_" since the source server stores the language file as "_QQ_"
							$contents	= JFile::read( $localSource );
							$contents	= str_ireplace( '"_QQ_"' , '\"' , $contents );

							JFile::write( $localSource , $contents );
						}
					break;
					case 'A':
						// @rule: Create necessary folders
						$this->createFolders( $filePath );

						// Overwrite the files
						if( JFile::copy( $patchSource , $localSource ) )
						{
							$ajax->script( '$("#result-holder").append("<div>' . JText::sprintf( 'COM_EASYBLOG_UPDATER_FILE_ADDED' , $file->file ) . '</div>");' );
						}
					break;

					// File is removed, remove existing files.
					case 'R':
						// @rule: Make backups first.
						JFile::copy( $localSource , $backupSource );

						if( JFile::exists( $localSource ) )
						{
							if( JFile::delete( $localSource ) )
							{
								$ajax->script( '$("#result-holder").append("<div>' . JText::sprintf( 'COM_EASYBLOG_UPDATER_FILE_Deleted' , $file->file ) . '</div>");' );
							}
						}
					break;
				}
			}

			// Note: This logic is wrong, it never goes in.
			if( ($fileCounter + 1 ) > count( $files ) )
			{
				// @rule: Once the patch is completed, remove loader image.
				$ajax->script( '$("#progress-box").find("i.bar-loader").remove();' );
				$ajax->script( '$("#result-holder").append("<div>' . JText::_( 'COM_EASYBLOG_UPDATER_PATCH_PROCESS_COMPLETED' ) . '</div>");' );
			}
			else
			{
				$x	= $fileCounter + 1;
				$ajax->script( 'ejax.load("updater", "copyFiles" , "' . $folder . '","' . $filename . '","' . $x . '");');
			}
		}

		$startProgress = min( round( $startProgress ), 100 );

		$ajax->script( '$("#bar-progress #progress-indicator").html("' . $startProgress . '%");' );
		$ajax->script( '$("#bar-progress").css("width" , "' . $startProgress . '%");' );
		$ajax->send();
	}

	function createFolders( $file )
	{
		$path	= dirname( $file );
		$paths	= explode( '/' , $path );
		$total	= count( $paths );
		$str	= JPATH_ROOT;

		for( $i = 0; $i < $total; $i++ )
		{
			$str	.= DIRECTORY_SEPARATOR . $paths[ $i ];

			if( !JFolder::exists( $str ) )
			{
				JFolder::create( $str );
			}
		}
	}
}
