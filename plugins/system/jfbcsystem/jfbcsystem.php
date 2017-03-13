<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');
jimport('sourcecoast.openGraph');
jimport('sourcecoast.utilities');
jimport('sourcecoast.easyTags');
jimport('sourcecoast.articleContent');
jimport('joomla.filesystem.file');

class plgSystemJFBCSystem extends JPlugin
{
    var $tagsToReplace;
    var $metadataTagsToStrip = array('JFBC', 'JLinked', 'SC');

    static $cssIncluded = false;

    function __construct(& $subject, $config)
    {
        $factoryFile = JPATH_ROOT . '/components/com_jfbconnect/libraries/factory.php';
        if (!JFile::exists($factoryFile))
        {
            JFactory::getApplication()->enqueueMessage("File missing: " . $factoryFile . "<br/>Please re-install JFBConnect or disable the JFBCSystem Plugin", 'error');
            return; // Don't finish loading this plugin to prevent other errors
        }
        require_once($factoryFile);
        // Need to load this as some custom developers expect this file to already be loaded and using the old JFBCFacebookLibrary classname
        // Doing this for backward compatibility in v5.1. Remove in the future
        require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/provider/facebook.php');

        parent::__construct($subject, $config);
    }

    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin())
        {
            // Need to disable Page caching so that values fetched from Facebook are not saved for the next user!
            // Do this by setting the request type to POST. In the Cache plugin, it's checked for "GET". can't be that.
            $option = JRequest::getCmd("option");
            $view = JRequest::getCmd("view");
            if ($option == 'com_jfbconnect' && $view == 'loginregister')
                $_SERVER['REQUEST_METHOD'] = 'POST';

            // Need to load our plugin group early to be able to hook into to every step after
            JPluginHelper::importPlugin('opengraph');
            JPluginHelper::importPlugin('socialprofiles');

            $providers = JFBCFactory::getAllProviders();
            foreach ($providers as $provider)
                $provider->onAfterInitialise();

            $this->buildListOfTagsToReplace();
        }
    }

    public function onAfterRoute()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin())
        {
            $app = JFactory::getApplication();
            $app->triggerEvent('onOpenGraphAfterRoute');
            if ($app->getUserState('com_jfbconnect.registration.alternateflow'))
                $app->triggerEvent('socialProfilesPrefillRegistration');
        }
    }

    // Called after the component has executed and it's output is available in the buffer
    // Modules have *not* executed yet
    public function onAfterDispatch()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin())
        {
            $providers = JFBCFactory::getAllProviders();
            foreach ($providers as $provider)
                $provider->onAfterDispatch();

            foreach ($this->metadataTagsToStrip as $metadataTag)
            {
                $this->replaceTagInMetadata($metadataTag);
            }

            $doc = JFactory::getDocument();
            if (JFBCFactory::config()->get('bootstrap_css'))
                $doc->addStyleSheet(JURI::base(true) . '/media/sourcecoast/css/sc_bootstrap.css');
            $doc->addStyleSheet(JURI::base(true) . '/media/sourcecoast/css/common.css');

            if ($doc->getType() == 'html')
            {
                $doc->addCustomTag('<SourceCoastProviderJSPlaceholder />');
                if (JFBCFactory::config()->getSetting('jquery_load'))
                    $doc->addScript(JURI::base(true) . '/media/sourcecoast/js/jq-bootstrap-1.8.3.js');
            }

            //Add Login with FB button to com_users login view and mod_login
            $showLoginWithJoomla = JFBCFactory::config()->getSetting('show_login_with_joomla_reg');
            if ($showLoginWithJoomla != SC_VIEW_NONE)
            {
                SCStringUtilities::loadLanguage('com_jfbconnect');

                if (SCEasyTags::canExtendJoomlaForm("login", false, $showLoginWithJoomla))
                {
                    $login = JFBCFactory::cache()->get('system.joomlaform.login');
                    if ($login === false)
                    {
                        $login = JFBCFactory::provider('facebook')->getLoginButton(JText::_('COM_JFBCONNECT_LOGIN_WITH'));
                        JFBCFactory::cache()->store($login, 'system.joomlaform.login');
                    }
                    SCEasyTags::extendJoomlaUserForms($login, $showLoginWithJoomla);
                }

                if (SCEasyTags::canExtendJoomlaForm('registration', false, $showLoginWithJoomla))
                {
                    $registration = JFBCFactory::cache()->get('system.joomlaform.registration');
                    if ($registration === false)
                    {
                        $registration = JFBCFactory::provider('facebook')->getLoginButton(JText::_('COM_JFBCONNECT_REGISTER_WITH'));
                        JFBCFactory::cache()->store($registration, 'system.joomlaform.registration');
                    }
                    SCEasyTags::extendJoomlaUserForms($registration, $showLoginWithJoomla);
                }
            }

            // Add the Open Graph links to the user edit form.
            if ($this->showOpenGraphProfileLinks() && JFBCFactory::provider('facebook')->userIsConnected() &&
                    SCEasyTags::canExtendJoomlaForm('profile', true, SC_VIEW_BOTTOM)
            )
            {
                SCStringUtilities::loadLanguage('com_jfbconnect');

                $htmlTag = '<a href="' . JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=activity') . '">' . JText::_('COM_JFBCONNECT_TIMELINE_ACTIVITY_LINK') . '</a>';
                $htmlTag .= '<br/><a href="' . JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=settings') . '">' . JText::_('COM_JFBCONNECT_TIMELINE_CHANGESETTINGS') . '</a>';

                SCEasyTags::extendJoomlaUserForms($htmlTag, SC_VIEW_BOTTOM);
            }

            JPluginHelper::importPlugin('opengraph');
            $app->triggerEvent('onOpenGraphAfterDispatch');

            // Finally, load the Toolbar classes
            JFBCFactory::library('toolbar')->onAfterDispatch();
        }
    }

    // Called right before the page is rendered
    public function onBeforeRender()
    {
        if (!JFactory::getApplication()->isAdmin() && JFactory::getDocument()->getType() == 'html')
        {
            if (JFactory::getUser()->authorise('jfbconnect.opengraph.debug', 'com_jfbconnect') && JFBCFactory::config()->get('facebook_display_errors'))
                JFBCFactory::addStylesheet('jfbconnect.css');

            JFactory::getDocument()->addCustomTag('<SourceCoastCSSPlaceholder />');
        }
    }

    public function onAfterRender()
    {
        if (!JFactory::getApplication()->isAdmin())
        {
            $this->doTagReplacements();

            $providers = JFBCFactory::getAllProviders();
            foreach ($providers as $provider)
                $provider->onAfterRender();

            JFBCFactory::library('toolbar')->onAfterRender();
            $this->replaceCSSPlaceholder();
        }
        return true;
    }

    private function replaceCSSPlaceholder()
    {
        $cssFiles = JFBCFactory::getStylesheets();
        $contents = JResponse::getBody();
        $tag = '';
        foreach ($cssFiles as $f)
        {
            $tag .= '<link rel="stylesheet" href="' . JUri::base(true) . '/media/sourcecoast/css/' . $f . '" type="text/css" />';
        }
        $contents = str_replace('<SourceCoastCSSPlaceholder />', $tag, $contents);
        JResponse::setBody($contents);
    }

    private function showOpenGraphProfileLinks()
    {
        if (JFactory::getUser()->guest)
            return false;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->qn('#__opengraph_action'))
            ->where($db->qn('published') . '=' . $db->q(1));
        $db->setQuery($query);
        $numOGActionsEnabled = $db->loadResult();

        $user = JFactory::getUser();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->qn('#__opengraph_activity'))
            ->where($db->qn('user_id') . '=' . $db->q($user->id))
            ->where($db->qn('status') . '=' . $db->q(1));
        $db->setQuery($query);
        $numActivities = $db->loadResult();

        return ($numOGActionsEnabled > 0) || ($numActivities > 0);
    }

    private function replaceTagInMetadata($metadataTag)
    {
        $doc = JFactory::getDocument();
        $description = $doc->getDescription();
        $replace = SCSocialUtilities::stripSystemTags($description, $metadataTag);

        if ($replace)
        {
            $description = SCStringUtilities::trimNBSP($description);
            $doc->setDescription($description);
        }
    }

    private function buildListOfTagsToReplace()
    {
        $tagsToReplace = JFBCFactory::cache()->get('system.alleasytags');
        if ($tagsToReplace === false)
        {
            $providers = JFBCFactory::getAllWidgetProviderNames();
            $tagsToReplace = array();
            foreach ($providers as $provider)
            {
                $widgets = JFBCFactory::getAllWidgets($provider);
                foreach ($widgets as $widget)
                {
                    $tagsToReplace[strtolower($widget->tagName)] = array('provider' => $provider, 'widget' => $widget->systemName);
                }
            }
            //Manually add SCLinkedinLogin, since JLinkedLogin is the actual tag
            $tagsToReplace['sclinkedinlogin'] = array('provider' => 'linkedin', 'widget' => 'login');

            //Tags like JFBCShare and JFBCRecommendations need to come up after JFBCShareDialog and JFBCRecommendationsBar
            $tagsToReplace = array_reverse($tagsToReplace);
            JFBCFactory::cache()->store($tagsToReplace, 'system.alleasytags');
        }
        $this->tagsToReplace = $tagsToReplace;
    }

    private function doTagReplacements()
    {
        /*
         * Code to strip any {JFBCxyz} tags from head.
         */
        //Get the head
        $content = JResponse::getBody();
        $regex = '|<head(.*)?</head>|sui';
        if (preg_match($regex, $content, $matches))
        {
            if (count($matches) == 2) // more than one head is a problem, don't do anything
            {
                //Remove the tag if it's in the head
                $newHead = preg_replace('/\{(SC|JFBC|JLinked)(.*?)}/ui', '', $matches[0], -1, $count);

                if ($count > 0)
                {
                    //Replace the head
                    $content = preg_replace('|<head(.*)?</head>|sui', $newHead, $content, -1, $count);
                    if ($count == 1) // Only update the body if exactly one head was found and replaced
                        JResponse::setBody($content);
                }
            }
        }

        $this->replaceTags();

        $this->replaceGraphTags();
        $this->replaceJSPlaceholders();
    }

    private function replaceJSPlaceholders()
    {
        $contents = JResponse::getBody();
        $javascript = '';

        $providers = JFBCFactory::getAllProviders();
        foreach ($providers as $provider)
        {
            $javascript .= $provider->getHeadData();
        }

        $pinterestWidgets = JFBCFactory::getAllWidgets('pinterest');
        $javascript .= $pinterestWidgets[0]->getHeadData();
        JFBConnectProviderPinterestWidgetShare::$needsJavascript = false;

        $contents = str_replace('<SourceCoastProviderJSPlaceholder />', $javascript, $contents);

        JResponse::setBody($contents);
    }

    private function replaceTags()
    {
        //Tag like {JFBCTag} {JLinkedTag} {SCTag} {JFBCTag field=value field2=value2} {JLinkedTag field=value field2=value2} {SCTag field=value field2=value2}
        $regex = '/\{(SC|JFBC|JLinked)(.*?)}/i';

        $replace = FALSE;
        $contents = JResponse::getBody();
        if (preg_match_all($regex, $contents, $matches, PREG_SET_ORDER))
        {
            $count = count($matches[0]);
            if ($count == 0)
                return true;

            $jfbcRenderKey = JFBCFactory::config()->get('social_tag_admin_key');

            foreach ($matches as $match)
            {
                $tagFields = explode(' ',$match[2]);
                $method = strtolower($match[1]) . strtolower($tagFields[0]);
                unset($tagFields[0]);
                $val = implode(' ', $tagFields);

                $params = SCEasyTags::_splitIntoTagParameters($val);
                $cannotRender = SCEasyTags::cannotRenderEasyTag($params, $jfbcRenderKey);
                if ($cannotRender)
                    continue;

                if (array_key_exists($method, $this->tagsToReplace))
                {
                    $widgetInfo = $this->tagsToReplace[$method];
                    $fields = SCEasyTags::getTagParameters($params);
                    $widget = JFBCFactory::widget($widgetInfo['provider'], $widgetInfo['widget'], $fields);
                    $newText = $widget->render();
                    $replace = TRUE;
                }
                else
                {
                    $newText = '';
                }

                $search = '/' . preg_quote($match[0], '/') . '/';
                $contents = preg_replace($search, $newText, $contents, 1);

            }
            if ($replace)
                JResponse::setBody($contents);
        }

        return $replace;
    }

    private function getGraphContents($regex, &$contents, &$newGraphTags)
    {
        if (preg_match_all($regex, $contents, $matches, PREG_SET_ORDER))
        {
            $count = count($matches[0]);
            if ($count == 0)
                return true;

            $jfbcRenderKey = JFBCFactory::config()->get('social_tag_admin_key');

            foreach ($matches as $match)
            {
                if (isset($match[1]))
                    $val = $match[1];
                else
                    $val = '';

                $params = SCEasyTags::_splitIntoTagParameters($val);
                $cannotRenderJFBC = SCEasyTags::cannotRenderEasyTag($params, $jfbcRenderKey);

                if ($cannotRenderJFBC)
                    continue;

                $val = $this->removeRenderKey($val, $jfbcRenderKey);
                $newGraphTags[] = $val;
                $contents = str_replace($match[0], '', $contents);
            }
        }
    }

    private function removeRenderKey($easyTag, $renderKey)
    {
        if ($renderKey != '')
        {
            $key = 'key=' . $renderKey;
            $easyTag = str_ireplace($key . ' ', '', $easyTag); //Key with blank space
            $easyTag = str_ireplace($key, '', $easyTag);
            $easyTag = SCStringUtilities::trimNBSP($easyTag);
        }
        return $easyTag;
    }

    private function replaceGraphTags()
    {
        $placeholder = '<SCOpenGraphPlaceholder />';
        $regex1 = '/\{SCOpenGraph\s+(.*?)\}/i';
        $regex2 = '/\{JFBCGraph\s+(.*?)\}/i';

        $newGraphTags1 = array();
        $newGraphTags2 = array();

        $contents = JResponse::getBody();
        $this->getGraphContents($regex1, $contents, $newGraphTags1);
        $this->getGraphContents($regex2, $contents, $newGraphTags2);

        $newGraphTags = array_merge($newGraphTags1, $newGraphTags2);
        //Replace Placeholder with new Head tags
        $defaultGraphFields = JFBCFactory::config()->getSetting('social_graph_fields');
        $locale = JFBCFactory::provider('facebook')->getLocale();

        $openGraphLibrary = OpenGraphLibrary::getInstance();
        $openGraphLibrary->addOpenGraphEasyTags($newGraphTags);
        $openGraphLibrary->addDefaultSettingsTags($defaultGraphFields);
        $openGraphLibrary->addAutoGeneratedTags($locale);
        $graphTags = $openGraphLibrary->buildCompleteOpenGraphList();

        $contents = $openGraphLibrary->removeOverlappingTags($contents);
        $search = '/' . preg_quote($placeholder, '/') . '/';
        $graphTags = str_replace('$', '\$', $graphTags);
        $contents = preg_replace($search, $graphTags, $contents, 1);
        $contents = str_replace($placeholder, '', $contents); //If JLinked attempts to insert, ignore
        JResponse::setBody($contents);
    }
}
