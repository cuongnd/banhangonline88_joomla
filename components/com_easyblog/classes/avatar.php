<?php
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

class EasyBlogAvatarDefault
{
	function _init()
	{
		return true;
	}

	function _getAvatar($profile)
	{
		$path  			= JPATH_ROOT . DIRECTORY_SEPARATOR . EasyImageHelper::getAvatarRelativePath() . DIRECTORY_SEPARATOR . $profile->avatar;
		$image			= EasyImageHelper::getAvatarRelativePath() . '/' . $profile->avatar;
		$mainframe		= JFactory::getApplication();
		$override		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'default_blogger.png';

		if (stristr($profile->avatar, 'http://') !== false) {
			$avatar = new stdClass();
			$avatar->link = $profile->avatar;

			return $avatar;
		}
		
		if( !JFile::exists( $path ) || empty( $profile->avatar ) )
		{
			if( JFile::exists( $override ) )
			{
				$image	= 'templates/' . $mainframe->getTemplate() . '/html/com_easyblog/assets/images/default_blogger.png';
			}
			else
			{
				$image	= 'components/com_easyblog/assets/images/default_blogger.png';
			}
		}

		$avatar 		= new stdClass();
		$avatar->link	= rtrim( JURI::root() , '/' ) . '/' . $image;

		return $avatar;
	}
}

class EasyBlogAvatarEasySocial
{
	function _init()
	{
		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		return $easysocial->exists();
	}

	function _getAvatar($profile)
	{
		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		$easysocial->init();

		$user 	= Foundry::user( $profile->id );

		$avatar = new stdClass();
		$avatar->link	= $user->getAvatar();

		return $avatar;
	}
}

class EasyBlogAvatarJomsocial
{
	function _init()
	{
		$files	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		if(!JFile::exists( $files ))
		{
			return false;
		}

		require_once( $files );

		return true;
	}

	function _getAvatar($profile)
	{
		$user   = null;
		if( is_null( $profile->id) )
		{
			$user	= CFactory::getUser(0);
		}
		else
		{
			$user	= CFactory::getUser( $profile->id );
		}

		$source	= $user->getThumbAvatar();

		$avatar = new stdClass();
		$avatar->link	= $source;

		return $avatar;
	}
}

class EasyBlogAvatarJomWall
{
	function _init()
	{
		$files 	= JPATH_ROOT . '/components/com_awdwall/helpers/user.php';

		if(!JFile::exists( $files ))
		{
			return false;
		}

		require_once( $files );

		return true;
	}

	function _getAvatar($profile)
	{
		$avatar = new stdClass();
		$avatar->link	= AwdwallHelperUser::getBigAvatar51($profile->id);;

		return $avatar;
	}

	public function _getLink()
	{
		$Itemid = AwdwallHelperUser::getComItemId();
		$link = EasyBlogRouter::_('index.php?option=com_awdwall&view=awdwall&layout=mywall&wuid='.$profile->id.'&Itemid='.$Itemid, false);
		return $link;
	}
}

class EasyBlogAvatarK2
{
	function _init()
	{
		$files	= JPATH_ROOT . '/components/com_k2/k2.php';

		if(!JFile::exists( $files ))
		{
			return false;
		}

		return true;
	}

	public function _getAvatar( $profile )
	{
		$file1 = JPATH_ROOT . '/components/com_k2/helpers/route.php';
		$file2 = JPATH_ROOT . '/components/com_k2/helpers/utilities.php';

		if( !JFile::exists($file1) || !JFile::exists($file2) )
		{
			return false;
		}

		require_once($file1);
		require_once($file2);

		$db		= EasyBlogHelper::db();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__k2_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userID' ) . '=' . $db->Quote( $profile->id );

		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( !$result || !$result->image )
		{
			return false;
		}

		$avatarLink		= JURI::root() . 'media/k2/users/' . $result->image;

		$avatar = new stdClass();
		$avatar->link 	= $avatarLink;

		return $avatar;
	}
}

class EasyBlogAvatarMightyTouch
{
	function _init()
	{
		$file 	= JPATH_ROOT . '/components/com_juser/api.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );

		return true;
	}

	public function _getAvatar( $profile )
	{
		$avatar = new stdClass();
		$avatar->link	= JSUserApi::getAvatarPath( $profile->id );

		return $avatar;
	}
}

class EasyBlogAvatarKunena
{

	function _init()
	{
		$files	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_kunena' . DIRECTORY_SEPARATOR . 'kunena.php';

		if(!JFile::exists( $files ))
		{
			return false;
		}

		return true;
	}

	function _getAvatar($profile)
	{
		$user 	= KunenaFactory::getUser( $profile->id );

		$avatar = new stdClass();
		$avatar->link	= $user->getAvatarURL('kavatar');

    	return $avatar;
	}
}

class EasyBlogAvatarCommunityBuilder
{
	function _init()
	{
		$files = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components'  . DIRECTORY_SEPARATOR .  'com_comprofiler'  . DIRECTORY_SEPARATOR .  'plugin.foundation.php';

		if(!JFile::exists( $files ))
		{
			return false;
		}

		require_once( $files );
		cbimport('cb.database');
		cbimport('cb.tables');
		cbimport('cb.tabs');

		return true;
	}

	function _getAvatar($profile)
	{
		$user = CBuser::getInstance( $profile->id );

		// @task: Apply guest avatars when necessary.
		if( !$profile->id )
		{
			$avatar 		= new stdClass();
			$avatar->link 	= selectTemplate() . 'images/avatar/tnnophoto_n.png';

			return $avatar;
		}

		if (!$user)
		{
			$user = CBuser::getInstance( null );
		}

		// Prevent CB from adding anything to the page.
		ob_start();
		$source	= $user->getField( 'avatar' , null , 'php' );
		$reset = ob_get_contents();
		ob_end_clean();
		unset( $reset );

		$source = $source[ 'avatar' ];

		//incase we view from backend. we need to remove the /administrator/ from the path.
		$source = str_replace('/administrator/','/', $source);

		$avatar = new stdClass();
		$avatar->link	= $source;

		return $avatar;
	}
}

class EasyBlogAvatarGravatar
{
	function _init()
	{
        return true;
	}

	function _getAvatar($profile)
	{
		if(!empty($profile->comment->email))
		{
			$user = $profile->comment;
		}
		else
		{
			$user = JFactory::getUser($profile->id);
		}

		$avatar = new stdClass();
		$avatar->link	= 'https://secure.gravatar.com/avatar.php?gravatar_id=' . md5($user->email)
	            		//. '&amp;default=' . urlencode(JURI::root().$profile->avatar)
						. '&amp;size=60';

		return $avatar;
	}
}

class EasyBlogAvatarPhpBB
{
	var $files;
	var $phpbbpath;
	var $phpbbuserid;

	function _init()
	{
		$config = EasyBlogHelper::getConfig();
		$this->phpbbpath = $config->get( 'layout_phpbb_path' );

		$this->files = JPATH_ROOT . DIRECTORY_SEPARATOR . $this->phpbbpath . DIRECTORY_SEPARATOR . 'config.php';

	    if(!JFile::exists( $this->files ))
		{
			return false;
		}

		return true;
	}

	function _getAvatar($profile)
	{
		$phpbbDB = $this->_getPhpbbDBO();
		$phpbbConfig = $this->_getPhpbbConfig();

		EasyBlogHelper::getJoomlaVersion() >= '3.0' ? $nameQuote = 'quoteName' : $nameQuote = 'nameQuote';

		if(empty($phpbbConfig))
		{
			return false;
		}

		$juser	= JFactory::getUser( $profile->id );

		$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('user_id').', '.$phpbbDB->{$nameQuote}('username').', '.$phpbbDB->{$nameQuote}('user_avatar').', '.$phpbbDB->{$nameQuote}('user_avatar_width').', '.$phpbbDB->{$nameQuote}('user_avatar_height').', '.$phpbbDB->{$nameQuote}('user_avatar_type').' '
				. 'FROM '.$phpbbDB->{$nameQuote}('#__users').' WHERE LOWER('.$phpbbDB->{$nameQuote}('username').') = '.$phpbbDB->quote( strtolower( $juser->username) ).' '
				. 'LIMIT 1';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObject();

		$this->phpbbuserid = empty($result->user_id)? '0' : $result->user_id;

		if(!empty($result->user_avatar))
		{

			switch($result->user_avatar_type)
			{
				case 'avatar.driver.upload':
					$subpath	= $phpbbConfig->avatar_upload_path;
					$phpEx 		= JFile::getExt(__FILE__);
					$source		= JURI::root().$this->phpbbpath.'/download/file.'.$phpEx.'?avatar='.$result->user_avatar;
					break;
				case 'avatar.driver.remote':
					$source		= $result->user_avatar;
					break;
				case 'avatar.driver.local':
					$subpath	= $phpbbConfig->avatar_gallery_path;
					$source		= JURI::root().$this->phpbbpath.'/'.$subpath.'/'.$result->user_avatar;
					break;
				default:
					$subpath = '';
			}
		}
		else
		{
			$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('style_name').' '
					. 'FROM '.$phpbbDB->{$nameQuote}('#__styles').' '
					. 'WHERE '.$phpbbDB->{$nameQuote}('style_id').' = '.$phpbbDB->quote($phpbbConfig->default_style);
			$phpbbDB->setQuery($sql);
			$theme = $phpbbDB->loadObject();

			$defaultPath	= $this->phpbbpath.'/styles/'.$theme->theme_name.'/theme/images/no_avatar.gif';
			$source			= JURI::root().$defaultPath;
		}

		$avatar = new stdClass();
		$avatar->link	= $source;

		return $avatar;
	}

	function _getPhpbbDBO()
	{
		static $phpbbDB = null;

		if($phpbbDB == null)
		{
			require( $this->files );

			$host		= $dbhost;
			$user		= $dbuser;
			$password	= $dbpasswd;
			$database	= $dbname;
			$prefix		= $table_prefix;
			$driver		= $dbms;
			$debug		= 0;

			$options = array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

			$phpbbDB = JDatabase::getInstance( $options );
		}

		return $phpbbDB;
	}

	function _getPhpbbConfig()
	{
		$phpbbDB = $this->_getPhpbbDBO();

		EasyBlogHelper::getJoomlaVersion() >= '3.0' ? $nameQuote = 'quoteName' : $nameQuote = 'nameQuote';

		$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('config_name').', '.$phpbbDB->{$nameQuote}('config_value').' '
				. 'FROM '.$phpbbDB->{$nameQuote}('#__config') . ' '
				. 'WHERE '.$phpbbDB->{$nameQuote}('config_name').' IN ('.$phpbbDB->quote('avatar_gallery_path').', '.$phpbbDB->quote('avatar_path').', '.$phpbbDB->quote('default_style').')';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObjectList();

		if(empty($result))
		{
			return false;
		}

		$phpbbConfig = new stdClass();
        $phpbbConfig->avatar_gallery_path	= null;
        $phpbbConfig->avatar_upload_path	= null;
		$phpbbConfig->default_style			= 1;

		foreach($result as $row)
		{
			switch($row->config_name)
			{
				case 'avatar_gallery_path':
					$phpbbConfig->avatar_gallery_path = $row->config_value;
					break;
				case 'avatar_path':
					$phpbbConfig->avatar_upload_path = $row->config_value;
					break;
				case 'default_style':
					$phpbbConfig->default_style = $row->config_value;
					break;
			}
		}

		return $phpbbConfig;
	}
}

class EasyBlogAvatarAnahita
{
	function _init()
	{
		if( !class_exists( 'KFactory' ) )
		{
			return false;
		}

		return true;
	}

	function _getAvatar( $profile )
	{

		$person	= KFactory::get( 'lib.anahita.se.person.helper' )->getPerson( $profile->id );

		$avatar			= new stdClass();
		$avatar->link	= $person->getAvatar()->getURL( AnSeAvatar::SIZE_MEDIUM );

		return $avatar;
	}
}

class EasyBlogAvatarTuiyo
{
	function _init()
	{
		return false;
	}

	function _getAvatar()
	{

	}
}

class EasyBlogAvatarEasydiscuss
{
	function _init()
	{
		$files	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_easydiscuss' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

		if(!JFile::exists( $files ))
		{
			return false;
		}

		require_once( $files );

		return true;
	}

	function _getAvatar($profile)
	{
		$EDProfile = DiscussHelper::getTable( 'Profile' );
		$EDProfile->load( $profile->id );

		$avatar 		= new stdClass();
		$avatar->link	= $EDProfile->getAvatar();

		return $avatar;
	}
}
