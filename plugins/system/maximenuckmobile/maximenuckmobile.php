<?php

/**
 * @copyright	Copyright (C) 2012 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');

class plgSystemMaximenuckmobile extends JPlugin {

	function plgSystemMaximenuckmobile(&$subject, $params) {
		parent::__construct($subject, $params);
	}

	/**
	 * @param       JForm   The form to be altered.
	 * @param       array   The associated data for the form.
	 * @return      boolean
	 * @since       1.6
	 */
	function onContentPrepareForm($form, $data) {
		if (
			($form->getName() != 'com_modules.module' && $form->getName() != 'com_advancedmodules.module' || ($form->getName() == 'com_modules.module' && $data && $data->module != 'mod_maximenuck') || ($form->getName() == 'com_advancedmodules.module' && $data && $data->module != 'mod_maximenuck'))
			&& ($form->getName() != 'com_menus.item' && $form->getName() != 'com_menumanagerck.itemedition')
			)
			return;

		JForm::addFormPath(JPATH_SITE . '/plugins/system/maximenuckmobile/params');

		// get the language
		// $lang = JFactory::getLanguage();
		// $langtag = $lang->getTag(); // returns fr-FR or en-GB
		$this->loadLanguage();

		// module options
		// $app = JFactory::getApplication();
		// $plugin = JPluginHelper::getPlugin('system', 'maximenuckmobile');
		// $pluginParams = new JRegistry($plugin->params);

		// load the additional options in the module
		if ($form->getName() == 'com_modules.module' || $form->getName() == 'com_advancedmodules.module') {
			$form->loadFile('mobile_menuparams_maximenuck', false);
		}
		
		// menu item options
		if ($form->getName() == 'com_menus.item' || $form->getName() == 'com_menumanagerck.itemedition') {
			$form->loadFile('mobile_itemparams_maximenuck', false);
		}
	}

	function onAfterDispatch() {

		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$doctype = $document->getType();


		// si pas en frontend, on sort
		if ($app->isAdmin()) {
			return false;
		}

		// si pas HTML, on sort
		if ($doctype !== 'html') {
			return;
		}

		// si Internet Explorer on sort
		jimport('joomla.environment.browser');
		$browser = JBrowser::getInstance();
		$browserType = $browser->getBrowser(); // info : il existe aussi un browser version
		if ($browserType == 'msie' and $browser->getVersion() < 9) {
			return false;
		}

		// get the language
		// $lang = JFactory::getLanguage();
		$this->loadLanguage();

		JHTML::_("jquery.framework", true);
		if (!class_exists('Mobile_Detect')) {
			require_once dirname(__FILE__) . '/Mobile_Detect.php';
		}
		$document->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
		$document->addScript(JUri::base(true) . '/plugins/system/maximenuckmobile/assets/maximenuckmobile.js');
		// $document->addScriptDeclaration("var CKTEXT_PLG_MAXIMENUCK_MENU = '" . JText::_('PLG_MAXIMENUCK_MENU') . "';");
		$menuIDs = Array();
		
		foreach ($this->getMaximenuModules() as $module) {
			if (!$module->params) 
				continue;
				
			$moduleParams = new JRegistry($module->params);
			if (!$moduleParams->get('maximenumobile_enable', '0'))
				continue;

			$menuID = ( $moduleParams->get('menuid', '') != '' ) ? $moduleParams->get('menuid', '') : 'maximenuck' . $module->id;
			$resolution = $this->params->get('maximenumobile_resolution', '640');
			$container = $moduleParams->get('maximenumobile_container', 'body');
			$useimages = $moduleParams->get('maximenumobile_useimage', '0');
			$usemodules = $moduleParams->get('maximenumobile_usemodule', '0');
			$theme = $this->params->get('maximenumobile_theme', 'default');
			$document->addStyleSheet(JUri::base(true) . '/plugins/system/maximenuckmobile/themes/' . $theme . '/maximenuckmobile.css');
			$showdesc = $moduleParams->get('maximenumobile_showdesc', '0');
			$showlogo = $moduleParams->get('maximenumobile_showlogo', '1');

			// set the text for the menu bar
			switch ($moduleParams->get('maximenumobile_showmobilemenutext', '')) {
				case 'none':
					$mobilemenutext = '';
					break;
				case 'default':
				default:
					$mobilemenutext = JText::_('PLG_MAXIMENUCK_MENU');
					break;
				case 'custom':
					$mobilemenutext = $moduleParams->get('maximenumobile_mobilemenutext', '');
					break;
			}

			array_push($menuIDs, $menuID);

			$js = "jQuery(document).ready(function($){
                    $('#" . $menuID . "').MobileMaxiMenu({"
					. "usemodules : " . $usemodules . ","
					. "container : '" . $container . "',"
					. "showdesc : " . $showdesc . ","
					. "showlogo : " . $showlogo . ","
					. "useimages : " . $useimages . ","
					. "menuid : '" . $menuID . "',"
					. "showmobilemenutext : '" . $moduleParams->get('maximenumobile_showmobilemenutext', '') . "',"
					. "mobilemenutext : '" . $mobilemenutext . "',"
					. "mobilebackbuttontext : '" . JText::_('MOD_MAXIMENUCK_MOBILEBACKBUTTON') . "',"
					. "displaytype : '" . $moduleParams->get('maximenumobile_display', 'flat') . "',"
					. "displayeffect : '" . $moduleParams->get('maximenumobile_displayeffect', 'normal') . "'"
					. "});
                });";
			$document->addScriptDeclaration($js);
			
			$css = $this->getMediaQueries($resolution, $menuID, $moduleParams);
			$document->addStyleDeclaration($css);
		}
	}

	private function getMaximenuModules_old() {
		$db = JFactory::getDBO();
		$query = "
            SELECT id, params
            FROM #__modules
            WHERE published=1
            AND module='mod_maximenuck'
            ;";
		$db->setQuery($query);
		$modules = $db->loadObjectList('id');
		return $modules;
	}
	
	/**
	 * Load published modules.
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	protected function getMaximenuModules()
	{
		static $clean;

		if (isset($clean))
		{
			return $clean;
		}

		$app = JFactory::getApplication();
		$Itemid = $app->input->getInt('Itemid');
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid')
			->from('#__modules AS m')
			->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id')
			->where('m.published = 1')

			->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
			->where('module = \'mod_maximenuck\'')
			->where('e.enabled = 1');

		$date = JFactory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();
		$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')')
			->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')')

			->where('m.access IN (' . $groups . ')')
			->where('m.client_id = ' . $clientId)
			->where('(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)');

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
		}

		$query->order('m.position, m.ordering');

		// Set the query
		$db->setQuery($query);
		$clean = array();

		try
		{
			$modules = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()), JLog::WARNING, 'jerror');

			return $clean;
		}

		// Apply negative selections and eliminate duplicates
		$negId = $Itemid ? -(int) $Itemid : false;
		$dupes = array();

		for ($i = 0, $n = count($modules); $i < $n; $i++)
		{
			$module = &$modules[$i];

			// The module is excluded if there is an explicit prohibition
			$negHit = ($negId === (int) $module->menuid);

			if (isset($dupes[$module->id]))
			{
				// If this item has been excluded, keep the duplicate flag set,
				// but remove any item from the cleaned array.
				if ($negHit)
				{
					unset($clean[$module->id]);
				}

				continue;
			}

			$dupes[$module->id] = true;

			// Only accept modules without explicit exclusions.
			if (!$negHit)
			{
				$module->name = substr($module->module, 4);
				$module->style = null;
				$module->position = strtolower($module->position);
				$clean[$module->id] = $module;
			}
		}

		unset($dupes);

		// Return to simple indexing that matches the query order.
		// $clean = array_values($clean);

		return $clean;
	}

	private function getMediaQueries($resolution, $menuID, $moduleParams) {
		$detect_type = $this->params->get('maximenumobile_detectiontype', 'resolution');
		$detect = new Mobile_Detect;
		$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
		$bodypadding = ($moduleParams->get('maximenumobile_container', 'body') == 'body' || $moduleParams->get('maximenumobile_container', 'body') == 'topfixed') ? 'body { padding-top: 40px !important; }' : '';
		if ($detect_type == 'resolution') {
			$css = "@media only screen and (max-width:" . str_replace('px', '', $resolution) . "px){
    #" . $menuID . " { display: none !important; }
    .mobilebarmenuck { display: block; }
	.hidemenumobileck {display: none !important;}
    " . $bodypadding . " }";
		} elseif (($detect_type == 'tablet' && $detect->isMobile()) || ($detect_type == 'phone' && $detect->isMobile() && !$detect->isTablet())) {
			$css = "#" . $menuID . " { display: none !important; }
    .mobilebarmenuck { display: block; }
	.hidemenumobileck {display: none !important;}
    " . $bodypadding;
		} else {
			$css = '';
		}

		return $css;
	}
}