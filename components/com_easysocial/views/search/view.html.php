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

FD::import( 'site:/views/views' );

class EasySocialViewSearch extends EasySocialSiteView
{
	/**
	 * Responsible to output the search layout.
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	public function display($tpl = null)
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the current logged in user.
		$query = $this->input->get('q', '', 'default');
		$q = $query;

		// Get the search type
		$type = $this->input->get('type', '', 'cmd');

		// Load up the model
		$indexerModel = FD::model('Indexer');

		// Retrieve a list of supported types
		$allowedTypes = $indexerModel->getSupportedType();

		if (!in_array($type, $allowedTypes)) {
			$type = '';
		}

		// Options
		$data = null;
		$types = null;
		$count = 0;
		$next_limit = '';
		$limit = FD::themes()->getConfig()->get('search_limit');

		// Get the search model
		$model = FD::model('Search');
		$searchAdapter = FD::get('Search');

		// Determines if finder is enabled
		$isFinderEnabled = JComponentHelper::isEnabled('com_finder');

		if (!empty($query) && $isFinderEnabled) {

			jimport('joomla.application.component.model');

			$lib = JPATH_ROOT . '/components/com_finder/models/search.php';

			require_once($lib);

			if ($type) {
				JRequest::setVar('t', $type);
			}

			// Load up finder's model
			$finderModel = new FinderModelSearch();
			$state = $finderModel->getState();

			// Get the query
			// this line need to be here. so that the indexer can get the correct value
			$query = $finderModel->getQuery();

			// When there is no terms match, check if smart search suggested any terms or not. if yes, lets use it.
			if (!$query->terms) {
				
				if (isset($query->included) && count($query->included) > 0) {
					$suggestion = '';

					foreach($query->included as $item) {
						if (isset($item->suggestion) && !empty($item->suggestion)) {
							$suggestion = $item->suggestion;
						}
					}

					if ($suggestion) {
						$app = JFactory::getApplication();
						$input = $app->input;
						$input->request->set('q', $suggestion);

						// Load up the new model
						$finderModel = new FinderModelSearch();
						$state = $finderModel->getState();

						// this line need to be here. so that the indexer can get the correct value
						$query = $finderModel->getQuery();
					}
				}
			}

			//reset the pagination state.
			$state->{'list.start'} 	= 0;
			$state->{'list.limit'} 	= $limit;

			$results = $finderModel->getResults();
			$count = $finderModel->getTotal();
			$pagination = $finderModel->getPagination();

			if ($results) {
				$data = $searchAdapter->format($results, $query);

				$query = $finderModel->getQuery();

				if (FD::isJoomla30()) {
					$pagination->{'pages.total'} = $pagination->pagesTotal;
				}

				if ($pagination->{'pages.total'} == 1) {
					$next_limit = '-1';
				} else {
					$next_limit = $pagination->limitstart + $pagination->limit;
				}
			}

			// @badge: search.create
			// Assign badge for the person that initiated the friend request.
			$badge 	= FD::badges();
			$badge->log( 'com_easysocial' , 'search.create' , $this->my->id , JText::_( 'COM_EASYSOCIAL_SEARCH_BADGE_SEARCHED_ITEM' ) );

			// get types
			$types	= $searchAdapter->getTaxonomyTypes();
		}

		// Set the page title
		FD::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_SEARCH'));

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_SEARCH'));

		$this->set('types', $types);
		$this->set('data', $data);
		$this->set('query', $q);
		$this->set('total', $count);
		$this->set('totalcount', $count);
		$this->set('next_limit', $next_limit);


		echo parent::display('site/search/default');
	}

	/**
	 * Displays the advanced search form
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function advanced( $tpl = null )
	{
		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Set the page title
		FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH' ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_ADVANCED_SEARCH' ) );

		// Get current logged in user.
		$my 			= FD::user();

		// What is this? - this is advanced search filter id.
		$fid 			= JRequest::getInt( 'fid', 0 );

		// Get filters
		$model 		= FD::model( 'Search' );
		$filters 	= $model->getFilters( $my->id );

		// Load up advanced search library
		$library 	= FD::get( 'AdvancedSearch' );

		// default values
		// Get values from posted data
		$match 		= JRequest::getVar( 'matchType', 'all' );
		$avatarOnly	= JRequest::getInt( 'avatarOnly', 0 );

		// Get values from posted data
		$values 				= array();
		$values[ 'criterias' ] 	= JRequest::getVar( 'criterias' );
		$values[ 'datakeys' ] 	= JRequest::getVar( 'datakeys' );
		$values[ 'operators' ] 	= JRequest::getVar( 'operators' );
		$values[ 'conditions' ] = JRequest::getVar( 'conditions' );
		$values[ 'match' ] 		= $match;
		$values[ 'avatarOnly' ] = $avatarOnly;

		// echo '<pre>';print_r( $values );echo '</pre>';exit;


		// Default values
		$results 		= null;
		$total 			= 0;
		$nextlimit 		= null;
		$criteriaHTML 	= '';


		if( $fid && empty( $values[ 'criterias' ] ) )
		{
			// we need to load the data from db and do the search based on the saved filter.
			$filter = FD::table( 'SearchFilter' );
			$filter->load( $fid );

			// data saved as json format. so we need to decode it.
			$dataFilter = FD::json()->decode( $filter->filter );

			// override with the one from db.
			$values['criterias'] 		= isset( $dataFilter->{'criterias[]'} ) ? $dataFilter->{'criterias[]'} : '';
			$values['datakeys'] 		= isset( $dataFilter->{'datakeys[]'} ) ? $dataFilter->{'datakeys[]'} : '';
			$values['operators'] 		= isset( $dataFilter->{'operators[]'} ) ? $dataFilter->{'operators[]'} : '';
			$values['conditions'] 		= isset( $dataFilter->{'conditions[]'} ) ? $dataFilter->{'conditions[]'} : '';

			// we need check if the item passed in is array or not. if not, make it an array.
			if( ! is_array( $values['criterias'] ) )
			{
				$values['criterias'] = array( $values['criterias'] );
			}

			if( ! is_array( $values['datakeys'] ) )
			{
				$values['datakeys'] = array( $values['datakeys'] );
			}

			if( ! is_array( $values['operators'] ) )
			{
				$values['operators'] = array( $values['operators'] );
			}

			if( ! is_array( $values['conditions'] ) )
			{
				$values['conditions'] = array( $values['conditions'] );
			}


			$values['match'] 			= isset( $dataFilter->matchType ) ? $dataFilter->matchType : 'all';
			$values['avatarOnly']		= isset( $dataFilter->avatarOnly ) ? true : false;

			$match 		= $values['match'];
			$avatarOnly	= $values['avatarOnly'];

		}

		$displayOptions = array();

		// If there are criterias, we know the user is making a post request to search
		if( $values[ 'criterias' ] )
		{
			$results	= $library->search( $values );
			$displayOptions = $library->getDisplayOptions();
			$total 		= $library->getTotal();
			$nextlimit 	= $library->getNextLimit();
		}

		// Get search criteria output
		$criteriaHTML	= $library->getCriteriaHTML( array() , $values );

		if (! $criteriaHTML) {
			$criteriaHTML	= $library->getCriteriaHTML( array() );
		}

		$this->set( 'criteriaHTML'	, $criteriaHTML );
		$this->set( 'match'			, $match );
		$this->set( 'avatarOnly'	, $avatarOnly );
		$this->set( 'results'		, $results );
		$this->set( 'total'			, $total );
		$this->set( 'nextlimit'		, $nextlimit );
		$this->set( 'filters'		, $filters);
		$this->set( 'fid'			, $fid );
		$this->set( 'displayOptions', $displayOptions );

		echo parent::display( 'site/advancedsearch/user/default' );
	}
}
