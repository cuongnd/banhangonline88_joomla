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

jimport('sourcecoast.utilities');

define('SOURCECOAST_JFBCONNECT', 'JFBConnect');
define('SOURCECOAST_JLINKED', 'JLinked');
define('CARRIAGE_RETURN', chr(13));
define('PRIORITY_HIGH', 2);
define('PRIORITY_NORMAL', 1);
define('PRIORITY_LOW', 0);

class OpenGraphLibrary
{
    private static $libraryInstance;

    private $openGraphTags; //array of OpenGraphTag objects that need to be added to content
    private $skippedGraphTags; //array of OpenGraphTag objects that we have not added, since an isFinal was encountered
    private $overlappingTags; //array of OpenGraphTag objects that were encountered from other extensions and removed

    function __construct()
    {
        $this->openGraphTags = array();
        $this->skippedGraphTags = array();
        $this->overlappingTags = array();
    }

    public function __get($name)
    {
        switch ($name)
        {
            case 'tagsInserted':
                return $this->openGraphTags;
            case 'tagsSkipped' :
                return $this->skippedGraphTags;
            case 'tagsRemoved' :
                return $this->overlappingTags;
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$libraryInstance))
        {
            self::$libraryInstance = new OpenGraphLibrary();
        }

        return self::$libraryInstance;
    }

    public function buildCompleteOpenGraphList()
    {
        $openGraphTags = '';

        foreach ($this->openGraphTags as $ogTag)
        {
            $openGraphTags .= $ogTag->toString();
        }

        return $openGraphTags;
    }

    public function removeOverlappingTags($contents)
    {
        foreach ($this->openGraphTags as $ogTag)
        {
            if (!$ogTag->allowsMultiple && get_class($ogTag) != 'SkippedOpenGraphTag')
            {
                // Removed the /s switch from the grep in v5.1.0 below to prevent multi-line scanning
                // Maybe add back later, but want to be more careful since we now look for /> instead of just >

                $regex = '/<meta (property|name)="' . $ogTag->name . '".+?\/>/i';
                $overlappingFound = preg_match($regex, $contents, $matches);
                $contents = preg_replace($regex, '', $contents);

                if ($overlappingFound)
                {
                    $openGraphTag = new OpenGraphTag($ogTag->name, "", false, $ogTag->priority, '3rd Party Extension');
                    $this->overlappingTags[] = $openGraphTag;
                }
            }
        }
        return $contents;
    }

    /*
     * This method adds a tag to the list of all open graph tags
     * we will eventually add to the page content.
     *
     * - Once isFinal is true, if we encounter this again, do not use new value
     * - If isFinal is false, keep updating value (unless multiple values are accepted)
     *
     * If {SCOpenGraph} is found, then use if isOverrideable is true
     * - content plugin - auto-generate description
     * - users add this manually to articles, etc
     */
    public function addOpenGraphTag($name, $value, $isFinal, $priority = PRIORITY_NORMAL, $origin = 'jfbconnect')
    {
        $isUsingNewValue = true;

        $name = OpenGraphUtilities::getTagName($name);

        if ($value == '' || !$value)
        {
            $isUsingNewValue = false;
        }
        else if (array_key_exists($name, $this->openGraphTags))
        {
            $existingTag = $this->openGraphTags[$name];

            if ($existingTag->allowsMultiple) // Just add value, disregards isFinal
            {
                $this->openGraphTags[$name]->addValue($value, $isFinal, $priority);
            }
            else if ($existingTag->isFinal) // We are not adding it
            {
                $this->addSkippedTag($name, $value, $isFinal, $priority, $origin);
                $isUsingNewValue = false;
            }
            else // Reset the value. Does not create new object to replace?
            {
                $existingTag = $this->openGraphTags[$name];
                $this->addSkippedTag($name, $existingTag->value[0], false, $priority, $existingTag->origin);
                $this->openGraphTags[$name]->setValues($value, $isFinal, $priority);
                $this->openGraphTags[$name]->origin = $origin;
            }
        }
        else // Does not exist, so just add it
        {
            $openGraphTag = new OpenGraphTag($name, $value, $isFinal, $priority, $origin);
            $this->openGraphTags[$name] = $openGraphTag;
        }

        return $isUsingNewValue;
    }

    /*
    * This method will add the tag so it is skipped when coming up with the
    * final list of OpenGraph tags to add. This should be called from the
    * plugins and only used when we "trust" that the component is adding og tags
    * correctly
    *
    * - Reset as skipped object if a regular one already exists.
    * - If it does not exist yet, then add it as final and it will be skipped later
    */
    public function skipOpenGraphTag($name, $origin)
    {
        $name = OpenGraphUtilities::getTagName($name);
        $this->openGraphTags[$name] = new SkippedOpenGraphTag($name, "Skipped", true, PRIORITY_HIGH, $origin);
        return true;
    }

    private function addDefaultGraphTag($name, $value, $priority)
    {
        if (!array_key_exists($name, $this->openGraphTags))
        {
            $this->addOpenGraphTag($name, $value, false, $priority, 'Auto-generated');
        }
        else
        {
            $this->addSkippedTag($name, $value, false, $priority, 'Auto-generated');
        }
    }

    private function addSkippedTag($name, $value, $isFinal, $priority, $origin)
    {
        $openGraphTag = new OpenGraphTag($name, $value, $isFinal, $priority, $origin);
        $this->skippedGraphTags[] = $openGraphTag;
    }

    /*
     * Expects a list of "key=value" strings. Adds them into the list as non-final
     * Should handle {SCOpenGraph} tags first before automatically generating. They have priority.
     */
    public function addOpenGraphEasyTags($easyTagsList)
    {
        foreach ($easyTagsList as $graphField)
        {
            $this->addTagFromString($graphField, false, PRIORITY_HIGH, 'EasyTag');
        }
    }

    /*
     * Expects a default list of carriage-returned fields that are "key=value" format
     */
    public function addDefaultSettingsTags($defaultSettingsTagList)
    {
        $fields = explode(CARRIAGE_RETURN, $defaultSettingsTagList);
        foreach ($fields as $graphField)
        {
            $this->addTagFromString($graphField, true, PRIORITY_LOW, 'Default Setting');
        }
    }

    public function addAutoGeneratedTags($locale)
    {
        $url = OpenGraphUtilities::getCurrentUrlTag();
        $this->addDefaultGraphTag('og:url', $url, PRIORITY_NORMAL);

        $title = OpenGraphUtilities::getCurrentPageTitleTag();
        $this->addDefaultGraphTag('og:title', $title, PRIORITY_NORMAL);

        $desc = OpenGraphUtilities::getCurrentDescriptionTag();
        $this->addDefaultGraphTag('og:description', $desc, PRIORITY_NORMAL);

        $type = OpenGraphUtilities::getCurrentPageTypeTag();
        $this->addDefaultGraphTag('og:type', $type, PRIORITY_NORMAL);

        $appId = OpenGraphUtilities::getCurrentAppId();
        $this->addDefaultGraphTag('fb:app_id', $appId, PRIORITY_NORMAL);

        $locale = OpenGraphUtilities::getCurrentLocaleTag($locale);
        $this->addDefaultGraphTag('og:locale', $locale, PRIORITY_NORMAL);

        $siteName = OpenGraphUtilities::getCurrentSiteNameTag();
        $this->addDefaultGraphTag('og:site_name', $siteName, PRIORITY_NORMAL);
    }

    private function addTagFromString($graphFieldStr, $isDefault, $priority, $origin)
    {
        $keyValue = explode('=', $graphFieldStr, 2);
        if (count($keyValue) == 2)
        {
            $graphName = strtolower(trim($keyValue[0]));
            $graphValue = SCStringUtilities::trimNBSP($keyValue[1]);
            //$graphValue = trim($keyValue[1]);

            if ($isDefault)
                $this->addDefaultGraphTag($graphName, $graphValue, $priority);
            else
                $this->addOpenGraphTag($graphName, $graphValue, false, $priority, $origin);
        }
    }
}

class OpenGraphTag
{
    var $name;
    var $value;
    var $isFinal;
    var $allowsMultiple;
    var $priority;
    var $origin;

    function __construct($name, $value, $isFinal, $priority, $origin)
    {
        $this->isFinal = $isFinal;
        $this->name = OpenGraphUtilities::getTagName($name);
        $this->priority = $priority;
        $this->origin = $origin;
        $this->addValue($value, $isFinal, $priority);

        // og:image and any custom namespace tags are allowed multiple times
        $this->allowsMultiple = ($name == 'og:image') ||
                (strpos($name, 'og:') === false && strpos($name, 'fb:') === false);
    }

    function addValue($value, $isFinal, $priority)
    {
        $cleanValue = $this->getCleanValue($value);
        if ($priority == PRIORITY_HIGH && is_array($this->value))
        {
            array_unshift($this->value, $cleanValue); //prepend high priority items
        }
        else
        {
            $this->value[] = $cleanValue;
        }
        $this->isFinal = $isFinal;
    }

    function setValues($value, $isFinal, $priority)
    {
        if (count($this->value) == 1 && $this->priority < $priority)
        {
            $this->priority = $priority;
            $this->value[0] = $this->getCleanValue($value);
            $this->isFinal = $isFinal;
        }
    }

    function toString()
    {
        $graphValue = '';

        foreach ($this->value as $tagValue)
        {
            // Basic Twitter Card support
            if($this->name == 'og:title')
                $graphValue .= '<meta name="twitter:title" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;
            else if($this->name == 'og:type')
                $graphValue .= '<meta name="twitter:card" content="summary"/>' . CARRIAGE_RETURN;
            else if($this->name == 'og:description')
                $graphValue .= '<meta name="twitter:description" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;
            else if($this->name == 'og:image' && strpos($graphValue, 'og:image')===false)
                $graphValue .= '<meta name="twitter:image" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;

            //Add twitter first, so only first image is added to twitter
            $graphValue .= '<meta property="' . $this->name . '" content="' . $tagValue . '"/>' . CARRIAGE_RETURN;
        }

        return $graphValue;
    }

    private function getCleanValue($value)
    {
        $value = trim($value);
        $value = str_replace('"', "'", $value);
        return $value;
    }
}

class SkippedOpenGraphTag extends OpenGraphTag
{
    function __construct($name, $value, $isFinal, $priority, $origin)
    {
        parent::__construct($name, $value, true, PRIORITY_HIGH, $origin);
        $this->allowsMultiple = false;
    }

    function toString()
    {
        return '';
    }
}

class OpenGraphUtilities
{
    /**
     * @return bool
     */
    public static function isHomepage()
    {
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        return ($menu->getActive() == $menu->getDefault());
    }

    public static function getTagName($name)
    {
        $name = strtolower(trim($name));
        if (strpos($name, 'ns:') === 0)
        {
            $appConfig = JFBCFactory::config()->get('autotune_app_config');
            $namespace = $appConfig['namespace'];
            $name = $namespace . ":" . $name;
            $name = str_replace($namespace . ":ns:", $namespace . ":", $name);
        }

        if (strpos($name, ':') === false)
        {
            if ($name == 'admins' || $name == 'app_id')
                $name = 'fb:' . $name;
            else
                $name = 'og:' . $name;
        }
        return $name;
    }

    public static function getCurrentUrlTag()
    {
        $url = SCSocialUtilities::getStrippedUrl();
        return $url;
    }

    public static function getCurrentPageTitleTag()
    {
        $doc = JFactory::getDocument();
        $title = $doc->getTitle();
        return $title;
    }

    public static function getCurrentDescriptionTag()
    {
        $doc = JFactory::getDocument();
        $desc = $doc->getDescription();
        return $desc;
    }

    public static function getCurrentPageTypeTag()
    {
        $isHomePage = OpenGraphUtilities::isHomepage();
        if ($isHomePage)
            $type = "website";
        else
            $type = "article";
        return $type;
    }

    public static function getCurrentAppId()
    {
        if (SCSocialUtilities::areJFBConnectTagsEnabled())
        {
            $appId = JFBCFactory::provider('facebook')->appId;
            if ($appId != '')
            {
                return $appId;
            }
        }
        return '';
    }

    public static function getCurrentLocaleTag($locale)
    {
        $locale = strtolower($locale);
        return $locale;
    }

    public static function getCurrentSiteNameTag()
    {
        $config = JFactory::getConfig();
        $siteName = $config->get("sitename");
        return $siteName;
    }
}