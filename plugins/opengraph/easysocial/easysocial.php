<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.articleContent');
jimport('sourcecoast.openGraphPlugin');

include_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

class plgOpenGraphEasySocial extends OpenGraphPlugin
{
    protected function init()
    {
        $this->extensionName = "EasySocial";
        $this->supportedComponents[] = 'com_easysocial';

        // Default Open Graph tags are set for profile, album, and group pages even if the admin hasn't defined object types for those pages
        $this->setsDefaultTags = true;

        // Define all types of pages this component can create and would be 'objects'
        $this->addSupportedObject("Photo", "photos");
        $this->addSupportedObject("Album", "albums");
        $this->addSupportedObject("Profile", "profile");

        // Add actions that aren't passive (commenting, voting, etc).
        // Things that trigger just by loading the page should not be defined here unless extra logic is required
        // ie. Don't define reading an article
        $this->addSupportedAction("Album Image Upload", "image_upload");
    }

    protected function findObjectType($queryVars)
    {
        // Setup Object type for page
        $layout = array_key_exists('layout', $queryVars) ? $queryVars['layout'] : '';
        $view = array_key_exists('view', $queryVars) ? $queryVars['view'] : '';

        $objectTypes = $this->getObjects($view);
        $object = null;

        if ($view == 'profile' ||
            ($view == 'photos' && $layout == 'item') ||
            ($view == 'albums' && $layout == "item")
        )
        {
            // If there's an object, that's the one we want since we don't current support multiple profile types
            if (array_key_exists('0', $objectTypes))
                $object = $objectTypes[0];
        }
        return $object;
    }

    protected function setOpenGraphTags()
    {
        $view = JRequest::getCmd('view');
        $layout = JRequest::getCmd('layout');

        if ($view == 'profile')
        {
            // Set image from profile. Title and description are done by JFBConnect automatically
            // Get EasySocial user object
            $juser = JFactory::getUser();
            $joomlaId = $juser->id;
            $user = Foundry::user($joomlaId);

            if($user->avatars['medium'])
            {
                $avatarUrl = JURI::root().'media/com_easysocial/users/'.$joomlaId.'/'.$user->avatars['medium'];
                $this->addOpenGraphTag('image', $avatarUrl, false);
            }

            // Add the canonical URL to the profile
            $uri = JURI::getInstance();
            $url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $url .= JRoute::_('index.php?option=com_easysocial&view=profile&id=' . $joomlaId, false);
            $this->addOpenGraphTag('url', $url, true);
        }
        else if ($view == 'albums' && $layout == 'item') //Album
        {
            $albumId = JRequest::getInt('id');
            $album = Foundry::albums($albumId);
            /*
                        // EasySocial currently adds og:image tags to the page already
                        if($album->data->hasCover())
                        {
                            $image = $album->data->getCover();
                            $imageUrl = $image->getPath('original');
                            $this->addOpenGraphTag('image', $imageUrl, false);
                        }
            */
            if(isset($album->data->caption))
                $this->addOpenGraphTag('description', $album->data->caption, false);
            else if(isset($album->data->title))
                $this->addOpenGraphTag('description', $album->data->title, false);
        }
        else if ($view == 'photos' && $layout == 'item') //Individual Photo
        {
            $photoId = JRequest::getInt('id');
            $photo = Foundry::photo($photoId);
            $this->addOpenGraphTag('description', $photo->data->caption, false);

        }

    }

    /************* DEFINED ACTIONS CALLS *******************/

}