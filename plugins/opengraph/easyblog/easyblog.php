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

class plgOpenGraphEasyBlog extends OpenGraphPlugin
{
    protected function init()
    {
        $this->extensionName = "EasyBlog";
        $this->supportedComponents[] = 'com_easyblog';
        $this->setsDefaultTags = true;

        // Define all types of pages this component can create and would be 'objects'
        $this->addSupportedObject("Blog Post", "post");
//        $this->addSupportedObject("Category", "category");

        // Add actions that aren't passive (commenting, voting, etc).
        // Things that trigger just by loading the page should not be defined here unless extra logic is required
        // ie. Don't define reading an article
        $this->addSupportedAction("Rate", "rate");
        $this->addSupportedAction("Comment", "comment");
    }

    protected function findObjectType($queryVars)
    {
        // Setup Object type for page
        $view = array_key_exists('view', $queryVars) ? $queryVars['view'] : '';
        $object = null;
        if ($view == 'entry')
        {
            require_once(JPATH_SITE . '/components/com_easyblog/helpers/helper.php');
            $objectTypes = $this->getObjects('post');
            $blog = EasyBlogHelper::getTable('Blog', 'Table');
            if ($blog->load($queryVars['id']))
            {
                $catId = $blog->category_id;
                $object = $this->getBestCategory($objectTypes, $catId);
            }
        }
        return $object;
    }

    private function getBestCategory($objectTypes, $catId)
    {
        $object = null;
        if ($objectTypes)
        {
            $bestDistance = 99999;
            $this->db->setQuery("SELECT lft, rgt FROM #__easyblog_category WHERE id = " . $catId);
            $catLoc = $this->db->loadObject();
            foreach ($objectTypes as $type)
            {
                $this->db->setQuery("SELECT lft, rgt FROM #__easyblog_category WHERE id = " . $type->params->get('category'));
                $result = $this->db->loadObject();
                if ($result->lft <= $catLoc->lft && $result->rgt >= $catLoc->rgt)
                {
                    $distance = $result->rgt - $result->lft;
                    if ($distance < $bestDistance)
                        $object = $type;
                    if ($distance == 1)
                        break;
                }
            }
        }
        return $object;
    }

    protected function setOpenGraphTags()
    {
        $desc = ''; //Note: meta is same as blank value, since system plugin attempts to generate from metadescription if no value is found
        $image = '';

        $view = JRequest::getCmd('view');

        if($this->object)
        {
            $desc_type = $this->object->params->get('custom_desc_type');
            $desc_length = $this->object->params->get('custom_desc_length');
            $image_type = $this->object->params->get('custom_image_type');
            $image_path = $this->object->params->get('custom_image_path');
        }
        else
        {
            $desc_type = 'custom_desc_introwords';
            $desc_length = '50';
            $image_type = 'custom_image_first';
            $image_path = '';
        }

        if ($view == 'entry')
        {
            require_once(JPATH_SITE . '/components/com_easyblog/helpers/helper.php');
            $item = EasyBlogHelper::getTable('Blog', 'Table');
            $item->load(JRequest::getInt('id'));

            $this->addOpenGraphTag('title', $item->title, false);

            $itemText = trim(strip_tags($item->intro)) . ' ' . trim(strip_tags($item->content));
            if ($desc_type == 'custom_desc_introwords')
                $desc = $this->getSelectedText($itemText, SC_INTRO_WORDS, $desc_length);
            else if ($desc_type == 'custom_desc_introchars')
                $desc = $this->getSelectedText($itemText, SC_INTRO_CHARS, $desc_length);
            $this->addOpenGraphTag('description', $desc, false);

            //if ($image_type == 'custom_image_item')
            //{
            $image = $this->getEasyBlogMainImage($item);
            //}
            if ($image_type == 'custom_image_first' || $image == '')
            {
                $tmpImage = $this->getFirstImageFromText($itemText);
                if($tmpImage != '')
                    $image = $tmpImage;
            }
            if ($image_type == 'custom_image_category' || $image == '')
            {
                $tmpImage = $this->getEasyBlogCategoryImage($item->category_id);
                if($tmpImage != '')
                    $image = $tmpImage;
            }
            if (($image_type == 'custom_image_custom' || $image == '') && $image_path != '')
            {
                $image = $image_path;
            }
            $this->addOpenGraphTag('image', $image, false);

            /*// Item Author
            if(isset($item->created_by))
            {
                $this->db->setQuery("SELECT name FROM #__users WHERE id=".$item->created_by);
                $author = $this->db->loadResult();
                $this->addOpenGraphTag('author', $author, false);
            }*/
        }
    }

    /************* DEFINED ACTIONS CALLS *******************/
    protected function checkActionAfterRoute($action)
    {
        /***************** NEW VOE ******************/
        if (JRequest::getCmd('format') == 'ejax' && JRequest::getCmd('layout') == 'vote' && $action->system_name == 'rate')
        {
            require_once(JPATH_SITE . '/components/com_easyblog/helpers/helper.php');

            $my = JFactory::getUser();
            $config = EasyBlogHelper::getConfig();

            $value = JRequest::getInt('value0'); // Rating
            $uid = JRequest::getInt('value1'); // Blog ID
            $type = JRequest::getCmd('value2'); // should be 'entry'
            $blog = EasyBlogHelper::getTable('Blog', 'Table');
            $blog->load($uid);

            if ($config->get('main_password_protect', true) && !empty($blog->blogpassword))
            {
                return;
            }

            $rating = EasyBlogHelper::getTable('Ratings', 'Table');

            // Do not allow guest to vote, or if the voter already voted.
            if ($rating->fill($my->id, $uid, $type, JFactory::getSession()->getId()) || ($my->id < 1 && !$config->get('main_ratings_guests')))
                return;

            $uri = JURI::getInstance();
            $url = $uri->toString(array('scheme', 'host', 'port'));
            $url = $url . JRoute::_('index.php?option=com_easyblog&view=entry&id=' . $uid, false);
            $this->triggerAction($action, $url);
        }

        /*************** NEW COMMENT ******************/
        if (JRequest::getCmd('format') == 'ejax' && JRequest::getCmd('layout') == 'commentSave' && $action->system_name == 'comment')
        {
            require_once(JPATH_SITE . '/components/com_easyblog/helpers/helper.php');

            // Get the post data
            // From /components/com_easyblog/controller.php
            $data = JRequest::get('POST', JREQUEST_ALLOWHTML);
            $post = array();

            foreach ($data as $key => $value)
            {
                if (JString::substr($key, 0, 5) == 'value')
                {
                    if (is_array($value))
                    {
                        $arrVal = array();
                        foreach ($value as $val)
                        {
                            $item = $val;
                            $item = stripslashes($item);
                            // $item   = rawurldecode($item);
                            $arrVal[] = $item;
                        }
                        $arrVal = EasyBlogStringHelper::ejaxPostToArray($arrVal);
                        $post = $arrVal;
                    } else
                    {
                        $val = stripslashes($value);
                        $val = rawurldecode($val);
                        $post = $val;
                    }
                }
            }

            $config = EasyBlogHelper::getConfig();
            $acl = EasyBlogACLHelper::getRuleSet();

            if (empty($acl->rules->allow_comment))
                return;

            $blogId = $post['id'];

            if (!$config->get('comment_require_email') && !isset($post['esemail']))
            {
                $post['esemail'] = '';
            }

            // Load the EasyBlog view class to run their own validation tests
            require_once(JPATH_SITE.'/components/com_easyblog/views.php');
            require_once(JPATH_SITE.'/components/com_easyblog/views/entry/view.ejax.php');
            // @task: Run some validation tests on the posted values.
            $ebView = new EasyBlogViewEntry();
            if (!$ebView->_validateFields($post))
                return;

            $uri = JURI::getInstance();
            $url = $uri->toString(array('scheme', 'host', 'port'));
            $url = $url . JRoute::_('index.php?option=com_easyblog&view=entry&id=' . $blogId, false);
            $this->triggerAction($action, $url);
        }
    }

    /* Images and Descriptions */
    protected function getEasyBlogMainImage($article)
    {
        $url = '';

        if (isset($article->image))
        {
            $image = json_decode($article->image);
            if (isset($image->url))
            {
                $filePath = str_replace(JURI::root(), '', $image->url);

                jimport('joomla.filesystem.file');
                if (JFile::exists($filePath))
                    $url = $image->url;
            }
        }

        return $url;
    }

    protected function getEasyBlogCategoryImage($catid)
    {
        $url = '';
        $category = $this->getEasyBlogCategory($catid);

        if (isset($category->avatar))
        {
            $image = $category->avatar;
            $imageName = 'images/easyblog_cavatar/' . $image;

            jimport('joomla.filesystem.file');
            if (JFile::exists($imageName))
                $url = JURI::base() . $imageName;
        }
        return $url;
    }

    protected function getCurrentEasyBlogCategoryId()
    {
        $catid = JRequest::getInt('id');
        return $catid;
    }

    protected function getEasyBlogCategory($catid)
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easyblog/tables');
        $category = JTable::getInstance('Category', 'EasyBlogTable');
        $category = null;
        if ($category) // Some users have reported this not coming back. Haven't determined why, but this check should fix.
            $category->load($catid);
        return $category;
    }

}