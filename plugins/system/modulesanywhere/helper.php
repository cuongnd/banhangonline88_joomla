<?php
/**
 * Plugin Helper File
 *
 * @package         Modules Anywhere
 * @version         3.6.0
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

NNFrameworkFunctions::loadLanguage('plg_system_modulesanywhere');

/**
 * Plugin that places modules
 */
class plgSystemModulesAnywhereHelper
{
	public function __construct(&$params)
	{
		$this->option = JFactory::getApplication()->input->get('option');

		$this->params = $params;
		$this->params->comment_start = '<!-- START: Modules Anywhere -->';
		$this->params->comment_end = '<!-- END: Modules Anywhere -->';
		$this->params->message_start = '<!--  Modules Anywhere Message: ';
		$this->params->message_end = ' -->';
		$this->params->protect_start = '<!-- START: MA_PROTECT -->';
		$this->params->protect_end = '<!-- END: MA_PROTECT -->';

		$tags = array();
		$tags[] = preg_quote($this->params->module_tag, '#');
		$tags[] = preg_quote($this->params->modulepos_tag, '#');
		if ($this->params->handle_loadposition)
		{
			$tags[] = 'loadposition';
		}
		$this->params->tags = '(' . implode('|', $tags) . ')';

		$bts = '((?:<p(?: [^>]*)?>)?)((?:\s*<br ?/?>)?\s*)';
		$bte = '(\s*(?:<br ?/?>\s*)?)((?:</p>)?)';
		$regex = '((?:\{div(?: [^\}]*)\})?)(\s*)'
			. '\{(' . implode('|', $tags) . ')(?:\s|&nbsp;|&\#160;)((?:[^\}]*?\{[^\}]*?\})*[^\}]*?)\}'
			. '(\s*)((?:\{/div\})?)';
		$this->params->regex = '#' . $bts . $regex . $bte . '#s';
		$this->params->regex2 = '#' . $regex . '#s';

		$this->params->protected_tags = $tags;

		$this->params->message = '';

		$this->aid = JFactory::getUser()->getAuthorisedViewLevels();

		$disabled_components = is_array($this->params->components) ? $this->params->components : array(explode('|', $this->params->components));
		$this->params->disabled_components = array('com_acymailing');
		$this->params->disabled_components = array_merge($disabled_components, $this->params->disabled_components);
	}

	public function onContentPrepare(&$article, &$context)
	{
		$this->params->message = '';

		$area = isset($article->created_by) ? 'articles' : 'other';

		if (!NNProtect::articlePassesSecurity($article, $this->params->articles_security_level))
		{
			$this->params->message = JText::_('MA_OUTPUT_REMOVED_SECURITY');
		}

		NNFrameworkHelper::processArticle($article, $context, $this, 'processModules', array($area));
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed')
		{
			return;
		}

		$buffer = JFactory::getDocument()->getBuffer('component');

		if (empty($buffer) || is_array($buffer))
		{
			return;
		}

		$this->replaceTags($buffer, 'component');

		JFactory::getDocument()->setBuffer($buffer, 'component');
	}

	public function onAfterRender()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed')
		{
			return;
		}

		$html = JResponse::getBody();
		if ($html == '')
		{
			return;
		}

		if (JFactory::getDocument()->getType() != 'html')
		{
			$this->replaceTags($html);
		}
		else
		{
			// only do stuff in body
			list($pre, $body, $post) = nnText::getBody($html);
			$this->replaceTags($body);
			$html = $pre . $body . $post;
		}

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	function replaceTags(&$string, $area = 'article')
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		if (!preg_match('#\{' . $this->params->tags . '#', $string))
		{
			return;
		}

		// allow in component?
		if (
			$area == 'component'
			&& in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components)
		)
		{
			$this->protectTags($string);

			return;
		}

		$this->protect($string);

		// COMPONENT
		if (JFactory::getDocument()->getType() == 'feed')
		{
			$s = '#(<item[^>]*>)#s';
			$string = preg_replace($s, '\1<!-- START: MODA_COMPONENT -->', $string);
			$string = str_replace('</item>', '<!-- END: MODA_COMPONENT --></item>', $string);
		}
		if (strpos($string, '<!-- START: MODA_COMPONENT -->') === false)
		{
			$this->tagArea($string, 'MODA', 'component');
		}

		$this->params->message = '';
		$components = $this->params->components;
		if (!is_array($components))
		{
			$components = explode('|', $components);
		}

		if (in_array($this->option, $components))
		{
			// For all components that are selected, set the message
			$this->params->message = JText::_('MA_OUTPUT_REMOVED_NOT_ENABLED');
		}

		$components = $this->getTagArea($string, 'MODA', 'component');

		foreach ($components as $component)
		{
			$this->processModules($component['1'], 'components');
			$string = str_replace($component['0'], $component['1'], $string);
		}

		// EVERYWHERE
		$this->processModules($string, 'other');

		NNProtect::unprotect($string);
	}

	function tagArea(&$string, $ext = 'EXT', $area = '')
	{
		if ($string && $area)
		{
			$string = '<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->' . $string . '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
			if ($area == 'article_text')
			{
				$string = preg_replace('#(<hr class="system-pagebreak".*?/>)#si', '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->\1<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->', $string);
			}
		}
	}

	function getTagArea(&$string, $ext = 'EXT', $area = '')
	{
		$matches = array();
		if ($string && $area)
		{
			$start = '<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
			$end = '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
			$matches = explode($start, $string);
			array_shift($matches);
			foreach ($matches as $i => $match)
			{
				list($text) = explode($end, $match, 2);
				$matches[$i] = array(
					$start . $text . $end,
					$text
				);
			}
		}

		return $matches;
	}

	function processModules(&$string, $area = 'articles')
	{
		if (
			$area == 'articles' && !$this->params->articles_enable
			|| $area == 'components' && !$this->params->components_enable
			|| $area == 'other' && !$this->params->other_enable
		)
		{
			$this->params->message = JText::_('MA_OUTPUT_REMOVED_NOT_ENABLED');
		}

		if (preg_match('#\{' . $this->params->tags . '#', $string))
		{
			jimport('joomla.application.module.helper');
			JPluginHelper::importPlugin('content');

			self::replace($string, $this->params->regex, $area);
			self::replace($string, $this->params->regex2, $area);
		}
	}

	function replace(&$string, $regex, $area = 'articles')
	{
		if (@preg_match($regex . 'u', $string))
		{
			$regex .= 'u';
		}

		$matches = array();
		$count = 0;
		$protects = array();
		while ($count++ < 10 && preg_match('#\{' . $this->params->tags . '#', $string) && preg_match_all($regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				if (!$this->processMatch($string, $match, $area))
				{
					$protected = $this->params->protect_start . base64_encode($match['0']) . $this->params->protect_end;
					$string = str_replace($match['0'], $protected, $string);
					$protects[] = array($match['0'], $protected);
				}
			}
			$matches = array();
		}
		foreach ($protects as $protect)
		{
			$string = str_replace($protect['1'], $protect['0'], $string);
		}
	}

	function processMatch(&$string, &$match, $area = 'articles')
	{
		$html = '';
		if ($this->params->message != '')
		{
			if ($this->params->place_comments)
			{
				$html = $this->params->message_start . $this->params->message . $this->params->message_end;
			}
		}
		else
		{
			if (count($match) < 10)
			{
				array_unshift($match, $match['0'], '');
				$match['2'] = '';
				array_push($match, '', '');
			}
			$p_start = $match['1'];
			$br1a = $match['2'];
			$div_start = $match['3'];
			$br2a = $match['4'];
			$type = trim($match['5']);
			$id = trim($match['6']);
			$br2a = $match['7'];
			$div_end = $match['8'];
			$br2b = $match['9'];
			$p_end = $match['10'];

			$type = trim($type);
			$id = trim($id);

			$chrome = '';
			$forcetitle = 0;

			$ignores = array();
			$overrides = array();

			$vars = str_replace('\|', '[:MA_BAR:]', $id);
			$vars = explode('|', $vars);
			$id = array_shift($vars);
			foreach ($vars as $var)
			{
				$var = trim(str_replace('[:MA_BAR:]', '|', $var));
				if (!$var)
				{
					continue;
				}
				if (strpos($var, '=') === false)
				{
					if ($this->params->override_style)
					{
						$chrome = $var;
					}
				}
				else
				{
					if ($type == $this->params->module_tag)
					{
						list($key, $val) = explode('=', $var, 2);
						$val = str_replace(array('\{', '\}'), array('{', '}'), $val);
						if ($key == 'style')
						{
							$chrome = $val;
						}
						else if (in_array($key, array('ignore_access', 'ignore_state', 'ignore_assignments', 'ignore_caching')))
						{
							$ignores[$key] = $val;
						}
						else if ($key == 'showtitle')
						{
							$overrides['showtitle'] = $val;
							$forcetitle = $val;
						}
						else if ($this->params->override_settings)
						{
							$overrides[$key] = html_entity_decode($val);
						}
					}
				}
			}

			if (!$chrome)
			{
				$chrome = ($forcetitle) ? 'xhtml' : $this->params->style;
			}

			if ($type == $this->params->module_tag)
			{
				// module
				$html = $this->processModule($id, $chrome, $ignores, $overrides, $area);
				if ($html == 'MA_IGNORE')
				{
					return 0;
				}
			}
			else
			{
				// module position
				$html = $this->processPosition($id, $chrome);
			}

			if ($p_start && $p_end)
			{
				$p_start = '';
				$p_end = '';
			}

			$html = $br1a . $br2a . $html . $br2a . $br2b;

			if ($div_start)
			{
				$extra = trim(preg_replace('#\{div(.*)\}#si', '\1', $div_start));
				$div = '';
				if ($extra)
				{
					$extra = explode('|', $extra);
					$extras = new stdClass;
					foreach ($extra as $e)
					{
						if (strpos($e, ':') !== false)
						{
							list($key, $val) = explode(':', $e, 2);
							$extras->$key = $val;
						}
					}
					if (isset($extras->class))
					{
						$div .= 'class="' . $extras->class . '"';
					}

					$style = array();
					if (isset($extras->width))
					{
						if (is_numeric($extras->width))
						{
							$extras->width .= 'px';
						}
						$style[] = 'width:' . $extras->width;
					}
					if (isset($extras->height))
					{
						if (is_numeric($extras->height))
						{
							$extras->height .= 'px';
						}
						$style[] = 'height:' . $extras->height;
					}
					if (isset($extras->align))
					{
						$style[] = 'float:' . $extras->align;
					}
					else if (isset($extras->float))
					{
						$style[] = 'float:' . $extras->float;
					}

					if (!empty($style))
					{
						$div .= ' style="' . implode(';', $style) . ';"';
					}
				}
				$html = trim('<div ' . trim($div)) . '>' . $html . '</div>';

				$html = $p_end . $html . $p_start;
			}
			else
			{
				$html = $p_start . $html . $p_end;
			}

			nnText::fixHtmlTagStructure($html);
		}

		if ($this->params->place_comments)
		{
			$html = $this->params->comment_start . $html . $this->params->comment_end;
		}

		$string = str_replace($match['0'], $html, $string);
		unset($match);

		return 1;
	}

	function processPosition($position, $chrome = 'none')
	{
		$renderer = JFactory::getDocument()->loadRenderer('module');

		$html = array();
		foreach (JModuleHelper::getModules($position) as $module)
		{
			$module_html = $renderer->render($module, array('style' => $chrome));

			if ($this->params->show_edit)
			{
				$this->addFrontendEditing($module, $module_html);
			}

			$html[] = $module_html;
		}

		return implode('', $html);
	}

	function processModule($id, $chrome = 'none', $ignores = array(), $overrides = array(), $area = 'articles')
	{
		$ignore_access = isset($ignores['ignore_access']) ? $ignores['ignore_access'] : $this->params->ignore_access;
		$ignore_state = isset($ignores['ignore_state']) ? $ignores['ignore_state'] : $this->params->ignore_state;
		$ignore_assignments = isset($ignores['ignore_assignments']) ? $ignores['ignore_assignments'] : $this->params->ignore_assignments;
		$ignore_caching = isset($ignores['ignore_caching']) ? $ignores['ignore_caching'] : $this->params->ignore_caching;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('m.*')
			->from('#__modules AS m')
			->where('m.client_id = 0');
		if (is_numeric($id))
		{
			$query->where('m.id = ' . (int) $id);
		}
		else
		{
			$query->where('m.title = ' . $db->quote(NNText::html_entity_decoder($id)));
		}
		if (!$ignore_access)
		{
			$query->where('m.access IN (' . implode(',', $this->aid) . ')');
		}
		if (!$ignore_state)
		{
			$query->where('m.published = 1')
				->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
				->where('e.enabled = 1');
		}
		if (!$ignore_assignments)
		{
			$date = JFactory::getDate();
			$now = $date->toSql();
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')')
				->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')');
		}
		$query->order('m.ordering');
		$db->setQuery($query);
		$module = $db->loadObject();

		if ($module && !$ignore_assignments)
		{
			$this->applyAssignments($module);
		}

		if (empty($module))
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('MA_OUTPUT_REMOVED_NOT_PUBLISHED') . $this->params->message_end;
			}

			return '';
		}

		//determine if this is a custom module
		$module->user = (substr($module->module, 0, 4) == 'mod_') ? 0 : 1;

		// set style
		$module->style = $chrome;

		if (($area == 'articles' && !$ignore_caching) || !empty($overrides))
		{
			$json = ($module->params && substr(trim($module->params), 0, 1) == '{');
			if ($json)
			{
				$params = json_decode($module->params);
			}
			else
			{
				// Old ini style. Needed for crappy old style modules like swMenuPro
				$params = JRegistryFormat::getInstance('INI')->stringToObject($module->params);
			}

			// override module parameters
			if (!empty($overrides))
			{
				foreach ($overrides as $key => $val)
				{
					if (isset($module->{$key}))
					{
						$module->{$key} = $val;
					}
					else
					{
						if ($val && $val['0'] == '[' && $val[strlen($val) - 1] == ']')
						{
							$val = json_decode('{"val":' . $val . '}');
							$val = $val->val;
						}
						else if (isset($params->{$key}) && is_array($params->{$key}))
						{
							$val = explode(',', $val);
						}
						$params->{$key} = $val;
					}
				}
				if ($json)
				{
					$module->params = json_encode($params);
				}
				else
				{
					$registry = new JRegistry;
					$registry->loadObject($params);
					$module->params = $registry->toString('ini');
				}
			}
		}

		if (isset($module->access) && !in_array($module->access, $this->aid))
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('MA_OUTPUT_REMOVED_ACCESS') . $this->params->message_end;
			}

			return '';
		}

		$document = clone(JFactory::getDocument());
		$document->_type = 'html';
		$renderer = $document->loadRenderer('module');
		$html = $renderer->render($module, array('style' => $chrome, 'name' => ''));

		$show_edit = isset($overrides['show_edit']) ? $overrides['show_edit'] : $this->params->show_edit;
		if ($show_edit)
		{
			$this->addFrontendEditing($module, $html);
		}

		// don't return html on article level when caching is set
		if (
			$area == 'articles'
			&& !$ignore_caching
			&& (
				(isset($params->cache) && !$params->cache)
				|| (isset($params->owncache) && !$params->owncache) // for stupid modules like RAXO that mess about with default params
			)
		)
		{
			return 'MA_IGNORE';
		}

		return $html;
	}

	function addFrontendEditing(&$module, &$html)
	{
		if (
			trim($html) == ''
			|| !JFactory::getApplication()->isSite()
			|| !JFactory::getUser()->id
			|| !JFactory::getUser()->authorise('core.edit', 'com_modules')
			|| !JFactory::getUser()->authorise('core.edit', 'com_modules.module.' . $module->id)
		)
		{
			return;
		}

		if (!$frontediting = JFactory::getApplication()->get('frontediting', 1))
		{
			return;
		}

		$displayData = array('moduleHtml' => &$html, 'module' => $module, 'position' => '[:REMOVE:]', 'menusediting' => ($frontediting == 2));
		JLayoutHelper::render('joomla.edit.frontediting_modules', $displayData);

		$position_tip = htmlspecialchars('<br />' . sprintf(JText::_('JLIB_HTML_EDIT_MODULE_IN_POSITION'), '[:REMOVE:]'));
		$html = str_replace($position_tip, '', $html);
	}

	function applyAssignments(&$module)
	{
		$module->published = 1;
		$modules = array($module->id => $module);
		$this->onPrepareModuleList($modules);
		$module = array_shift($modules);

		if (!$module->published)
		{
			$module = 0;
		}
	}

	function onPrepareModuleList(&$modules)
	{
		// for old Advanced Module Manager versions
		if (function_exists('plgSystemAdvancedModulesPrepareModuleList'))
		{
			plgSystemAdvancedModulesPrepareModuleList($modules);

			return;
		}

		if (!class_exists('plgSystemAdvancedModuleHelper'))
		{
			return;
		}

		// for new Advanced Module Manager versions
		$helper = new plgSystemAdvancedModuleHelper;
		$helper->onPrepareModuleList($modules);
	}

	function protect(&$string)
	{
		NNProtect::protectFields($string);
		NNProtect::protectSourcerer($string);
	}

	function protectTags(&$string)
	{
		NNProtect::protectTags($string, $this->params->protected_tags);
	}

	function unprotectTags(&$string)
	{
		NNProtect::unprotectTags($string, $this->params->protected_tags);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	function cleanLeftoverJunk(&$string)
	{
		$this->unprotectTags($string);

		$string = preg_replace('#<\!-- (START|END): MODA_[^>]* -->#', '', $string);
		if ($this->params->place_comments)
		{
			return;
		}

		$string = str_replace(
			array(
				$this->params->comment_start, $this->params->comment_end,
				htmlentities($this->params->comment_start), htmlentities($this->params->comment_end),
				urlencode($this->params->comment_start), urlencode($this->params->comment_end)
			), '', $string
		);
		$string = preg_replace('#' . preg_quote($this->params->message_start, '#') . '.*?' . preg_quote($this->params->message_end, '#') . '#', '', $string);
	}
}
