<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('sourcecoast.utilities');
jimport('sourcecoast.articleContent');

class plgContentJFBCContent extends JPlugin
{
    function __construct(& $subject, $config)
    {
        // Don't even register this plugin if JFBCFactory isn't loaded and available (the jfbcsystem plugin likely isn't enabled)
        if (class_exists('JFBCFactory'))
            parent::__construct($subject, $config);
    }

    function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0)
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin())
            return;

        if (strpos($context, 'com_k2') !== 0)
        {
            // Check to only see if we're inside com_content, not tags (or anywhere else)
            if (strpos($context, 'com_content') !== 0)
                return;
            // Make sure we're showing the article from the component, not from a module
            if (!$params || ((get_class($params) == 'JRegistry' || get_class($params) == 'Joomla\\Registry\\Registry') && !$params->exists('article_layout')))
                return;
        }

        //Don't show when printing
        $template = JRequest::getVar('tmpl', '');
        $printing = JRequest::getInt('print', 0);
        if($printing && $template=='component')
        {
            return;
        }

        //Get Social RenderKey
        $jfbcLibrary = JFBCFactory::provider('facebook');
        $renderKey = $jfbcLibrary->getSocialTagRenderKey();
        if ($renderKey)
            $renderKeyString = " key=" . $renderKey;
        else
            $renderKeyString = "";

        $configModel = $jfbcLibrary->getConfigModel();

        $view = JRequest::getVar('view');
        $layout = JRequest::getVar('layout');
        $task = JRequest::getVar('task');
        $isArticleView = $this->isArticleView($view);

        if ($view == 'item' || $view == 'itemlist' || $view == 'latest') //K2
        {
            $showK2Comments = $this->showSocialItemInK2Item($article,
                $configModel->getSetting('social_k2_comment_item_include_ids'),
                $configModel->getSetting('social_k2_comment_item_exclude_ids'),
                $configModel->getSetting('social_k2_comment_cat_include_type'),
                $configModel->getSetting('social_k2_comment_cat_ids'));

            $showK2Like = $this->showSocialItemInK2Item($article,
                $configModel->getSetting('social_k2_like_item_include_ids'),
                $configModel->getSetting('social_k2_like_item_exclude_ids'),
                $configModel->getSetting('social_k2_like_cat_include_type'),
                $configModel->getSetting('social_k2_like_cat_ids'));

            $showK2CommentsInViewPosition = $this->getSocialK2ItemViewPosition($article, $view, $layout, $task,
                $configModel->getSetting('social_k2_comment_item_view'),
                $configModel->getSetting('social_k2_comment_tag_view'),
                $configModel->getSetting('social_k2_comment_category_view'),
                $configModel->getSetting('social_k2_comment_userpage_view'),
                $configModel->getSetting('social_k2_comment_latest_view')
            );

            $showK2LikeInViewPosition = $this->getSocialK2ItemViewPosition($article, $view, $layout, $task,
                $configModel->getSetting('social_k2_like_item_view'),
                $configModel->getSetting('social_k2_like_tag_view'),
                $configModel->getSetting('social_k2_like_category_view'),
                $configModel->getSetting('social_k2_like_userpage_view'),
                $configModel->getSetting('social_k2_like_latest_view')
            );
            if ($showK2Like == true && $showK2LikeInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Item View
                    $likeText = $this->_getK2ItemLike($article, $configModel, $renderKeyString);
                else //Blog View
                    $likeText = $this->_getK2BlogLike($article, $configModel, $renderKeyString);

                $this->addTextToArticle($article, $likeText, $showK2LikeInViewPosition);
            }
            if ($showK2Comments == true && $showK2CommentsInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Item Text
                    $commentText = $this->_getK2ItemComments($article, $configModel, $renderKeyString);
                else
                    $commentText = $this->_getK2BlogComments($article, $configModel, $renderKeyString);

                $this->addTextToArticle($article, $commentText, $showK2CommentsInViewPosition);
            }
        } else
        {
            $showComments = $this->showSocialItemInArticle($article,
                $configModel->getSetting('social_comment_article_include_ids'),
                $configModel->getSetting('social_comment_article_exclude_ids'),
                $configModel->getSetting('social_comment_cat_include_type'),
                $configModel->getSetting('social_comment_cat_ids'),
                $configModel->getSetting('social_comment_sect_include_type'),
                $configModel->getSetting('social_comment_sect_ids'));

            $showLike = $this->showSocialItemInArticle($article,
                $configModel->getSetting('social_like_article_include_ids'),
                $configModel->getSetting('social_like_article_exclude_ids'),
                $configModel->getSetting('social_like_cat_include_type'),
                $configModel->getSetting('social_like_cat_ids'),
                $configModel->getSetting('social_like_sect_include_type'),
                $configModel->getSetting('social_like_sect_ids'));

            $showCommentsInViewPosition = $this->getSocialItemViewPosition($article, $view,
                $configModel->getSetting('social_comment_article_view'),
                $configModel->getSetting('social_comment_frontpage_view'),
                $configModel->getSetting('social_comment_category_view'),
                $configModel->getSetting('social_comment_section_view'));

            $showLikeInViewPosition = $this->getSocialItemViewPosition($article, $view,
                $configModel->getSetting('social_like_article_view'),
                $configModel->getSetting('social_like_frontpage_view'),
                $configModel->getSetting('social_like_category_view'),
                $configModel->getSetting('social_like_section_view'));

            if ($showLike == true && $showLikeInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Article Text
                    $likeText = $this->_getJoomlaArticleLike($article, $configModel, $renderKeyString);
                else //Blog Text
                    $likeText = $this->_getJoomlaBlogLike($article, $configModel, $renderKeyString);

                $this->addTextToArticle($article, $likeText, $showLikeInViewPosition);
            }
            if ($showComments == true && $showCommentsInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Article Text
                    $commentText = $this->_getJoomlaArticleComments($article, $configModel, $renderKeyString);
                else //Blog Text
                    $commentText = $this->_getJoomlaBlogComments($article, $configModel, $renderKeyString);

                $this->addTextToArticle($article, $commentText, $showCommentsInViewPosition);
            }
        }
    }

    function _getJoomlaArticleLike($article, $configModel, $renderKeyString)
    {
        $buttonStyle = $configModel->getSetting('social_article_like_layout_style');
        $showFaces = $configModel->getSetting('social_article_like_show_faces');
        $showShareButton = $configModel->getSetting('social_article_like_show_send_button');
        $width = $configModel->getSetting('social_article_like_width');
        $verbToDisplay = $configModel->getSetting('social_article_like_verb_to_display');
        $font = $configModel->getSetting('social_article_like_font');
        $colorScheme = $configModel->getSetting('social_article_like_color_scheme');
        $showFacebook = $configModel->getSetting('social_article_like_show_facebook');
        $showLinkedIn = $configModel->getSetting('social_article_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_article_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_article_like_show_googleplus');
        $showPinterest = $configModel->getSetting('social_article_like_show_pinterest');
        $pinImage = $this->getPinterestImage($article);
        $pinText = $this->getPinterestText($article);

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFacebook, $showFaces, $showShareButton, $showLinkedIn, $showTwitter, $showGooglePlus, $showPinterest, $width, $verbToDisplay, $font, $colorScheme, $pinImage, $pinText, $renderKeyString, true);
        return $likeText;
    }

    function _getJoomlaBlogLike($article, $configModel, $renderKeyString)
    {
        $buttonStyle = $configModel->getSetting('social_blog_like_layout_style');
        $showFaces = $configModel->getSetting('social_blog_like_show_faces');
        $showShareButton = $configModel->getSetting('social_blog_like_show_send_button');
        $width = $configModel->getSetting('social_blog_like_width');
        $verbToDisplay = $configModel->getSetting('social_blog_like_verb_to_display');
        $font = $configModel->getSetting('social_blog_like_font');
        $colorScheme = $configModel->getSetting('social_blog_like_color_scheme');
        $showFacebook = $configModel->getSetting('social_blog_like_show_facebook');
        $showLinkedIn = $configModel->getSetting('social_blog_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_blog_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_blog_like_show_googleplus');
        $showPinterest = $configModel->getSetting('social_blog_like_show_pinterest');
        $pinImage = $this->getPinterestImage($article);
        $pinText = $this->getPinterestText($article);

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFacebook, $showFaces, $showShareButton, $showLinkedIn, $showTwitter, $showGooglePlus, $showPinterest, $width, $verbToDisplay, $font, $colorScheme, $pinImage, $pinText, $renderKeyString, true);
        return $likeText;
    }

    function _getK2ItemLike($article, $configModel, $renderKeyString)
    {
        $buttonStyle = $configModel->getSetting('social_k2_item_like_layout_style');
        $showFaces = $configModel->getSetting('social_k2_item_like_show_faces');
        $showShareButton = $configModel->getSetting('social_k2_item_like_show_send_button');
        $width = $configModel->getSetting('social_k2_item_like_width');
        $verbToDisplay = $configModel->getSetting('social_k2_item_like_verb_to_display');
        $font = $configModel->getSetting('social_k2_item_like_font');
        $colorScheme = $configModel->getSetting('social_k2_item_like_color_scheme');
        $showFacebook = $configModel->getSetting('social_k2_item_like_show_facebook');
        $showLinkedIn = $configModel->getSetting('social_k2_item_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_k2_item_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_k2_item_like_show_googleplus');
        $showPinterest = $configModel->getSetting('social_k2_item_like_show_pinterest');
        $pinImage = $this->getPinterestImage($article);
        $pinText = $this->getPinterestText($article);

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFacebook, $showFaces, $showShareButton, $showLinkedIn, $showTwitter, $showGooglePlus, $showPinterest, $width, $verbToDisplay, $font, $colorScheme, $pinImage, $pinText, $renderKeyString, false);
        return $likeText;
    }

    function _getK2BlogLike($article, $configModel, $renderKeyString)
    {
        $buttonStyle = $configModel->getSetting('social_k2_blog_like_layout_style');
        $showFaces = $configModel->getSetting('social_k2_blog_like_show_faces');
        $showShareButton = $configModel->getSetting('social_k2_blog_like_show_send_button');
        $width = $configModel->getSetting('social_k2_blog_like_width');
        $verbToDisplay = $configModel->getSetting('social_k2_blog_like_verb_to_display');
        $font = $configModel->getSetting('social_k2_blog_like_font');
        $colorScheme = $configModel->getSetting('social_k2_blog_like_color_scheme');
        $showFacebook = $configModel->getSetting('social_k2_blog_like_show_facebook');
        $showLinkedIn = $configModel->getSetting('social_k2_blog_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_k2_blog_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_k2_blog_like_show_googleplus');
        $showPinterest = $configModel->getSetting('social_k2_blog_like_show_pinterest');
        $pinImage = $this->getPinterestImage($article);
        $pinText = $this->getPinterestText($article);

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFacebook, $showFaces, $showShareButton, $showLinkedIn, $showTwitter, $showGooglePlus, $showPinterest, $width, $verbToDisplay, $font, $colorScheme, $pinImage, $pinText, $renderKeyString, false);
        return $likeText;
    }

    function _getLikeButton($article, $buttonStyle, $showFacebook, $showFaces, $showShareButton, $showLinkedInButton, $showTwitterButton, $showGooglePlusButton, $showPinterestButton, $width, $verbToDisplay, $font, $colorScheme, $pinImage, $pinText, $renderKeyString, $isJoomla)
    {
        $url = SCArticleContent::getCurrentURL($article, $isJoomla);

        //Only set width for standard layout, not box_count or button_count
        if ($buttonStyle == 'standard')
            $widthField = ' width=' . $width;
        else
            $widthField = '';

        if($showFacebook)
            $likeText = '{JFBCLike layout=' . $buttonStyle . ' show_faces=' . $showFaces . ' share=' . $showShareButton
                . $widthField . ' action=' . $verbToDisplay . ' font=' . $font
                . ' colorscheme=' . $colorScheme . ' href=' . $url . $renderKeyString . '}';
        else
            $likeText = '';

        $buttonText = '<div style="position: relative; top:0px; left:0px; z-index: 99;" class="scsocialbuttons '.$buttonStyle.'">';
        if ($showLinkedInButton || $showTwitterButton || $showGooglePlusButton || $showPinterestButton)
        {
            $extraButtonText = SCSocialUtilities::getExtraShareButtons($url, $buttonStyle, false, false, $showTwitterButton, $showGooglePlusButton, $renderKeyString, $showLinkedInButton, '50', $showPinterestButton, $pinImage, $pinText);
            $buttonText .= $extraButtonText;
        }
        $buttonText .= $likeText;
        $buttonText .= '</div><div style="clear:left"></div>';
        $likeText = $buttonText;

        return $likeText;
    }

    function _getJoomlaArticleComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_article_comment_width');
        $numposts = $configModel->getSetting('social_article_comment_max_num');
        $colorscheme = $configModel->getSetting('social_article_comment_color_scheme');
        $orderBy = $configModel->getSetting('social_article_comment_order_by');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $orderBy, $renderKeyString, true);
        return $commentText;
    }

    function _getJoomlaBlogComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_blog_comment_width');
        $numposts = $configModel->getSetting('social_blog_comment_max_num');
        $colorscheme = $configModel->getSetting('social_blog_comment_color_scheme');
        $orderBy = $configModel->getSetting('social_blog_comment_order_by');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $orderBy, $renderKeyString, true);
        return $commentText;
    }

    function _getK2ItemComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_k2_item_comment_width');
        $numposts = $configModel->getSetting('social_k2_item_comment_max_num');
        $colorscheme = $configModel->getSetting('social_k2_item_comment_color_scheme');
        $orderBy = $configModel->getSetting('social_k2_item_comment_order_by');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $orderBy, $renderKeyString, false);
        return $commentText;
    }

    function _getK2BlogComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_k2_blog_comment_width');
        $numposts = $configModel->getSetting('social_k2_blog_comment_max_num');
        $colorscheme = $configModel->getSetting('social_k2_blog_comment_color_scheme');
        $orderBy = $configModel->getSetting('social_k2_blog_comment_order_by');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $orderBy, $renderKeyString, false);
        return $commentText;
    }

    function _getComments($article, $width, $numposts, $colorscheme, $orderBy, $renderKeyString, $isJoomla)
    {
        $href = SCArticleContent::getCurrentURL($article, $isJoomla);

        if (!$numposts || $numposts == '0')
        {
            $commentText = '{JFBCCommentsCount href=' . $href . $renderKeyString . '}';
        } else
        {
            $commentText = '{JFBCComments href=' . $href . ' width=' . $width . ' num_posts=' . $numposts
                . ' colorscheme=' . $colorscheme . ' order_by=' . $orderBy . $renderKeyString . '}';
        }

        $buttonText = '<div style="z-index: 98;" class="scsocialcomments">' . $commentText . '</div>';
        return $buttonText;
    }

    /* Methods to get / set data in article text */

    private function getPinterestImage($article)
    {
        JPluginHelper::importPlugin('opengraph');
        $app = JFactory::getApplication();
        $args = array($article);
        $images = $app->triggerEvent('onOpenGraphGetBestImage', $args);
        return current(array_filter($images)); // return first non-null image in the array
    }

    private function getPinterestText($article)
    {
        JPluginHelper::importPlugin('opengraph');
        $app = JFactory::getApplication();
        $args = array($article);
        $text = $app->triggerEvent('onOpenGraphGetBestText', $args);
        return current(array_filter($text)); // return first non-null text in the array
    }

    private function getSocialItemViewPosition($article, $view, $showInArticleView, $showInFrontpageView, $showInCategoryView, $showInSectionView)
    {
        $returnValue = "0";
        if ($view == 'article' && $article->id != null)
            $returnValue = $showInArticleView;
        else if ($view == 'frontpage' || $view == 'featured')
            $returnValue = $showInFrontpageView;
        else if ($view == 'category' && $article->catid != null)
            $returnValue = $showInCategoryView;

        return $returnValue;
    }

    private function getSocialK2ItemViewPosition($article, $view, $layout, $task, $showInItemView, $showInTagView, $showInCategoryView, $showInUserpageView, $showInLatestView)
    {
        $returnValue = "0";
        if ($view == 'item' && $article->id != null)
            $returnValue = $showInItemView;
        else if ($view == 'itemlist')
        {
            if ($this->_isK2Layout($layout, $task, 'category')
                || $this->_isK2Layout($layout, $task, 'search')
                || $this->_isK2Layout($layout, $task, 'date')
            )
                $returnValue = $showInCategoryView;
            else if ($this->_isK2Layout($layout, $task, 'generic') || $this->_isK2Layout($layout, $task, 'tag'))
                $returnValue = $showInTagView;
            else if ($this->_isK2Layout($layout, $task, 'user') && JRequest::getInt('id', 0))
                $returnValue = $showInUserpageView;
        } else if ($view == 'latest')
            $returnValue = $showInLatestView;
        return $returnValue;
    }

    private function _isK2Layout($layout, $task, $targetLayout)
    {
        return ($layout == $targetLayout || $task == $targetLayout);
    }

    private function showSocialItemInArticle($article, $articleIncludeIds, $articleExcludeIds, $catIncludeType, $catIds, $sectIncludeType, $sectIds)
    {
        //Show in Article
        $includeArticles = explode(",", $articleIncludeIds);
        $excludeArticles = explode(",", $articleExcludeIds);

        //Specific Article is included or excluded, then show or don't show it.
        if ($includeArticles != null && in_array($article->id, $includeArticles))
            return true;
        else if ($excludeArticles != null && in_array($article->id, $excludeArticles))
            return false;

        //Show in Category
        $categories = unserialize($catIds);
        $inCategoryArray = $categories != null && in_array($article->catid, $categories);

        if ($catIncludeType == SC_TYPE_INCLUDE)
        {
            if ($inCategoryArray)
                return true;
            else
                return false;
        } else if ($catIncludeType == SC_TYPE_EXCLUDE)
        {
            if ($inCategoryArray)
                return false;
            else
                return true;
        }

        return true;
    }

    private function showSocialItemInK2Item($article, $articleIncludeIds, $articleExcludeIds, $catIncludeType, $catIds)
    {
        //Show in Article
        $includeArticles = explode(",", $articleIncludeIds);
        $excludeArticles = explode(",", $articleExcludeIds);

        //Specific Article is included or excluded, then show or don't show it.
        if ($includeArticles != null && in_array($article->id, $includeArticles))
            return true;
        else if ($excludeArticles != null && in_array($article->id, $excludeArticles))
            return false;

        //Show in Category
        $categories = unserialize($catIds);
        $inCategoryArray = $categories != null && in_array($article->catid, $categories);

        if ($catIncludeType == SC_TYPE_INCLUDE)
        {
            if ($inCategoryArray)
                return true;
            else
                return false;
        } else if ($catIncludeType == SC_TYPE_EXCLUDE)
        {
            if ($inCategoryArray)
                return false;
            else
                return true;
        }

        return true;

    }

    private function isArticleView($view)
    {
        return ($view == 'article' || $view == 'item');
    }

    private function _prependToIntrotext(& $article, $fbText)
    {
        if (isset($article->text))
            $article->text = $fbText . $article->text;
        if (isset($article->introtext))
            $article->introtext = $fbText . $article->introtext;
    }

    private function _prependToFulltext(& $article, $fbText)
    {
        if (isset($article->text))
            $this->_prependAfterSplitter($article->text, $fbText);
        if (isset($article->fulltext))
            $this->_prependAfterSplitter($article->fulltext, $fbText);
    }

    private function _appendToFulltext(& $article, $fbText)
    {
        if (isset($article->text))
            $article->text = $article->text . $fbText;
        else if (isset($article->fulltext))
            $article->fulltext = $article->fulltext . $fbText;
    }

    private function _prependAfterSplitter(& $text, $fbText)
    {
        $articleText = str_replace('{K2Splitter}', '', $text, $count);
        $text = $fbText . $articleText;
        if ($count)
            $text = '{K2Splitter}' . $text;
    }

    private function _appendBeforeSplitter(& $text, $fbText)
    {
        $articleText = str_replace('{K2Splitter}', '', $text, $count);
        $text = $articleText . $fbText;
        if ($count)
            $text .= '{K2Splitter}';
    }

    private function addTextToBottom($article, $bottomText, $hasFullText)
    {
        $view = JRequest::getVar('view');
        //$layout = JRequest::getVar('layout');
        //$task = JRequest::getVar('task');

        if ($hasFullText && $view != 'category' && $view != 'featured')
        {
            //If fulltext is present, it means there's already something after fulltext, so safe to
            //just add at the bottom of text.
            $this->_appendToFulltext($article, $bottomText);
        }
        else
        {
            //If full text is not present, then we must add the bottom portion before K2Splitter
            if (isset($article->text))
                $this->_appendBeforeSplitter($article->text, $bottomText);
            if (isset($article->introtext))
                $this->_appendBeforeSplitter($article->introtext, $bottomText);
        }
    }

    private function addClassToFBText($fbText, $className)
    {
        $newFbText = str_replace('scsocialbuttons', 'scsocialbuttons ' . $className, $fbText);
        return $newFbText;
    }

    private function addTextToArticle(& $article, $fbText, $showTextPosition)
    {
        $hasFullText = isset($article->fulltext) && $article->fulltext != "";

        $introtextStartsWithSplitter = isset($article->introtext) && strpos($article->introtext, '{K2Splitter}') === 0;
        $textStartsWithSplitter = isset($article->text) && strpos($article->text, '{K2Splitter}') === 0;

        $hasIntroText = isset($article->introtext) && $article->introtext != "";
        if ($textStartsWithSplitter || $introtextStartsWithSplitter)
            $hasIntroText = false;

        $topText = $this->addClassToFBText($fbText, "top");
        $bottomText = $this->addClassToFBText($fbText, "bottom");

        if ($showTextPosition == SC_VIEW_TOP)
        {
            if (!$hasIntroText && $hasFullText)
            {
                if (isset($article->text))
                    $this->_prependAfterSplitter($article->text, $topText);
                if (isset($article->fulltext))
                    $this->_prependAfterSplitter($article->fulltext, $topText);
            }
            else
            {
                $this->_prependToIntrotext($article, $topText);
            }
        }
        else if ($showTextPosition == SC_VIEW_BOTH)
        {
            //If introtext is present, we have to be careful of where to put the bottom item, because of K2Splitter
            if ($hasIntroText)
            {
                $this->_prependToIntrotext($article, $topText);
                $this->addTextToBottom($article, $bottomText, $hasFullText);
            }
            else if ($hasFullText)
            {
                //If fulltext is present, 1it means there's already something after fulltext, so safe to
                //just add at the bottom of text.
                $this->_prependToFulltext($article, $topText);
                $this->_appendToFulltext($article, $bottomText);
            }
        }
        else if ($showTextPosition == SC_VIEW_BOTTOM)
        {
            $this->addTextToBottom($article, $bottomText, $hasFullText);
        }

//        echo 'FULL:'.$article->fulltext.'<br/>';
//        echo 'INTRO:'.$article->introtext.'<br/>';
//        echo 'ALL:'.$article->text.'<br/><br/><br/>';
    }
}
