<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
class fsj_transmanController extends JControllerLegacy
{
    /**
     * Method to display the view
     *
     * @access    public
     */
	public function display($cachable = false, $urlparams = array())
	{
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = JRequest::getVar('view', $this->default_view);
		$viewLayout = JRequest::getVar('layout', 'default');
		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
		// FSJ ADDITION : Set the controller up on the view class
		$view->controller = $this;
		// Get/Create the model
		if ($model = $this->getModel($viewName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}
		$view->document = $document;
		$conf = JFactory::getConfig();
		// Display the view
		if ($cachable && $viewType != 'feed' && $conf->get('caching') >= 1)
		{
			$option = JRequest::getVar('option');
			$cache = JFactory::getCache($option, 'view');
			if (is_array($urlparams))
			{
				$app = JFactory::getApplication();
				if (!empty($app->registeredurlparams))
				{
					$registeredurlparams = $app->registeredurlparams;
				}
				else
				{
					$registeredurlparams = new stdClass;
				}
				foreach ($urlparams as $key => $value)
				{
					// Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}
				$app->registeredurlparams = $registeredurlparams;
			}
			$cache->get($view, 'display');
		}
		else
		{
			$view->display();
		}
		return $this;
	}
}
