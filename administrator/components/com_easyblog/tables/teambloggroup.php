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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyBlogTableTeamBlogGroup extends EasyBlogTable
{
	var $team_id	= null;
	var $group_id	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_team_groups' , 'id' , $db );
	}

	function exists()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'WHERE `team_id`=' . $db->Quote( $this->team_id ) . ' '
				. 'AND `group_id`=' . $db->Quote( $this->group_id );
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}
}
