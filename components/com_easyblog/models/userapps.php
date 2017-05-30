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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'model.php' );
jimport('joomla.utilities.date');

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class EasyBlogModelUserApps extends EasyBlogModel
{
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function getUserApps($appName = '')	
	{
		$db	= EasyBlogHelper::db();
		$query	= 'SELECT * FROM `#__easyblog_apps`';
		if(! empty($appName))
			$query	.= ' WHERE `appname` = ' . $db->Quote($appName);
			
		$db->setQuery($query);
		
		$result	= $db->loadObjectList();
		return $result;			
	
	}
	
	
	/**
	 * Get user apps params.
	 * return null if user apps not found. return an object if found.	 
	 */	 	
	
	function getUserAppsParams($appsId, $userId, $params=null)
	{
		$db	= EasyBlogHelper::db();
		
		$query	= 'SELECT b.*'
				. 'FROM ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_userapps') . ' AS a, ' 
							. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_params') . ' AS b '
				. 'WHERE ' 	. 'a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' = ' . 'b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('refer_id') . ' AND '
							. 'a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('app_id') . ' = ' . $db->quote($appsId) . ' AND '
							. 'a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('user_id') . ' = ' . $db->quote($userId) . ' AND '
							. 'b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_type') . ' = ' .	$db->quote('userapp');
							
		$db->setQuery($query);
		
		$result	= $db->loadObjectList();
				
		$obj	= null;
		
		if(! empty($result))
		{
			$obj	= new StdClass();												
			foreach($result as $row)
			{
				$key	= $row->param_name;
		
				$objAttr	= new StdClass();		
				$objAttr->id		= $row->id;
				$objAttr->referId	= $row->refer_id;				
				$objAttr->datatype	= $row->param_value_type;
				$objAttr->type		= $row->param_type;
				$objAttr->published	= $row->published;
				$objAttr->ordering	= $row->ordering;
				$objAttr->value		= $row->param_value;				
				
				$obj->$key		= $objAttr;								
								
			}
		}
		
		return $obj;
	}
	
		
	
	function saveUserAppsParams($mode = '', $userId, $appId, $referId = 0, $param = null)
	{
		$db		= EasyBlogHelper::db();
		$result	= true;
	
		if(! empty($mode))
		{
			$command	= array();
			$todayDate	= new JDate();
			
			if( $mode == 'insert' ) //new user params
			{				
				
				$inserted	= false;				
			
				$uApps	= EasyBlogHelper::getTable( 'UserApps' , 'Table' );
				$uApps->app_id		= $appId;
				$uApps->user_id		= $userId;
				$uApps->created 	= $todayDate->toMySql();
				$uApps->modified	= $todayDate->toMySql();
				$uApps->published	= true;
				if($uApps->store()) $inserted = true;
				
				if($inserted)
				{					
					$tmpId	= $uApps->id;					
					foreach ($param as $key => $val)
					{
						$query	= 'INSERT INTO ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_params') . ' '
								. ' ( ' 
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('refer_id') . ', '
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_name') . ', '
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_value') .', '
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_value_type') . ', '
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_type') . ', '
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created') . ', ' 
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('modified') . ', '
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('published') . ', ' 
									. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('ordering') . ' '
								. ' ) '	
								. 'VALUES ' 
								. ' ( '
									. $db->quote($tmpId) . ', '
									. $db->quote($key) . ', '
									. $db->quote($val) .', '
									. $db->quote('string') . ', '
									. $db->quote('userapp') . ', '
									. $db->quote($todayDate->toMySql()) .', '
									. $db->quote($todayDate->toMySql()) .', '
									. $db->quote('1') . ', ' 
									. $db->quote('1') . ' '
								. ' ); ';
						$command[]	= $query;
					}
				}
			}
			else //updating
			{
				foreach ($param as $key => $val)
				{
					$query	= 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_params') . ' '
							. 'SET '	
								. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_value') .' = ' . $db->quote($val) . ', '
								. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('modified') . ' = ' . $db->Quote($todayDate->toMySql()) . ' '
							. 'WHERE '
								. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('refer_id') . ' = ' . $db->quote($referId) . ' AND '
								. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_name') . ' = ' . $db->quote($key) . ' AND '
								. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('param_type') . ' = ' . $db->quote('userapp');
					$command[]	= $query;		
				}
			}
			
			// now we execute all the query
			if(empty($command)) $result = false;
			
			foreach ($command as $cmdQuery)
			{
				$db->setQuery($cmdQuery);
				if(! $db->Query()) {
					$result = false;
				}
				
				if($db->getErrorNum()){
					JError::raiseError( 500, $db->stderr());
				}			
			}						
			//process ended here.									
		}
		else
		{
			$result = false;
		}				
		return $result;
	}

}
