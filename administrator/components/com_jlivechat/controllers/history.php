<?php
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
  or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
  License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JLiveChatControllerHistory extends CMSController 
{
	/**
	 * Display the view
	 */
	function display()
	{
		$mainframe = & JFactory::getApplication();

		if(!function_exists('curl_init')) $mainframe->enqueueMessage(JText::_('CURL_NOT_INSTALLED'), 'error');

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		$viewName = JRequest::getCmd('view', 'history');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Get/Create the model
		$model = & $this->getModel('HistoryAdmin', 'JLiveChatModel');
		
		// Push the model into the view (as default)
		if($model) $view->setModel($model, true);

		// Set the layout
		$view->setLayout('default');

		// Display the view
		parent::display(false);
	}

	function display_chat_session()
	{
		$mainframe = & JFactory::getApplication();

		$viewName = JRequest::getCmd('view', 'history');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Get/Create the model
		$model = & $this->getModel('HistoryAdmin', 'JLiveChatModel');
		
		// Push the model into the view (as default)
		if($model) $view->setModel($model, true);

		$chatSessionId = JRequest::getInt('s', 0, 'method');

		// Set the layout
		$view->setLayout('default');
		$view->displayChatContents($chatSessionId);
	}

	function delete_history()
	{
		$mainframe = & JFactory::getApplication();

		$model = & $this->getModel('HistoryAdmin', 'JLiveChatModel');

		$model->deleteHistory();

		$uri = & JFactory::getURI();

		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('DELETE_HISTORY_SUCCESS_MSG'));
	}

	function get_history()
	{
		$mainframe = & JFactory::getApplication();

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-type: text/x-json");

		$limitstart = JRequest::getInt('startIndex', 0, 'method');
		$page = JRequest::getInt('page', 1, 'method');
		$resultsPerPage = JRequest::getInt('rp', 50, 'method');
		$sortname = JRequest::getVar('sort', 'cdate', 'method');
		$sortorder = JRequest::getVar('dir', 'desc', 'method');

		$history = & CMSModel::getInstance('HistoryAdmin', 'JLiveChatModel');

		$history->setSort($sortname, $sortorder);

		$history->setState('limit', $resultsPerPage);
		$history->setState('limitstart', $limitstart);

		$records = $history->getData();
		$pagination = $history->getPagination();

		if(!empty($records))
		{
			require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'jlcdateadmin.php';

			foreach($records as $a => $row)
			{
				$cdateObj = new JLiveChatModelJLCDateAdmin($row['cdate']);
				$records[$a]['cdate'] = $cdateObj->toFormat('%m/%d/%Y %H:%M:%S');

				$records[$a]['options'] = '<a id="view_contents_btn'.$row['chat_session_id'].'" href="index.php?option=com_jlivechat&view=history&task=display_chat_session&tmpl=component&format=raw&s='.$row['chat_session_id'].'" target="_blank">'.JText::_('VIEW_CONTENTS').'</a>';

				$records[$a]['options'] .= '<script type="text/javascript">
												var viewContentsBtn'.$row['chat_session_id'].' = new YAHOO.widget.Button("view_contents_btn'.$row['chat_session_id'].'");
											</script>';
			}
		}

		$output = array(
			'recordsReturned' => $pagination->total,
			'totalRecords' => $pagination->total,
			'startIndex' => $pagination->limitstart,
			'sort' => $sortname,
			'dir' => $sortorder,
			'pageSize' => $resultsPerPage,
			'totalResultsAvailable' => $pagination->total,
			'totalResultsReturned' => $pagination->limit,
			'firstResultPosition' => $pagination->limitstart,
			'Records' => $records
		);

		echo json_encode($output);

		jexit();
	}
}