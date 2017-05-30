<?php
/**
 * @package         Modals
 * @version         8.0.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_LIBRARIES . '/regularlabs/helpers/functions.php';
require_once JPATH_LIBRARIES . '/regularlabs/helpers/tags.php';
require_once JPATH_LIBRARIES . '/regularlabs/helpers/text.php';
require_once JPATH_LIBRARIES . '/regularlabs/helpers/protect.php';

RLFunctions::loadLanguage('plg_system_modals');

/**
 * Plugin that replaces stuff
 */
class PlgSystemModalsHelper
{
	var $params  = null;
	var $helpers = array();

	public function __construct(&$params)
	{
		$this->params = $params;

		$this->params->class = 'modal_link';
		// array_filter will remove any empty values
		$this->params->classnames = $this->params->autoconvert_classnames ? RLText::createArray(str_replace(' ', ',', trim($this->params->classnames))) : array();
		$this->params->classnames_images = $this->params->autoconvert_classnames_images ? RLText::createArray(str_replace(' ', ',', trim($this->params->classnames_images))) : array();
		$this->params->filetypes         = $this->params->autoconvert_filetypes ? RLText::createArray(str_replace(array(' ', '.'), '', $this->params->filetypes)) : array();
		$this->params->urls              = $this->params->autoconvert_urls ? RLText::createArray(str_replace("\r", '', $this->params->urls), "\n") : array();
		$this->params->auto_group_id     = uniqid('gallery_');

		$this->params->tag = preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag);
		$this->params->tag_content = preg_replace('#[^a-z0-9-_]#si', '', $this->params->tag_content);

		$this->params->paramNamesCamelcase = array(
			'innerWidth', 'innerHeight', 'initialWidth', 'initialHeight', 'maxWidth', 'maxHeight', 'className',
			'scalePhotos', 'returnFocus', 'fastIframe',
			'closeButton', 'overlayClose', 'escKey', 'arrowKey', 'xhrError', 'imgError',
			'slideshowSpeed', 'slideshowAuto', 'slideshowStart', 'slideshowStop',
			'retinaImage', 'retinaUrl', 'retinaSuffix',
			'onOpen', 'onLoad', 'onComplete', 'onCleanup', 'onClosed',
		);
		$this->params->paramNamesLowercase = array_map('strtolower', $this->params->paramNamesCamelcase);
		$this->params->paramNamesBooleans  = array(
			'scalephotos', 'scrolling', 'inline', 'iframe', 'fastiframe',
			'photo', 'preloading', 'retinaimage', 'open', 'returnfocus', 'trapfocus', 'reposition',
			'loop', 'slideshow', 'slideshowauto', 'overlayclose', 'closebutton', 'esckey', 'arrowkey', 'fixed',
		);

		if (JFactory::getApplication()->input->getInt('ml', 0))
		{
			JFactory::getApplication()->input->set('tmpl', JFactory::getApplication()->input->getWord('tmpl', $this->params->tmpl));
		}

		require_once __DIR__ . '/helpers/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance($params);
	}

	public function onContentPrepare(&$article, $context, $params)
	{
		$area    = isset($article->created_by) ? 'articles' : 'other';
		$context = (($params instanceof JRegistry) && $params->get('rl_search')) ? 'com_search.' . $params->get('readmore_limit') : $context;

		RLHelper::processArticle($article, $context, $this, 'replace', array($area, $context));
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html'
			&& !RLFunctions::isFeed()
		)
		{
			return;
		}

		// do not load scripts/styles on feed or print page
		if (!RLFunctions::isFeed()
			&& !JFactory::getApplication()->input->getInt('print', 0)
		)
		{
			$this->helpers->get('scripts')->loadScriptsStyles();
		}

		if (!$buffer = RLFunctions::getComponentBuffer())
		{
			return;
		}

		$this->replace($buffer, 'component');

		JFactory::getDocument()->setBuffer($buffer, 'component');
	}

	public function onAfterRender()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && !RLFunctions::isFeed())
		{
			return;
		}

		$html = JFactory::getApplication()->getBody();
		if ($html == '')
		{
			return;
		}

		// only do stuff in body
		list($pre, $body, $post) = RLText::getBody($html);
		$this->replace($body, 'body');

		if (strpos($body, $this->params->class) === false)
		{
			// remove style and script if no items are found
			$pre = preg_replace('#\s*<' . 'link [^>]*href="[^"]*/(modals/css|css/modals)/[^"]*\.css[^"]*"[^>]*( /)?>#s', '', $pre);
			$pre = preg_replace('#\s*<' . 'script [^>]*src="[^"]*/(modals/js|js/modals)/[^"]*\.js[^"]*"[^>]*></script>#s', '', $pre);
			$pre = preg_replace('#((?:;\s*)?)(;?)/\* START: Modals .*?/\* END: Modals [a-z]* \*/\s*#s', '\1', $pre);
		}

		$html = $pre . $body . $post;

		$this->cleanLeftoverJunk($html);

		JFactory::getApplication()->setBody($html);
	}

	public function replace(&$string, $area = 'article', $context = '')
	{
		$this->helpers->get('replace')->replace($string, $area, $context);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	private function cleanLeftoverJunk(&$string)
	{
		$this->helpers->get('protect')->unprotectTags($string);

		RLProtect::removeFromHtmlTagContent($string, $this->params->protected_tags);
		RLProtect::removeInlineComments($string, 'Modals');
	}
}
