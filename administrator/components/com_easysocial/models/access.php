<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

/**
 * Access Control model.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class EasySocialModelAccess extends EasySocialModel
{
	private $data			= null;

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct( 'access' );
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination( $this->total , $this->getState('limitstart') , $this->getState('limit') );
		}

		return $this->pagination;
	}

	/**
	 * Given the access uid and type, load the params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The access uid
	 * @param	string	The access type
	 * @return	string	The raw params
	 */
	public function getParams( $uid , $type )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_access' );
		$sql->column( 'params' );
		$sql->where( 'uid' , $uid );
		$sql->where( 'type' , $type );

		$db->setQuery( $sql );
		$params	= $db->loadResult();

		return $params;
	}

	/**
	 * Renders the access form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getForm( $uid , $type = SOCIAL_TYPE_USERGROUP , $prefix = '' , $activeTab = '' , $processActiveTab = true )
	{
		$model = FD::model('accessrules');

		// Convert the type to group type
		$group = SOCIAL_TYPE_USER;
		switch ($type)
		{
			case SOCIAL_TYPE_USER:
			case SOCIAL_TYPE_PROFILES:
				$group = SOCIAL_TYPE_USER;
			break;

			case SOCIAL_TYPE_GROUP:
			case SOCIAL_TYPE_CLUSTERS:
				$group = SOCIAL_TYPE_GROUP;
			break;
		}

		$rules = $model->getAllRules(array(
			'group' => $group,
			'state' => SOCIAL_STATE_PUBLISHED
		));

		if (empty($rules))
		{
			return '';
		}

		$forms 	= array();

		// Group rules by element
		foreach ($rules as $rule)
		{
			if (!isset($forms[$rule->element]))
			{
				$forms[$rule->element] = (object) array(
					'title' => JText::_('COM_EASYSOCIAL_ACCESS_TAB_' . strtoupper($rule->element)),
					'desc' => JText::_('COM_EASYSOCIAL_ACCESS_TAB_' . strtoupper($rule->element) . '_DESC'),
					'fields' => array()
				);
			}

			// Init some data to comply with FD::form();

			$rule->label = $rule->title;
			$rule->tooltip = $rule->description;

			if (empty($rule->type))
			{
				$rule->type = 'boolean';
			}

			if (!isset($rule->default))
			{
				$rule->default = true;
			}

			$forms[$rule->element]->fields[] = $rule;
		}

		$form 	= FD::form();
		$form->load($forms);

		$registry = FD::registry();

		// Get the access data.
		$access	= FD::table('Access');
		$state = $access->load(array('uid' => $uid, 'type' => $type));

		if ($state)
		{
			$registry = FD::registry($access->params);
		}

		$form->bind($registry);

		$output	= $form->render( true, true, $activeTab, $prefix , $processActiveTab );

		return $output;
	}
}
