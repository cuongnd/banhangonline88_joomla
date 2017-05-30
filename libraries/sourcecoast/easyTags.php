<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.0
 * @build-date      2014/08/21
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.utilities');
jimport('sourcecoast.articleContent');

class SCEasyTags
{
    /*
     * Determines if the Easy-Tag can be rendered. If it can, then remove the render key
     */
    static function cannotRenderEasyTag(&$params, $renderKey)
    {
        $hasAKey = false;
        $hasRenderKey = false;
        $foundIndex = -1;

        //Currently there are some indices that are skipped, so count method doesn't
        //quite return what we're expecting. TODO: Fix this to not have skipped index
        end($params);
        $count = key($params) + 1;
        reset($params);

        for($i=0;$i<$count;$i++)
        {
            if(isset($params[$i]))
            {
                $p = $params[$i];
                if (stripos($p, "key=") === 0) //Key starts at position 0
                {
                    $hasAKey = true; //If render key is blank, but any key is set, we should not display it
                    $check = substr($p, 4);
                    if($check == false && $renderKey == '')
                    {
                        //Render key is blank, but a key is not really set, even though key= is present in the tag
                        $hasAKey = false;
                        $foundIndex = $i;
                    }
                    else if ($check == $renderKey)
                    {
                        $hasRenderKey = true;
                        $foundIndex = $i;
                    }
                }
            }
        }
        $canRender = ($renderKey == '' && !$hasAKey) || ($renderKey != '' && $hasRenderKey);

        //Remove the key from the parameter array before it gets rendered
        if($canRender && $foundIndex > -1)
            unset($params[$foundIndex]);

        return (!$canRender);
    }

    static function getTagParameters($params)
    {
        $newFields = array();
        foreach($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $fieldName = strtolower(trim($paramValues[0]));
                    $fieldValue = trim($paramValues[1]);

                    $newFields[$fieldName] = $fieldValue;
                }
            }
        }
        return $newFields;
        //$this->fields->loadArray($newFields);
    }

    static function _splitIntoTagParameters($paramList)
    {
        $paramList = SCStringUtilities::replaceNBSPWithSpace($paramList);
        $params = explode(' ', $paramList);

        $count = count($params);
        for ($i = 0; $i < $count; $i++)
        {
            $params[$i] = str_replace('"', '', $params[$i]);
            if (strpos($params[$i], '=') === false && $i > 0)
            {
                $previousIndex = SCEasyTags::_findPreviousParameter($params, $i - 1);
                //Combine this with previous entry and space
                $combinedParamValue = $params[$previousIndex] . ' ' . $params[$i];
                $params[$previousIndex] = $combinedParamValue;
                unset($params[$i]);
            }
        }
        return $params;
    }

    static function _findPreviousParameter($params, $i)
    {
        for ($index = $i; $index >= 0; $index--)
        {
            if (isset($params[$index]))
                return $index;
        }
        return 0;
    }

    static function canExtendJoomlaForm($selView = 'login', $isConnected = false, $userFormPosition = '1')
    {
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');
        $user = JFactory::getUser();

        if ($option == 'com_users' && $view == $selView)
        {
            if (($view == 'login' && $user->guest) ||
                ($view == 'registration' && $user->guest) ||
                ($view == 'profile' && $isConnected)
            )
            {
                if($userFormPosition == SC_VIEW_TOP || $userFormPosition == SC_VIEW_BOTH)
                {
                    return true;
                }
                if($userFormPosition == SC_VIEW_BOTTOM || $userFormPosition == SC_VIEW_BOTH)
                {
                    return true;
                }
            }
        }

        return false;

    }

    /* Important: Call this method in conjunction with canExtendJoomlaForm to see if the HTML should
    actually be added to the page. Split up like this for performance improvements*/
    static function extendJoomlaUserForms($htmlTag, $userFormPosition = '1')
    {
        $document = JFactory::getDocument();
        $output = $document->getBuffer('component');

        if($userFormPosition == SC_VIEW_TOP || $userFormPosition == SC_VIEW_BOTH)
        {
            $output = $htmlTag . $output;
        }
        if($userFormPosition == SC_VIEW_BOTTOM || $userFormPosition == SC_VIEW_BOTH)
        {
            $output = $output . $htmlTag;
        }

        $document->setBuffer($output, 'component');
    }

    /*
     * Given JFBCLike layout of box_count, button_count or standard, returns
     * the parameters to set the same layout for the other share buttons
     */
    public static function getShareButtonLayout($provider, $layout, $addQuotes='')
    {
        $layoutParams = '';
        if($layout == 'box_count')
        {
            if($provider == 'facebook')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'layout', 'box_count');
            else if($provider == 'google')
            {
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-annotation','bubble');
                $layoutParams .= SCEasyTags::quoteParam($addQuotes, 'data-size','tall');
            }
            else if($provider == 'twitter')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-count', 'vertical');
            else if($provider == 'linkedin')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-counter', 'top');
            else if($provider == 'pinterest')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-pin-config','above');

        }
        else if($layout == 'button_count')
        {
            if($provider == 'facebook')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'layout', 'button_count');
            else if($provider == 'google')
            {
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-annotation','bubble');
                $layoutParams .= SCEasyTags::quoteParam($addQuotes, 'data-size','medium');
            }
            else if($provider == 'twitter')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-count', 'horizontal');
            else if($provider == 'linkedin')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-counter', 'right');
            else if($provider == 'pinterest')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-pin-config','beside');
        }
        else //if($layout == 'standard' || $layout == 'button')
        {
            if($provider == 'facebook')
            {
                if($layout == 'standard')
                {
                    $layoutParams = SCEasyTags::quoteParam($addQuotes, 'layout', 'standard');
                    $layoutParams .= SCEasyTags::quoteParam($addQuotes, 'width', '50');
                    $layoutParams .= SCEasyTags::quoteParam($addQuotes, 'show_faces', 'false');
                }
                else if($layout == 'button')
                    $layoutParams = SCEasyTags::quoteParam($addQuotes, 'layout', 'button');
            }
            else if($provider == 'google')
            {
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-annotation','none');
                $layoutParams .= SCEasyTags::quoteParam($addQuotes, 'data-size','standard');
            }
            else if($provider == 'twitter')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-count', 'none');
            else if($provider == 'linkedin')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-counter', 'no_count');
            else if($provider == 'pinterest')
                $layoutParams = SCEasyTags::quoteParam($addQuotes, 'data-pin-config','none');
        }

        return $layoutParams;
    }

    private static function quoteParam($quote, $option, $value)
    {
        return ' '.$option.'='.$quote.$value.$quote;
    }
}