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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerUsers extends EasySocialController
{


	/**
	 * Retrieves a list of users by sitewide search filter
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUsersByProfileFilter()
	{
		// Check for request forgeries
		FD::checkToken();

		$config = FD::config();
		$view = $this->getCurrentView();

		// Get the profile id
		$id = $this->input->get('id', 0, 'int');

		// fields filtering data
		$data = $this->input->getVar('data', null);


		$values 				= array();
		if (! is_null($data) && $data) {

			// data saved as json format. so we need to decode it.
			$dataFilter = FD::json()->decode( $data );

			$values['criterias'] 		= $dataFilter->{'criterias[]'};
			$values['datakeys'] 		= $dataFilter->{'datakeys[]'};
			$values['operators'] 		= $dataFilter->{'operators[]'};
			$values['conditions'] 		= $dataFilter->{'conditions[]'};
		} else {

			$values[ 'criterias' ] 	= $this->input->getVar( 'criterias' );
			$values[ 'datakeys' ] 	= $this->input->getVar( 'datakeys' );
			$values[ 'operators' ] 	= $this->input->getVar( 'operators' );
			$values[ 'conditions' ] = $this->input->getVar( 'conditions' );
		}

		$profile = FD::table('Profile');
		$profile->load($id);

		$options = array();

		$admin 		= $config->get( 'users.listings.admin' ) ? true : false;
		$options	= array('includeAdmin' => $admin );

		// setup the limit
		$limit 		= FD::themes()->getConfig()->get('userslimit');
		$options['limit']	= $limit;

		$searchOptions = array();

		// lets do some clean up here.
		for($i = 0; $i < count($values[ 'criterias' ]); $i++ ) {
			$criteria = $values[ 'criterias' ][$i];
			$condition = $values[ 'conditions' ][$i];
			$datakey = $values[ 'datakeys' ][$i];
			$operator = $values[ 'operators' ][$i];


			if (trim($condition)) {
				$searchOptions['criterias'][] = $criteria;
				$searchOptions['datakeys'][] = $datakey;
				$searchOptions['operators'][] = $operator;

				$field  = explode( '|', $criteria );

				$fieldCode 	= $field[0];
				$fieldType 	= $field[1];

				if ($fieldType == 'birthday') {
					// currently the value from form is in age format. we need to convert it into date time.
					$ages  = explode( '|', $condition );

					if (! isset($ages[1])) {
						// this happen when start has value and end has no value
						$ages[1] = $ages[0];
					}

					if ($ages[1] && !$ages[0]) {
						//this happen when start is empty and end has value
						$ages[0] = $ages[1];
					}

					$startdate = '';
					$enddate = '';

					$currentTimeStamp = FD::date()->toUnix();

					if ($ages[0] == $ages[1]) {
						$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

						$year = FD::date($start)->toFormat('Y');
						$startdate = $year . '-01-01 00:00:01';
						$enddate = FD::date($start)->toFormat('Y-m-d') . ' 23:59:59';
					} else {

						if ($ages[0]) {
							$start = strtotime('-' . $ages[0] . ' years', $currentTimeStamp);

							$year = FD::date($start)->toFormat('Y');
							$enddate = $year . '-12-31 23:59:59';
						}

						if ($ages[1]) {
							$end = strtotime('-' . $ages[1] . ' years', $currentTimeStamp);

							$year = FD::date($end)->toFormat('Y');
							$startdate = $year . '-01-01 00:00:01';
						}
					}

					$condition = $startdate . '|' . $enddate;
				}

				$searchOptions['conditions'][] = $condition;
			}

		}


		$searchOptions[ 'match' ] = 'and';
		$searchOptions[ 'avatarOnly' ] = false;
		if( $id ) {
			$searchOptions[ 'profile' ] = $id;
		}

		// Retrieve the users
		$model = FD::model('Users');
		$pagination  = null;

		$result = $model->getUsersByFilter('0', $options, $searchOptions);
		$pagination	= $model->getPagination();


		// Define those query strings here
		$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
		$pagination->setVar( 'view'		, 'users' );
		$pagination->setVar( 'filter' , 'profiletype' );
		$pagination->setVar( 'id' , $profile->id );

		for($i = 0; $i < count($values[ 'criterias' ]); $i++ ) {

			$criteria = $values[ 'criterias' ][$i];
			$condition = $values[ 'conditions' ][$i];
			$datakey = $values[ 'datakeys' ][$i];
			$operator = $values[ 'operators' ][$i];

			$pagination->setVar( 'criterias['.$i.']' , $criteria );
			$pagination->setVar( 'datakeys['.$i.']' , $datakey );
			$pagination->setVar( 'operators['.$i.']' , $operator );
			$pagination->setVar( 'conditions['.$i.']' , $condition );
		}

		$users 		= array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[]	= FD::user( $obj->id );
		}

		if( $arrIds )
		{
			FD::user( $arrIds );
		}

		foreach( $result as $obj )
		{
			$users[]	= FD::user( $obj->id );
		}

		return $view->call(__FUNCTION__, $users, $profile, $data, $pagination);
	}

	function get_google_plus_login(){
		########## Google Settings.Client ID, Client Secret from https://console.developers.google.com #############
		require_once JPATH_ROOT.DS.'libraries/google-api-php-client-master/src/Google/autoload.php';
		$client_id = '256006136278-rs9r009iunikdtmcmbifamhdo9b6vei4.apps.googleusercontent.com';
		$client_secret = 'Tk5gGQyODcU8GDHGjdIYIHJ7';

		###################################################################

		$client = new Google_Client();
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->addScope("email");
		$client->addScope("profile");
		$service = new Google_Service_Oauth2($client);
		$redirectUri=JUri::root().'index.php?option=com_easysocial&ctrl=users&task=create_account_vendor_current_user_login_by_google';
		$client->setRedirectUri($redirectUri);
		$auth_url = $client->createAuthUrl();
		$response=new stdClass();
		$response->auth_url=$auth_url;
		echo json_encode($response);
		die;

	}

	function create_account_vendor_current_user_login_by_google()
	{
		echo "sfsdfsd";
		die;
		require_once JPATH_ROOT.DS.'libraries/google-api-php-client-master/src/Google/autoload.php';
		$client_id = '256006136278-rs9r009iunikdtmcmbifamhdo9b6vei4.apps.googleusercontent.com';
		$client_secret = 'Tk5gGQyODcU8GDHGjdIYIHJ7';

		###################################################################

		$client = new Google_Client();
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->addScope("email");
		$client->addScope("profile");
		$service = new Google_Service_Oauth2($client);
		$redirectUri=JUri::root().'index.php?option=com_hikamarket&ctrl=vendor&task=create_account_vendor_current_user_login_by_google';
		$client->setRedirectUri($redirectUri);

		$app = JFactory::getApplication();
		$input = $app->input;
		$code = $input->getString('code');
		$client->authenticate($code);
		$access_token = $client->getAccessToken();
		$client->setAccessToken($access_token);
		/*        $message= JText::sprintf('FACEBOOK_GRAPH_RETURNED_AN_ERROR',$e->getMessage());
                $app->redirect(JUri::root().JRoute::_('index.php?option=com_hikamarket&ctrl=vendor&task=cpanel'),$message);*/

		$googlePlus = new Google_Service_Plus($client);
		$userProfile = $googlePlus->people->get('me');
		$google_email = reset($userProfile->getEmails())->getValue();
		$user_by_email = JUserHelper::get_user_by_email($google_email);
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$new_user_system=0;
		if ($user_by_email) {
			$user = JFactory::getUser($user_by_email->id);
		} else {

			$temp = new stdClass();
			$temp->id = 0;
			$temp->useractivation = 0;
			$temp->email1 = $google_email;
			$temp->username = $google_email;
			$temp->name = $userProfile->getName()->getGivenName();
			$temp->password1 = JUserHelper::genRandomPassword();
			$new_user_system=1;
			// Finish the registration.
			$data = (array)$temp;
			JModelLegacy::addIncludePath(JPATH_ROOT.DS.'components/com_users/models');
			$model_registration = JModelLegacy::getInstance('Registration', 'UsersModel');
			$params = JComponentHelper::getParams('com_users');
			$params->set('useractivation', 0);
			$return = $model_registration->ajax_register($data, $params);

			// Check for errors.
			if ($return === false) {
				// Save the data in the session.
				$app->setUserState('users.registration.form.data', $data);

				// Redirect back to the registration form.
				$message = JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model_registration->getError());
				die($message);
			}
			$user = JUserHelper::get_user_by_email($google_email);
			$user = JFactory::getUser($user->id);
		}
		$session->set('user', $user);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select('vendor_id')
			->from('#__hikamarket_vendor')
			->where('vendor_email=' . $query->q($user->email));
		$vendor_id = $db->setQuery($query)->loadResult();
		self::go_to_cpanel($new_user_system,$vendor_id,$user);
		return;

	}

	/**
	 * Retrieves a list of users by sitewide search filter
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUsersByFilter()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the profile id
		$fid = $this->input->get('id', 0, 'int');
		$sort   = $this->input->get('sort', 'latest', 'word');

		$filter = FD::table('SearchFilter');
		$filter->load($fid);

		// Get the current view
		$view = $this->getCurrentView();

		$model 		= FD::model('Users');

		$options = array();

		// setup the limit
		$limit 		= FD::themes()->getConfig()->get('userslimit');
		$options['limit']	= $limit;

		$result		= $model->getUsersByFilter( $fid, $options );
		$pagination  = null;

		$pagination	= $model->getPagination();

		// Define those query strings here
		$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
		$pagination->setVar( 'view'		, 'users' );
		$pagination->setVar( 'filter' , 'search' );
		$pagination->setVar( 'id' , $fid );


		$users 		= array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[]	= FD::user( $obj->id );
		}

		if( $arrIds )
		{
			FD::user( $arrIds );
		}

		foreach( $result as $obj )
		{
			$users[]	= FD::user( $obj->id );
		}


		return $view->call(__FUNCTION__, $users, $filter, $pagination);
	}



	/**
	 * Retrieves a list of users by specific profile
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUsersByProfile()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the profile id
		$id = $this->input->get('id', 0, 'int');
		$sort   = $this->input->get('sort', 'latest', 'word');


		$profile = FD::table('Profile');
		$profile->load($id);

		// Get the current view
		$view = $this->getCurrentView();

		$model 		= FD::model('Users');
		$options	= array('profile' => $id);

		if ($sort == 'alphabetical') {
			$options[ 'ordering' ]	= 'a.name';
			$options[ 'direction' ]	= 'ASC';
		} elseif($sort == 'latest') {
			$options[ 'ordering' ]	= 'a.id';
			$options[ 'direction' ]	= 'DESC';
		}

		// setup the limit
		$limit 		= FD::themes()->getConfig()->get('userslimit');
		$options['limit']	= $limit;

		// we only want published user.
		$options[ 'published' ]	= 1;

		// exclude users who blocked the current logged in user.
		$options['excludeblocked'] = 1;

		$config 	= FD::config();
		$options['includeAdmin'] = $config->get( 'users.listings.admin' ) ? true : false;

		// $model = FD::model('Profiles');
		// $users = $model->getMembers($id, $options);

		$result		= $model->getUsers( $options );
		$pagination  = null;


		$pagination	= $model->getPagination();

		// Define those query strings here
		$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
		$pagination->setVar( 'view'		, 'users' );
		$pagination->setVar( 'filter' , 'profiletype' );
		$pagination->setVar( 'id' , $id );


		$users 		= array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[]	= FD::user( $obj->id );
		}

		if( $arrIds )
		{
			FD::user( $arrIds );
		}

		foreach( $result as $obj )
		{
			$users[]	= FD::user( $obj->id );
		}


		return $view->call(__FUNCTION__, $users, $profile, $pagination);
	}

	/**
	 * Retrieves the list of users on the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getUsers()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();
		$my 	= FD::user();

		// Get the current filter
		$filter = $this->input->get('filter', 'all', 'word');

		// Get the current sorting
		$sort   = $this->input->get('sort', 'latest', 'word');
		$isSort = $this->input->get('isSort', false, 'bool');
		$showPagination = $this->input->get('showpagination', 0, 'default');

		$model 		= FD::model('Users');
		$options	= array('exclusion' => $my->id);

		if ($sort == 'alphabetical') {
			$options[ 'ordering' ]	= 'a.name';
			$options[ 'direction' ]	= 'ASC';
		} elseif($sort == 'latest') {
			$options[ 'ordering' ]	= 'a.id';
			$options[ 'direction' ]	= 'DESC';
		}

		if ($filter == 'online') {
			$options[ 'login' ]	= true;
		}

		if ($filter == 'photos') {
			$options[ 'picture' ]	= true;
		}

		// setup the limit
		$limit 		= FD::themes()->getConfig()->get('userslimit');
		$options['limit']	= $limit;

		// Determine if we should display admins
		$config 	= FD::config();
		$admin 		= $config->get( 'users.listings.admin' ) ? true : false;

		$options[ 'includeAdmin' ]	= $admin;

		// we only want published user.
		$options[ 'published' ]	= 1;

		// exclude users who blocked the current logged in user.
		$options['excludeblocked'] = 1;

		$result		= $model->getUsers( $options );
		$pagination  = null;

		if ($showPagination) {
			$pagination	= $model->getPagination();

			// Define those query strings here
			$pagination->setVar( 'Itemid'	, FRoute::getItemId( 'users' ) );
			$pagination->setVar( 'view'		, 'users' );
			$pagination->setVar( 'filter' , $filter );
			$pagination->setVar( 'sort' , $sort );
		}

		$users 		= array();

		// preload users.
		$arrIds = array();

		foreach ($result as $obj) {
			$arrIds[]	= FD::user( $obj->id );
		}

		if( $arrIds )
		{
			FD::user( $arrIds );
		}

		foreach( $result as $obj )
		{
			$users[]	= FD::user( $obj->id );
		}

		return $view->call( __FUNCTION__ , $users , $isSort, $pagination );
	}
}
