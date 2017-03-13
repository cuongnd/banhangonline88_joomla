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

jimport('joomla.filesystem.file');
jimport('joomla.user.helper');
jimport('sourcecoast.easyTags');

define('CHECK_NEW_MAPPING_JFBCONNECT', 'jfbcCheckNewMapping');
define('LOGIN_TASK_JFBCONNECT', 'loginFacebookUser');

define('AUTONAME_EXT', '0');
define('AUTONAME_FIRSTLAST', '1');
define('AUTONAME_FIRLAS', '2');
define('AUTONAME_EMAIL', '3');

define('EXT_SOURCECOAST', 'sourcecoast');
define('EXT_JFBCONNECT', 'jfbconnect');

class SCSocialUtilities
{
    static function isJFBConnectInstalled()
    {
        return SCSocialUtilities::isComponentEnabled('com_jfbconnect', JPATH_ROOT . '/components/com_jfbconnect/libraries/facebook.php');
    }

    static function isComponentEnabled($option, $libraryFile)
    {
        $isComponentEnabled = false;
        if (JFile::exists($libraryFile))
        {
            $isComponentEnabled = SCSocialUtilities::isJoomlaComponentEnabled($option);
        }
        return $isComponentEnabled;
    }

    static function isJoomlaComponentEnabled($option)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('extension_id AS id, element AS "option", params, enabled');
        $query->from('#__extensions');
        $query->where($query->qn('type') . ' = ' . $db->quote('component'));
        $query->where($query->qn('element') . ' = ' . $db->quote($option));
        $db->setQuery($query);

        $component = $db->loadObject();
        $isComponentEnabled = $component && $component->enabled;
        return $isComponentEnabled;
    }

    static function areJFBConnectTagsEnabled()
    {
        return JPluginHelper::isEnabled('system', 'jfbcsystem');
    }

    static function getExtraShareButtons($url, $dataCount, $showFacebookLikeButton, $showFacebookShareButton, $showTwitterButton, $showGooglePlusButton, $renderKeyString, $showLinkedInButton = false, $fbWidth = 50, $showPinterestButton = false, $pinnedImage = "", $pinnedText = "")
    {
        //Reset to FB value if from JLinked
        if ($dataCount == "top")
            $dataCount = 'box_count';
        else if ($dataCount == 'right')
            $dataCount = 'button_count';
        else if ($dataCount == 'none')
            $dataCount = 'button';

        $extraButtonText = '';

        if ($url == '')
            $url = SCSocialUtilities::getStrippedUrl();

        if ($showLinkedInButton)
        {
            $extraButtonText .= '{JLinkedShare href=' . $url . " layout=" . $dataCount . $renderKeyString . '}';
        }
        if ($showTwitterButton)
        {
            $extraButtonText .= '{SCTwitterShare href=' . $url . " layout=" . $dataCount . $renderKeyString . '}';
        }
        if ($showGooglePlusButton)
        {
            $extraButtonText .= '{SCGooglePlusOne href=' . $url . " layout=" . $dataCount . $renderKeyString . '}';
        }
        if ($showPinterestButton)
        {
            $extraButtonText .= '{SCPinterestShare href=' . $url . " layout=" . $dataCount . ' image=' . $pinnedImage . ' desc=' . $pinnedText . $renderKeyString . '}';
        }
        if ($showFacebookLikeButton)
        {
            $shareString = $showFacebookShareButton ? "true" : "false";
            $extraButtonText .= '{JFBCLike href=' . $url . " layout=" . $dataCount . ' share=' . $shareString . $renderKeyString . '}';
        }

        return $extraButtonText;
    }

    static function forceScheme($href)
    {
        $forceScheme = JFBCFactory::config()->get('social_force_scheme', 0);
        if ($forceScheme == '1')
            $href = str_replace('https://', 'http://', $href);
        else if ($forceScheme == '2')
            $href = str_replace('http://', 'https://', $href);
        return $href;
    }

    static function getStrippedUrl()
    {
        $href = SCSocialUtilities::forceScheme(JURI::current());

        $juri = JURI::getInstance();
        // Delete some common, unwanted query params to at least try to get at the canonical URL
        $juri->delVar('fb_comment_id');
        $juri->delVar('tp');
        $juri->delVar('notif_t');
        $juri->delVar('ref');
        $juri->delVar('utm_source');
        $juri->delVar('utm_medium');
        $juri->delVar('utm_campaign');
        $juri->delVar('utm_term');
        $juri->delVar('fb_source');
        $juri->delVar('fb_action_ids');
        $juri->delVar('fb_action_types');
        $juri->delVar('fb_aggregation_id');
        $query = $juri->getQuery();

        if ($query)
            $href .= '?' . $query;

        return $href;
    }

    static function stripSystemTags(&$description, $metadataTag)
    {
        $replace = false;

        //Full Match
        if (preg_match_all('/\{' . $metadataTag . '.*?\}/i', $description, $matches, PREG_SET_ORDER))
        {
            $replace = true;
            foreach ($matches as $match)
            {
                $description = str_replace($match, '', $description);
            }
        }
        //Partial Match
        if (preg_match('/\{' . $metadataTag . '+(.*?)/i', $description, $matches))
        {
            $replace = true;
            $trimPoint = strpos($description, '{' . $metadataTag);
            if ($trimPoint == 0)
                $description = '';
            else
                $description = substr($description, 0, $trimPoint);
        }

        return $replace;
    }

    static function setJFBCNewMappingEnabled()
    {
        $jfbcLibrary = JFBCFactory::provider('facebook');
        $session = JFactory::getSession();
        $session->set(CHECK_NEW_MAPPING_JFBCONNECT, true);

        $jfbcLibrary->checkNewMapping = true;
    }

    static function clearJFBCNewMappingEnabled()
    {
        $jfbcLibrary = JFBCFactory::provider('facebook');

        $session = JFactory::getSession();
        $session->clear(CHECK_NEW_MAPPING_JFBCONNECT);

        $jfbcLibrary->checkNewMapping = false;
    }

    static function getLinkFromMenuItem($itemId, $isLogout)
    {
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getItem($itemId);

        if ($item)
        {
            if ($item->type == 'url') //External menu item
            {
                $redirect = $item->link;
            }
            else if ($item->type == 'alias') //Alias menu item
            {
                $aliasedId = $item->params->get('aliasoptions');

                if ($isLogout && SCSocialUtilities::isMenuRegistered($aliasedId))
                    $link = 'index.php';
                else
                    $link = SCSocialUtilities::getLinkWithItemId($item->link, $aliasedId);
                $redirect = JRoute::_($link, false);
            }
            else //Regular menu item
            {
                if ($isLogout && SCSocialUtilities::isMenuRegistered($itemId))
                    $link = 'index.php';
                else
                    $link = SCSocialUtilities::getLinkWithItemId($item->link, $itemId);
                $redirect = JRoute::_($link, false);
            }
        }
        else
            $redirect = '';

        return $redirect;
    }

    static function getLinkWithItemId($link, $itemId)
    {
        $app = JFactory::getApplication();
        $router = $app->getRouter();

        if ($link)
        {
            if ($router->getMode() == JROUTER_MODE_SEF)
                $url = 'index.php?Itemid=' . $itemId;
            else
                $url = $link . '&Itemid=' . $itemId;
        }
        else
            $url = '';

        return $url;
    }

    static function isMenuRegistered($menuItemId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__menu WHERE id=" . $db->quote($menuItemId);
        $db->setQuery($query);
        $menuItem = $db->loadObject();
        return ($menuItem && $menuItem->access != "1");
    }

    static function getRemoteContent($url)
    {
        // Parts of this function inspired by JomSocial's implementation (c) Slashes 'n Dots azrul.com
        if (!$url)
            return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);

        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);

        if ($curl_errno != 0)
        {
            // Find a better way to show errors only when reporting is enabled
            /*            $mainframe = JFactory::getApplication();
                        $err = 'CURL error : ' . $curl_errno . ' ' . $curl_error;
                        $mainframe->enqueueMessage($err, 'error');*/
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // TODO: Add recursion allowing for more redirects? Unsure if multi redirects would happen...
        if ($code == 301 || $code == 302)
        {
            list($headers, $body) = explode("\r\n\r\n", $response, 2);

            preg_match("/(Location:|URI:)(.*?)\n/", $headers, $matches);

            if (!empty($matches) && isset($matches[2]))
            {
                $url = JString::trim($matches[2]);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, true);
                $response = curl_exec($ch);
            }
        }

        list($headers, $body) = explode("\r\n\r\n", $response, 2);

        curl_close($ch);
        return $body;
    }

    static function getAffiliateLink($affiliateID, $extension)
    {
        if ($extension == EXT_JFBCONNECT)
        {
            $defaultLink = 'http://www.sourcecoast.com/joomla-facebook/';
            $textLinkId = '495360';
        }
        else //SourceCoast
        {
            $defaultLink = 'http://www.sourcecoast.com/';
            $textLinkId = '495362';
        }

        if ($affiliateID)
            return 'http://www.shareasale.com/r.cfm?b=' . $textLinkId . '&u=' . $affiliateID . '&m=46720&urllink=&afftrack=';
        else
            return $defaultLink;
    }
}

class SCStringUtilities
{
    static function endswith($string, $test)
    {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen)
            return false;
        return substr_compare($string, $test, -$testlen) === 0;
    }

    static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    static function trimNBSP($htmlText)
    {
        // turn some HTML with non-breaking spaces into a "normal" string
        //$converted = htmlentities(strip_tags($htmlText), ENT_QUOTES, 'UTF-8');
        //$converted = trim(str_replace('&nbsp;', ' ', $converted));

        //Delete HTML for br's and nbsp's. Joomla inserts funky characters in the editor - chr(0xC2).chr(0xA0)
        $spaces = array('<br/>', '<br />', '&nbsp;', chr(0xC2).chr(0xA0));
        $htmlText = trim(str_replace($spaces, ' ', $htmlText));
        return $htmlText;
    }

    static function replaceNBSPWithSpace($htmlText)
    {
        $htmlText = strip_tags($htmlText);
        $htmlText = htmlentities(html_entity_decode($htmlText), ENT_QUOTES, 'UTF-8');
        $htmlText = trim(str_replace('&nbsp;', ' ', $htmlText));
        return $htmlText;
    }

    //Recursively implode and trim an array (of strings/arrays)
    static function r_implode($glue, $pieces)
    {
        foreach ($pieces as $r_pieces)
        {
            if (is_array($r_pieces))
            {
                unset($r_pieces['id']); // Remove id key which is useless to import
                $retVal[] = SCStringUtilities::r_implode($glue, $r_pieces);
            }
            else
            {
                $retVal[] = trim($r_pieces);
            }
        }
        return implode($glue, $retVal);
    }

    static function substr($str, $start, $length, $encoding = 'UTF-8')
    {
        if (function_exists('mb_substr'))
            $str = mb_substr($str, $start, $length, $encoding);
        else
            $str = substr($str, $start, $length);

        return $str;
    }

    static function strtolower($str)
    {
        if (function_exists('mb_strtolower'))
            $str = mb_strtolower($str);
        else
            $str = strtolower($str);
        return $str;
    }

    /**
     * Loads the translation strings -- this is an internal function, called automatically
     */
    static function loadLanguage($extension, $basePath = JPATH_BASE)
    {
        // Load translations
        $jlang = JFactory::getLanguage();
        $jlang->load($extension, $basePath, 'en-GB', true); // Load English (British)
        // $jlang->load($extension, JPATH_BASE, $jlang->getDefault(), true); // Load the site's default language
        $jlang->load($extension, $basePath, null, true); // Load the currently selected language
    }
}

class SCUserUtilities
{
    static function getAutoUsername($firstName, $lastName, $email, $genericPrefixStr, $socialUserId, $usernamePrefixFormat)
    {
        // Scrub the first/last name for invalid characters that Joomla will deny:
        $stripChars = array('<', '>', '\\', '"', "'", '%', ';', '(', ')', '&');
        $firstName = str_replace($stripChars, '', $firstName);
        $lastName = str_replace($stripChars, '', $lastName);

        if ($usernamePrefixFormat == AUTONAME_EXT) //fb_ or li_
        {
            return $genericPrefixStr . $socialUserId;
        }
        else if ($usernamePrefixFormat == AUTONAME_FIRSTLAST) //first.last
        {
            $firstName = SCStringUtilities::strtolower($firstName);
            $lastName = SCStringUtilities::strtolower($lastName);
            if ($lastName != '')
                $prefix = $firstName . "." . $lastName;
            else
                $prefix = $firstName;
        }
        else if ($usernamePrefixFormat == AUTONAME_FIRLAS) //firlas
        {
            $firstName = SCStringUtilities::strtolower($firstName);
            $lastName = SCStringUtilities::strtolower($lastName);

            // Try to always make usernames at least 6 characters

            $lastLength = strlen(utf8_decode($lastName));
            $firstLength = strlen(utf8_decode($firstName));

            $firstPrefix = SCStringUtilities::substr($firstName, 0, max(3, 6 - $lastLength));
            $lastPrefix = SCStringUtilities::substr($lastName, 0, max(3, 6 - $firstLength));
            $prefix = $firstPrefix . $lastPrefix;
        }
        else if ($email != '') //email
        {
            $prefix = $email;
        }

        if ($prefix != '')
        {
            $suffix = SCUserUtilities::getUsernameUniqueNumber($prefix);
            return $prefix . $suffix;
        }
        else
            return '';
    }

    static function getUsernameUniqueNumber($prefix)
    {
        $dbo = JFactory::getDBO();
        // First, check if any user has this name
        $query = $dbo->getQuery(true);
        $query->select('COUNT(*)')
                ->from($dbo->qn('#__users'))
                ->where($dbo->qn('username') . '=' . $dbo->q($prefix));
        $dbo->setQuery($query);
        $count = $dbo->loadResult();
        if ($count == 0)
            $suffix = "";
        else
        {
            // Get a very strict match to see the last similar username
            if ($dbo->name == "postgresql")
                $query = "SELECT CAST(REPLACE(username, " . $dbo->quote($prefix) . ", '') AS INT) suffix FROM #__users WHERE " . $dbo->qn('username') . " ~ '^" . $prefix . "[0-9]+$' ORDER BY " . $dbo->qn('suffix') . " DESC LIMIT 1";
            else
                $query = 'SELECT CAST(REPLACE(username, ' . $dbo->quote($prefix) . ', "") AS UNSIGNED) suffix FROM #__users WHERE `username` REGEXP "^' . $prefix . '[[:digit:]]+$" ORDER BY `suffix` DESC LIMIT 1';
            $dbo->setQuery($query);
            $suffix = $dbo->loadResult();
            if ($suffix)
            { # increment the last user's number
                $suffix++;
            }
            else
                $suffix = 1;
        }
        return $suffix;
    }

    static function getPostData($postData, $setting)
    {
        $postDataValue = '';
        if (array_key_exists('jform', $postData))
            $postDataValue = $postData['jform'][$setting];
        return $postDataValue;
    }

    static function getDisplayUsername($postData, $firstName, $lastName, $email, $liMemberId, $configModel, $loginRegisterModel,
                                       $generateUsernameSetting = 'registration_generate_username', $autoUsernameSetting = 'auto_username_format')
    {
        $postUsername = SCUserUtilities::getPostData($postData, 'username');
        if ($postUsername != '')
        {
            $liUsername = $postUsername;
        }
        else if ($configModel->getSetting($generateUsernameSetting))
        {
            $usernamePrefixFormat = $configModel->getSetting($autoUsernameSetting);
            $liUsername = $loginRegisterModel->generateUsername($firstName, $lastName, $email, $liMemberId, $usernamePrefixFormat);
        }
        else
        {
            $liUsername = '';
        }
        return $liUsername;
    }

    static function getDisplayNameByFullName($postData, $name)
    {
        $postName = SCUserUtilities::getPostData($postData, 'name');
        if ($postName != '')
        {
            $liName = $postName;
        }
        else
        {
            $liName = $name;
        }

        return $liName;
    }

    static function getDisplayNameByFirstLast($postData, $firstName, $lastName)
    {
        $postName = SCUserUtilities::getPostData($postData, 'name');
        if ($postName != '')
        {
            $liName = $postName;
        }
        else
        {
            $liName = $firstName . ' ' . $lastName;
        }

        return $liName;
    }

    /**
     * Check passed in email to see if it's already in Joomla
     * If so, return blank, forcing the user to input an email address (and getting validation error if using the same)
     * If not, pre-populate the form with the user's FB address
     * @param string $email Users email address
     * @param string $email1 Returns user's email 1
     * @param string $email2 Returns user's email 2
     * @return string Email value that will be shown on registration form
     */
    static function getDisplayEmail($postData, $email, &$email1, &$email2)
    {
        $postEmail1 = SCUserUtilities::getPostData($postData, 'email1');
        $postEmail2 = SCUserUtilities::getPostData($postData, 'email2');

        if ($postEmail1 != '' || $postEmail2 != '')
        {
            $email = $postEmail1;
            $email1 = $postEmail1;
            $email2 = $postEmail2;
        }

        $dbo = JFactory::getDBO();
        $query = "SELECT id FROM #__users WHERE email=" . $dbo->quote($email);
        $dbo->setQuery($query);
        $jEmail = $dbo->loadResult();
        if ($jEmail != null)
        {
            $email1 = "";
            $email2 = "";
        }
        else
        {
            $email1 = $email;
            $email2 = $email;
        }
    }
}