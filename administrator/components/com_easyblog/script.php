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

ini_set("display_errors", 1);

/**
 * This file and method will automatically get called by Joomla
 * during the installation process
 **/

if(!defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}

class com_EasyBlogInstallerScript
{
	var $version;
	var $message;
	var $status;
	var	$sourcePath;

	function execute()
	{
		$message	= $this->message;
		$status		= $this->status;
		$sourcePath	= $this->sourcePath;

		//create default easy blog config
		if( !configExist() )
		{
			if(!createConfig())
			{
				$message[] = 'Warning : The system encounter an error when it tries to create default config. Please kindly configure your Easy Blog manually.';
			}
		}

		//update Db columns first before proceed.
		updateEasyBlogDBColumns();

		//check if need to create default category
		if( !blogCategoryExist() )
		{
			if(!createBlogCategory())
			{
				$message[] = 'Warning : The system encounter an error when it tries to create default blog categories. Please kindly create the categories manually.';
			}
		}

		//check if need to create sample post
		if( !postExist() )
		{
			if(!createSamplePost())
			{
				$message[] = 'Warning : The system encounter an error when it tries to create some sample post.';
			}
		}

		//check if twitter table exist.
		if( twitterTableExist() )
		{
			//migrate twitter data if the table exist
			if(!twitterTableMigrate())
			{
				$message[] = 'Warning : The system encounter an error when it tries to migrate your social share data to a new table. Please kindly migrate the data manually.';
			}
			else
			{
				if(!twitterTableRemove())
				{
					$message[] = 'Warning : The system encounter an error when it tries to remove the unused twitter table. Please kindly remove the table manually.';
				}
			}
		}

		//truncate the table before recreating the default acl rules.
		if(!truncateACLTable())
		{
			$message[] = 'Fatal Error : The system encounter an error when it tries to truncate the acl rules table. Please kindly check your database permission and try again.';
			$status = false;
		}

		//update acl rules
		if(!updateACLRules())
		{
			$message[] = 'Fatal Error : The system encounter an error when it tries to create the ACL rules. Please kindly check your database permission and try again.';
			$status = false;
		}
		else
		{
			//update user group acl rules
			if(!updateGroupACLRules())
			{
				$message[] = 'Fatal Error : The system encounter an error when it tries to create the user groups ACL rules. Please kindly check your database permission and try again.';
				$status = false;
			}
		}

		//install default plugin.
		if(! installDefaultPlugin($sourcePath))
		{
			$message[] = 'Warning : The system encounter an error when it tries to install the user plugin. Please kindly install the plugin manually.';
		}


		if( ! copyMediaFiles( $sourcePath ) )
		{
			$message[] = 'Warning: The system could not copy files to Media folder. Please kindly check the media folder permission.';
			$status		= false;
		}

		// migrating stream records from old JS to JS 2.8
		migrateJomSocialStreamNameSpace();

		if($status)
		{
			$message[] = 'Success : Installation Completed. Thank you for choosing Easy Blog.';
		}

		$this->message	= $message;
		$this->status	= $status;

		return $status;
	}

	function install($parent)
	{
		return $this->execute();
	}

	function uninstall($parent)
	{

	}

	function update($parent)
	{
		return $this->execute();
	}

	public static function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];


		return $jVersion;
	}

	function preflight($type, $parent)
    {
		//check if php version is supported before proceed with installation.
    	$phpVersion = floatval(phpversion());
    	if($phpVersion < 5 )
    	{
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage('Installation was unsuccessful because you are using an unsupported version of PHP. EasyBlog supports only PHP5 and above. Please kindly upgrade your PHP version and try again.', 'error');

			return false;
		}

    	//get source path and version number from manifest file.
		$installer	= JInstaller::getInstance();
		$manifest	= $installer->getManifest();

		$sourcePath	= $installer->getPath('source');

		if( self::getJoomlaVersion() >= '3.0' )
		{
			$this->version = (string) $manifest->attributes()->version;
		}
		else
		{
			$this->version		= $manifest->getAttribute('version');
		}

		$this->message		= array();
		$this->status		= true;
		$this->sourcePath	= $sourcePath;


		// if this is a uninstallation process, do not execute anything, just return true.
		if( $type == 'install' || $type == 'update' || $type == 'discover_install')
		{
			require_once( $this->sourcePath . DS . 'admin' . DS . 'install.defaultvalue.php' );

			//this is needed as joomla failed to remove it themselve during uninstallation or failed attempt of installation
			removeAdminMenu();
		}

		return true;
    }

    function postflight($type, $parent)
    {
    	$version	= $this->version;
		$message	= $this->message;
		$status		= $this->status;

		// fix invalid admin menu id with Joomla 1.7
		fixMenuIds();

    	//update or create menu item.
		if( menuExist() )
		{
			if(!updateMenuItems())
			{
				$message[] = 'Warning : The system encounter an error when it tries to update the menu item. Please kindly update the menu item manually.';
			}
		}
		else
		{
			if(!createMenuItems())
			{
				$message[] = 'Warning : The system encounter an error when it tries to create a menu item. Please kindly create the menu item manually.';
			}
		}

		ob_start();
		?>

		<style type="text/css">
		/**
		 * Messages
		 */

		#eblog-message {
			color: red;
			font-size:13px;
			margin-bottom: 15px;
			padding: 5px 10px 5px 35px;
		}

		#eblog-message.error {
			border-top: solid 2px #900;
			border-bottom: solid 2px #900;
			color: #900;
		}

		#eblog-message.info {
			border-top: solid 2px #06c;
			border-bottom: solid 2px #06c;
			color: #06c;
		}

		#eblog-message.warning {
			border-top: solid 2px #f90;
			border-bottom: solid 2px #f90;
			color: #c30;
		}
		</style>

		<table width="100%" border="0">
			<tr>
				<td>
					<div><img src="http://stackideas.com/images/eblog/install_success35.png" /></div>
				</td>
			</tr>
			<?php
				foreach($message as $msgString)
				{
					$msg = explode(":", $msgString);
					switch(trim($msg[0]))
					{
						case 'Fatal Error':
							$classname = 'error';
							break;
						case 'Warning':
							$classname = 'warning';
							break;
						case 'Success':
						default:
							$classname = 'info';
							break;
					}
					?>
					<tr>
						<td><div id="eblog-message" class="<?php echo $classname; ?>"><?php echo $msg[0] . ' : ' . $msg[1]; ?></div></td>
					</tr>
					<?php
				}
			?>
			<tr>
				<td><h3>Need help in starting up? Check out our <a href="http://stackideas.com/docs/easyblog/how-tos.html" target="_blank">How To</a> documentation.</h3></td>
			</tr>

		</table>
		<?php
		$html = ob_get_contents();
		@ob_end_clean();

		echo $html;

		return $status;
    }
}
