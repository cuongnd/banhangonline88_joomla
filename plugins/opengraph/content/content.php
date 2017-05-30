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

class plgOpenGraphContent extends OpenGraphPlugin
{
    protected function init()
    {
        $this->extensionName = "Joomla Content";
        $this->supportedComponents[] = 'com_content';

        // Enable this setting if the setOpenGraph function should be called even if an object type hasn't been defined
        // Good for setting title, description, image, etc tags on pages that may not be defined as objects by the admin
        // For example, in content, those are good tags to have (for Like buttons) regardless of if Actions are setup for the page
        $this->setsDefaultTags = true;

        // Define all types of pages this component can create and would be 'objects'
        $this->addSupportedObject("Article", "article");
        $this->addSupportedObject("Category", "category");

        // Add actions that aren't passive (commenting, voting, etc).
        // Things that trigger just by loading the page should not be defined here unless extra logic is required
        // ie. Don't define reading an article
        $this->addSupportedAction("Vote", "vote");
    }

    protected function findObjectType($queryVars)
    {
        // Setup Object type for page
        $view = array_key_exists('view', $queryVars) ? $queryVars['view'] : '';
        $objectTypes = $this->getObjects($view);
        $object = null;
        if ($view == 'article')
        {
            JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models');
            $contentModel = JModelLegacy::getInstance('Article', 'ContentModel');
            $item = $contentModel->getItem((int)$queryVars['id']);
            $catId = $item->catid;
            $object = $this->getBestCategory($objectTypes, $catId);

        }
        else if ($view == 'category')
        {
            JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models');
            $contentModel = JModelLegacy::getInstance('Category', 'ContentModel');
            $category = $contentModel->getCategory();
            $catId = $category->id;
            $object = $this->getBestCategory($objectTypes, $catId);
        }
        return $object;
    }

    private function getBestCategory($objectTypes, $catId)
    {
        $object = null;
        if ($objectTypes)
        {
            $bestDistance = 99999;
            $this->db->setQuery("SELECT lft, rgt FROM #__categories WHERE id = " . $catId);
            $catLoc = $this->db->loadObject();
            foreach ($objectTypes as $type)
            {
                $this->db->setQuery("SELECT lft, rgt FROM #__categories WHERE id = " . $type->params->get('category'));
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

    //TODO - clean up when we remove J1.5 support
    protected function setOpenGraphTags()
    {
        $desc = ''; //Note: meta is same as blank value, since system plugin attempts to generate from metadescription if no value is found
        $image = '';

        $view = JRequest::getCmd('view');

        if ($this->object)
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

        if ($view == 'article')
        {
            $contentModel = JModelLegacy::getInstance('Article', 'ContentModel');
            $article = $contentModel->getItem();
            $this->addOpenGraphTag('title', $article->title, false);

            if ($desc_type == 'custom_desc_introwords')
                $desc = $this->getFirstArticleText($article, $desc_length, SC_INTRO_WORDS);
            else if ($desc_type == 'custom_desc_introchars')
                $desc = $this->getFirstArticleText($article, $desc_length, SC_INTRO_CHARS);
            $this->addOpenGraphTag('description', $desc, false);

            //Note: Always need to try to set an image

            //if ($image_type == 'custom_image_full' || $image == '')
            //{
            $image = $this->getArticleFullImage($article);
            //}
            if ($image_type == 'custom_image_intro' || $image == '')
            {
                $tmpImage = $this->getArticleIntroImage($article);
                if ($tmpImage != '')
                    $image = $tmpImage;
            }
            if ($image_type == 'custom_image_first' || $image == '')
            {
                $tmpImage = $this->getFirstImage($article);
                if ($tmpImage != '')
                    $image = $tmpImage;
            }
            if ($image_type == 'custom_image_category' || $image == '')
            {
                $tmpImage = $this->getImageFromCategory($article);
                if ($tmpImage != '')
                    $image = $tmpImage;
            }
            if (($image_type == 'custom_image_custom' || $image == '') && $image_path != '')
            {
                $image = $image_path;
            }
            $this->addOpenGraphTag('image', $image, false);

            /*// Item Author
            if(isset($article->created_by))
            {
                $this->db->setQuery("SELECT name FROM #__users WHERE id=".$article->created_by);
                $author = $this->db->loadResult();
                $this->addOpenGraphTag('author', $author, false);
            }*/
        }
        else if ($view == 'category')
        {
            $contentModel = JModelLegacy::getInstance('Category', 'ContentModel');
            $category = $contentModel->getCategory();

            if ($desc_type == 'custom_desc_catwords')
                $desc = $this->getFirstCategoryText($category, $desc_length, SC_INTRO_WORDS);
            else if ($desc_type == 'custom_desc_catchars')
                $desc = $this->getFirstCategoryText($category, $desc_length, SC_INTRO_CHARS);
            $this->addOpenGraphTag('description', $desc, false);

            //if ($image_type == 'custom_image_category')
            //{
            $image = $this->getCategoryImage($category->id);
            //}
            if (($image_type == 'custom_image_custom' || $image == '') && $image_path != '')
            {
                $image = $image_path;
            }
            $this->addOpenGraphTag('image', $image, false);
        }
    }


    /************* DEFINED ACTIONS CALLS *******************/
    protected function checkActionAfterRoute($action)
    {
        $canVote = JRequest::getCmd('task') == 'article.vote' && $action->system_name == 'vote';
        if ($canVote)
        {
            $url = JRequest::getVar('url');
            $queryVars = $this->jfbcOgActionModel->getUrlVars($url);
            $user_rating = JRequest::getInt('user_rating', -1);

            if ($user_rating >= 1 && $user_rating <= 5 && $queryVars['id'] > 0)
                $this->triggerAction($action, $url);
        }
    }

    /* Images and Descriptions */
    protected function getArticleIntroImage($article)
    {
        $reg = new JRegistry();
        if (!empty($article->images))
            $reg->loadArray(json_decode($article->images));
        $fullImagePath = $reg->get('image_intro');
        $fullImagePath = $this->getImageLink($fullImagePath);
        return $fullImagePath;
    }

    protected function getArticleFullImage($article)
    {
        $reg = new JRegistry();
        if (!empty($article->images))
            $reg->loadArray(json_decode($article->images));
        $fullImagePath = $reg->get('image_fulltext');
        $fullImagePath = $this->getImageLink($fullImagePath);
        return $fullImagePath;
    }

    protected function getImageFromCategory($article)
    {
        $image = NULL;
        $fullImagePath = '';

        if (isset($article->catid))
        {
            $fullImagePath = $this->getCategoryImage($article->catid);
        }
        return $fullImagePath;
    }

    protected function getCategoryImage($catid)
    {
        jimport('joomla.application.categories');
        $content = JCategories::getInstance('content');
        $category = $content->get($catid);
        $fullImagePath = '';
        if ($category)
        {
            $image = $category->getParams()->get('image');
            $fullImagePath = $this->getImageLink($image);
        }

        return $fullImagePath;
    }

    protected function getBestImage($article)
    {
        $image = $this->getArticleFullImage($article);
        if ($image == '')
        {
            $image = $this->getArticleIntroImage($article);
        }
        if ($image == '')
        {
            $image = $this->getFirstImage($article);
        }
        if ($image == '')
        {
            $image = $this->getImageFromCategory($article);
        }
        return $image;
    }

    protected function getBestText($article)
    {
        return $this->getFirstArticleText($article, 20, SC_INTRO_WORDS);
    }
}