<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.openGraph');
jimport('sourcecoast.utilities');

class OpenGraphPlugin extends JPlugin
{
    var $pluginName;
    var $extensionName;
    var $supportedActions;
    var $supportedObjects;
    var $jfbcOgActionModel;
    var $jfbcOgObjectModel;
    var $supportedComponents;
    var $jfbcLibrary;
    var $object;
    var $db;
    var $setsDefaultTags;

    private $openGraphLibrary;

    function __construct(&$subject, $config)
    {
        $this->pluginName = $config['name'];
        $this->extensionName = $config['name']; // Should be overridden by the plugin itself.
        if (class_exists('JFBConnectFacebookLibrary'))
        {
            JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/models');
            $this->jfbcOgActionModel = JModelLegacy::getInstance('OpenGraphAction', 'JFBConnectModel');
            $this->jfbcOgObjectModel = JModelLegacy::getInstance('OpenGraphObject', 'JFBConnectModel');
            $this->jfbcLibrary = JFBCFactory::provider('facebook');
        }

        if (class_exists('OpenGraphLibrary'))
            $this->openGraphLibrary = OpenGraphLibrary::getInstance();

        $this->hasDefaultTags = false;
        $this->db = JFactory::getDBO();

        parent::__construct($subject, $config);

        $this->init();
    }

    protected function init()
    {
    }

    /******* Triggers ******/
    public function onOpenGraphGetPlugins()
    {
        return $this;
    }

    public function onOpenGraphAfterRoute()
    {
        if (count($this->supportedActions) > 0)
        {
            if ($this->jfbcLibrary->userIsConnected())
                $this->setupDefinedActions();
        }
    }

    public function onOpenGraphAfterDispatch()
    {
        if ($this->inSupportedComponent())
        {
            $juri = JURI::getInstance();
            $url = $juri->toString();
            $queryVars = $this->jfbcOgActionModel->getUrlVars($url);
            $this->object = $this->findObjectType($queryVars);

            // Set the OG tags if this plugin has an object type set *or* will set OG tags even without objects set by the admin
            if ($this->object || $this->setsDefaultTags)
                $this->setOpenGraphTags();

            if ($this->object)
            {
                $this->setTypeTag();

                // If user is connected, see if there are any actions that should be setup
                if ($this->jfbcLibrary->userIsConnected())
                {
                    $this->setupTimedActions();
                    $this->setupPageActions();
                }
            }
        }
    }

    public function onOpenGraphAJAXAction($actionId, $objectId, $url)
    {
        $action = $this->jfbcOgActionModel->getAction($actionId);
        return $this->triggerAction($action, $url);
    }

    public function onOpenGraphFindObjectType($url)
    {
        $queryVars = $this->jfbcOgActionModel->getUrlVars($url);
        return $this->findObjectType($queryVars);
    }

    public function onOpenGraphGetBestImage($article)
    {
        return $this->getBestImage($article);
    }

    public function onOpenGraphGetBestText($article)
    {
        return $this->getBestText($article);
    }

    /******** End triggers ********/

    /******** Object Calls ********/
    protected function getObjects($type)
    {
        // Can we make this more efficient to load all the plugin objects once, and then just pick off the 'name' types
        // when we need them?
        return $this->jfbcOgObjectModel->getPluginObjects($this->pluginName, $type);
    }

    protected function addSupportedAction($systemName, $displayName)
    {
        $this->supportedActions[$systemName] = $displayName;
    }

    protected function addSupportedObject($systemName, $displayName)
    {
        $this->supportedObjects[$systemName] = $displayName;
    }

    private function inSupportedComponent()
    {
        // If none are defined, plugin always fired
        if (!$this->supportedComponents)
            return true;

        if (in_array(JRequest::getCmd('option'), $this->supportedComponents))
            return true;

        return false;
    }

    protected function addOpenGraphTag($name, $value, $isFinal)
    {
        $this->openGraphLibrary->addOpenGraphTag($name, $value, $isFinal, PRIORITY_NORMAL, "Open Graph - " . ucfirst($this->pluginName)." Plugin");
    }

    protected function skipOpenGraphTag($name)
    {
        $this->openGraphLibrary->skipOpenGraphTag($name, $this->pluginName);
    }

    protected function getDefaultObject($name)
    {
        $object = new ogObject();
        $object->loadDefaultObject($this->pluginName, $name);

        return $object;
    }

    // Setup any extra Open Graph tags specific to this object (title, image, description, video, etc)
    protected function setOpenGraphTags()
    {
        // Should be overridden by the plugin
    }

    // Actions that are triggered after a timeout while viewing the page.
    protected function setupTimedActions()
    {
        $doc = JFactory::getDocument();
        $actionAdded = false;
        foreach ($this->object->getAssociatedActions() as $action)
        {
            if ($action->params->get('og_auto_type') == "page_load" && $action->params->get('og_auto_timer') > 0)
            {
                $key = $this->getUniqueKey($this->getCurrentURL());
                $jq = JFBCFactory::config()->getSetting('jquery_load') ? 'jfbcJQuery' : 'jQuery';
                if ($action->actionReady($key))
                {
                    $actionAdded = true;
                    // The space between { jfbc.opengraph is required. Otherwise, our system plugin will strip it thinking it's a {JFBCxyz} tag.
                    // Not the perfect solution, but it works..
                    $doc->addScriptDeclaration($jq . "(document).ready(function () {" .
                            "setTimeout(function(){ jfbc.opengraph.triggerAction('" . $action->id . "','" . $this->getCurrentURL() . "');}, " . ($action->params->get('og_auto_timer') * 1000) . ");" .

                            "});"
                    );
                }
                else
                {
                    $data = $action->getLastPublished($key);
                    if ($data)
                    {
                        SCStringUtilities::loadLanguage('com_jfbconnect');
                        $actionAdded = true;
                        $date = new JDate($data->created);
                        $doc->addScriptDeclaration($jq . "(document).ready(function () {" .
                            "setTimeout(function(){jfbc.opengraph.actionPopup('<span class=\"ogMessage\">" .
                            JText::sprintf("COM_JFBCONNECT_OPENGRAPH_ACTION_EXISTS", $date->format(JText::_('DATE_FORMAT_LC4'))) . "</span>" .
                            '<span class="ogOptions"><a href="' . JRoute::_('index.php?option=com_jfbconnect&task=opengraph.userdelete&actionid=' . $data->id) . '" target="_blank">' . JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_DELETE') . '</a>' .
                            ' | <a href="' . JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=activity') . '" target="_blank">' . JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_SEE_ALL') . '</a></span>' .
                            "')})" .
                            "});"
                        );
                    }
                }
            }
        }
        if ($actionAdded)
        {
            // Include our CSS file for styling the popup
            JFBCFactory::addStylesheet('jfbconnect.css');
        }
    }

    // Actions that are triggered immediately just by viewing the page.
    protected function setupPageActions()
    {
        foreach ($this->object->getAssociatedActions() as $action)
        {
            if ($action->params->get('og_auto_type') == "page_load" && $action->params->get('og_auto_timer') == 0)
            {
                if ($action->actionReady($this->getUniqueKey($this->getCurrentURL())))
                    $this->triggerAction($action);
            }
        }
    }

    protected function setupDefinedActions()
    {
        // get any defined action instances
        foreach ($this->supportedActions as $supportedAction)
        {
            $actions = $this->jfbcOgActionModel->getActionsOfType($this->pluginName, $supportedAction);
            foreach ($actions as $action)
            {
                $this->checkActionAfterRoute($action);
            }
        }
    }

    protected function checkActionAfterRoute($action)
    {
        // Override by plugin
    }

    protected function triggerAction($action, $url = null)
    {
        if (!$url)
            $url = $this->getCurrentURL();

        $uniqueKey = $this->getUniqueKey($url);
        $this->jfbcOgActionModel->triggerAction($action, $url, null, $uniqueKey);
    }

    // getUniqueKey
    // Used for generating a unique key per URL or 'item'. Used to ensure that certain actions aren't repeated within the
    // set action interval. For example, reading an article should only be triggered once (or at most every day).
    // By default, will decompose the URL to non-SEF and look for an id query string param.
    // If none exists, will use the MD5 hash of the full path/query string of URL.
    protected function getUniqueKey($url)
    {
        return $this->jfbcOgActionModel->getUniqueKey($url);
    }

    protected function setTypeTag()
    {
        $this->addOpenGraphTag('type', $this->object->getObjectPath(), true);
    }

    private function getCurrentURL()
    {
        $uri = JURI::getInstance();
        return $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query'));
    }

    protected function getBestImage($article) { return null; }

    protected function getBestText($article) { return null; }

    /******** Get Images and Descriptions ********/

    protected function getFirstCategoryText($category, $numCharacters = 100, $socialGraphFirstText = '1')
    {
        $categoryText = '';
        if (isset($category->description))
            $categoryText = $this->getSelectedText($category->description, $socialGraphFirstText, $numCharacters);
        return $categoryText;
    }

    protected function getFirstArticleText($article, $numCharacters = 100, $socialGraphFirstText = '1')
    {
        $articleText = '';
        if (isset($article->introtext) && trim(strip_tags($article->introtext)) != "")
        {
            $articleText = $article->introtext;
        } else if (isset($article->text) && trim(strip_tags($article->text)) != "")
        {
            $articleText = $article->text;
        } else if (isset($article->fulltext) && trim(strip_tags($article->fulltext)) != "")
        {
            $articleText = $article->fulltext;
        }

        $articleText = $this->getSelectedText($articleText, $socialGraphFirstText, $numCharacters);

        return $articleText;
    }

    protected function getSelectedText($contentText, $socialGraphFirstText, $numCharacters)
    {
        $articleText = SCStringUtilities::trimNBSP($contentText);
        $articleText = strip_tags($articleText);
        $articleText = preg_replace('/\s+/', ' ', $articleText);
        $articleText = str_replace('{K2Splitter}', '', $articleText);
        SCSocialUtilities::stripSystemTags($articleText, 'JFBC');
        SCSocialUtilities::stripSystemTags($articleText, 'JLinked');
        SCSocialUtilities::stripSystemTags($articleText, 'SC');
        SCSocialUtilities::stripSystemTags($articleText, 'loadposition');
        $articleText = trim($articleText);

        $addEllipsis = false;
        if ($socialGraphFirstText == '1')
        {
            $addEllipsis = strlen($articleText) > $numCharacters;

            if (function_exists('mb_substr'))
                $articleText = mb_substr($articleText, 0, $numCharacters, 'UTF-8');
            else
                $articleText = substr($articleText, 0, $numCharacters);
        } else if ($socialGraphFirstText == '2')
        {
            if (function_exists('mb_split'))
                $parts = mb_split('\s+', $articleText);
            else
                $parts = preg_split('\s+', $articleText);
            $selParts = array_slice($parts, 0, $numCharacters);
            $articleText = implode(" ", $selParts);
            $addEllipsis = count($parts) > $numCharacters;
        } else
            $articleText = '';

        if ($addEllipsis)
            $articleText .= '...';

        return $articleText;
    }

    protected function getFirstImage($article)
    {
        if (isset($article->text))
            $articleText = $article->text;
        else
            $articleText = $article->introtext . $article->fulltext;

        $fullImagePath = $this->getFirstImageFromText($articleText);
        return $fullImagePath;
    }

    protected function getFirstImageFromText($text)
    {
        $fullImagePath = '';
        if (preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $text, $matches))
        {
            $fullImagePath = $this->getImageLink($matches[1][0]);
        }
        return $fullImagePath;
    }

    protected function getImageLink($path)
    {
        if ($path)
        {
            $juri = JURI::getInstance();
            $basePath = str_replace(array($juri->getScheme() . "://", $juri->getHost()), "", $juri->base());

            if (strpos($path, $basePath) === 0)
            {
                $path = substr($path, strlen($basePath));
                $path = $juri->base() . $path;
            } else if (strpos($path, "http") !== 0)
                $path = $juri->base() . $path;
        }
        return $path;
    }
}