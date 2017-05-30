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

class EasySocialModGroupsHelper
{
	public static function getGroups( &$params )
	{
		$model 	= FD::model( 'Groups' );

		// Determine filter type
		$filter 	= $params->get( 'filter' );

		// Determine the ordering of the groups
		$ordering 	= $params->get( 'ordering' , 'latest' );

		// Default options
		$options 	= array();

		// Limit the number of groups based on the params
		$options[ 'limit' ]		= $params->get( 'display_limit' , 5 );
		$options[ 'ordering' ]	= $ordering;
		$options['state']		= SOCIAL_STATE_PUBLISHED;

		if ($filter == 0) {
			$groups 	= $model->getGroups( $options );
		}

		if( $filter == 1 )
		{
			$category 	= trim( $params->get( 'category' ) );

			if( empty( $category ) )
			{
				return array();
			}

			// Since category id's are stored as ID:alias, we only want the id portion
			$category 	= explode( ':' , $category );

			$options[ 'category' ]	= $category[0];

			$groups 				= $model->getGroups( $options );
		}

		// Featured modules only
		if( $filter == 2 )
		{
			$options[ 'featured' ]	= true;

			$groups 	= $model->getGroups( $options );
		}

		if( !$groups )
		{
			return $groups;
		}

		return $groups;
	}
}
